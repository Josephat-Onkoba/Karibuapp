@extends('layouts.app')

@section('title', 'Registration - Step 3: Attendance Days')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Sticky Header -->
    <div class="sticky top-0 z-10 bg-white pb-4 mb-6 text-center">
        <h1 class="text-2xl md:text-4xl font-bold text-[#041E42]">Registration</h1>
        <p class="text-gray-600 mt-2 md:text-lg">Step 3: Select Attendance Days</p>
    </div>

    <!-- Stepper Progress -->
    <div class="mb-10">
        <div class="max-w-4xl mx-auto">
            <div class="flex justify-between text-sm md:text-base">
                @php
                    $steps = ['Category', 'Details', 'Attendance', 'Ticket', 'Check-in'];
                @endphp
                @foreach($steps as $index => $step)
                    <div class="flex flex-col items-center {{ $index === 2 ? 'text-[#041E42] font-bold' : 'text-gray-400' }}">
                        <div class="w-7 h-7 md:w-8 md:h-8 rounded-full flex items-center justify-center 
                                    {{ $index === 2 ? 'bg-[#041E42] text-white' : ($index < 2 ? 'bg-[#041E42] text-white' : 'bg-gray-300 text-white') }}">
                            {{ $index + 1 }}
                        </div>
                        <span class="mt-1 text-xs md:text-sm">{{ $step }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Form Content -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
        <div class="p-6">
            <form action="{{ route('usher.registration.process_step3') }}" 
                  method="POST" 
                  id="attendance-form"
                  data-category="{{ $data['category'] }}"
                  data-payment-status="{{ $data['payment_status'] ?? '' }}"
                  data-eligible-days="{{ $data['eligible_days'] ?? '' }}">
                @csrf
                
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-bold text-gray-700 mb-4">Conference Attendance</h3>
                        <p class="text-gray-600 mb-4">Select the days this participant will attend</p>

                        @if($data['category'] === 'general' && $data['payment_status'] !== 'Waived')
                        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-blue-700">
                                        This participant has paid for {{ $data['eligible_days'] }} {{ Str::plural('day', $data['eligible_days']) }}.
                                        Please select exactly {{ $data['eligible_days'] }} {{ Str::plural('day', $data['eligible_days']) }}.
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        @if($conferenceDays->isEmpty())
                            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-yellow-700">
                                            No active conference days found. Please contact an administrator.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="mt-4 space-y-4">
                                @foreach($conferenceDays as $day)
                                @php
                                    $isPastDay = $day->date->isPast() && !$day->date->isToday();
                                @endphp
                                <div class="border rounded-lg p-4 relative overflow-hidden {{ $isPastDay ? 'bg-gray-50' : '' }}">
                                    <label class="flex items-start {{ $isPastDay ? 'cursor-not-allowed' : 'cursor-pointer' }}">
                                        <div class="flex items-center h-5">
                                            <input
                                                type="checkbox"
                                                name="attendance_days[]"
                                                value="{{ $day->id }}"
                                                class="attendance-checkbox h-5 w-5 text-[#041E42] focus:ring-[#041E42] rounded {{ $isPastDay ? 'opacity-50' : '' }}"
                                                {{ (old('attendance_days') && in_array($day->id, old('attendance_days'))) || 
                                                   (isset($data['attendance_days']) && in_array($day->id, $data['attendance_days'])) ? 'checked' : '' }}
                                                {{ $isPastDay ? 'disabled' : '' }}
                                                data-day-id="{{ $day->id }}"
                                            >
                                        </div>
                                        <div class="ml-3 flex-grow">
                                            <div class="flex items-center justify-between">
                                                <span class="block text-gray-900 font-medium {{ $isPastDay ? 'text-gray-500' : '' }}">
                                                {{ \Carbon\Carbon::parse($day->date)->format('l, F j, Y') }}
                                                </span>
                                                <div class="flex items-center space-x-2">
                                                @if($day->isToday())
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        Today
                                                    </span>
                                                    @elseif($isPastDay)
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                            Past
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                            Upcoming
                                                        </span>
                                                @endif
                                                </div>
                                            </div>
                                            <span class="block text-gray-500 mt-1 {{ $isPastDay ? 'text-gray-400' : '' }}">{{ $day->description }}</span>
                                            <span class="text-sm text-gray-600 mt-1 {{ $isPastDay ? 'text-gray-400' : '' }}">
                                                Location: {{ $day->location ?: 'TBD' }}
                                            </span>
                                            @if($isPastDay)
                                                <div class="mt-2 text-sm text-gray-500">
                                                    <span class="text-yellow-600">
                                                        <svg class="inline-block h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                        </svg>
                                                        This day has passed and cannot be selected
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    </label>
                                </div>
                                @endforeach
                            </div>
                            
                            @error('attendance_days')
                            <div class="text-red-500 mt-2 text-sm">{{ $message }}</div>
                            @enderror
                        @endif
                    </div>
                </div>
                
                <div class="mt-8 pt-5 border-t border-gray-200">
                <div class="flex flex-col sm:flex-row justify-between gap-4 sm:gap-0">
                    <a href="{{ route('usher.registration.step2') }}"
                    class="inline-flex items-center justify-center py-2 px-5 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 -ml-1" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        Back to Details
                    </a>

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center py-2 px-5 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-[#041E42] hover:bg-[#0A2E5C] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#041E42] disabled:opacity-50 disabled:cursor-not-allowed"
                        {{ $conferenceDays->isEmpty() ? 'disabled' : '' }}
                    >
                        Continue to Ticket Preview
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2 -mr-1" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>

                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('attendance-form');
    const category = form.dataset.category;
    const paymentStatus = form.dataset.paymentStatus;
    const eligibleDays = parseInt(form.dataset.eligibleDays) || null;
    const checkboxes = document.querySelectorAll('.attendance-checkbox');
    
    function updateCheckboxes() {
        if (category === 'general' && paymentStatus !== 'Waived') {
            const checkedBoxes = document.querySelectorAll('.attendance-checkbox:checked').length;
            
            checkboxes.forEach(checkbox => {
                if (!checkbox.checked && checkedBoxes >= eligibleDays) {
                    checkbox.disabled = true;
                    checkbox.closest('.border').classList.add('opacity-50');
                } else if (!checkbox.disabled) {
                    checkbox.disabled = false;
                    checkbox.closest('.border').classList.remove('opacity-50');
                }
            });
        }
    }
    
    if (category === 'general' && paymentStatus !== 'Waived') {
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateCheckboxes);
        });
        
        // Initial update
        updateCheckboxes();
        
        // Form validation
        form.addEventListener('submit', function(event) {
            const checkedBoxes = document.querySelectorAll('.attendance-checkbox:checked').length;
            if (checkedBoxes !== eligibleDays) {
                event.preventDefault();
                alert(`Please select exactly ${eligibleDays} ${eligibleDays === 1 ? 'day' : 'days'} for this participant.`);
            }
        });
    }
});
</script>
@endpush
@endsection 