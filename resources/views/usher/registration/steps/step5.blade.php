@extends('layouts.app')

@section('title', 'Registration - Step 5: Check-in')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Sticky Header -->
    <div class="sticky top-0 z-10 bg-white pb-4 mb-6 text-center">
        <h1 class="text-2xl md:text-4xl font-bold text-[#041E42]">Registration</h1>
        <p class="text-gray-600 mt-2 md:text-lg">Step 5: Check-in Decision</p>
    </div>
    
    <!-- Stepper Progress -->
    <div class="mb-10">
        <div class="max-w-4xl mx-auto">
            <div class="flex justify-between text-sm md:text-base">
                @php
                    $steps = ['Category', 'Details', 'Attendance', 'Ticket', 'Check-in'];
                @endphp
                @foreach($steps as $index => $step)
                    <div class="flex flex-col items-center {{ $index === 4 ? 'text-[#041E42] font-bold' : 'text-gray-400' }}">
                        <div class="w-7 h-7 md:w-8 md:h-8 rounded-full flex items-center justify-center 
                                    {{ $index === 4 ? 'bg-[#041E42] text-white' : ($index < 4 ? 'bg-[#041E42] text-white' : 'bg-gray-300 text-white') }}">
                            {{ $index + 1 }}
                        </div>
                        <span class="mt-1 text-xs md:text-sm">{{ $step }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Form Content -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200 max-w-3xl mx-auto">
        <div class="p-6">
            <form action="{{ route('usher.registration.process_step5') }}" method="POST">
                @csrf
                
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-bold text-gray-700 mb-4">Check-in Decision</h3>
                        <p class="text-gray-600 mb-6">Would you like to check in this participant for today?</p>
                    </div>
                    
                    @if(!isset($today) || !$today)
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4 rounded">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700">
                                        There is no active conference day today. The participant will be registered but cannot be checked in today.
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <input type="hidden" name="check_in_today" value="0">
                    @elseif(!$canCheckInToday)
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4 rounded">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700">
                                        Today ({{ \Carbon\Carbon::parse($today->date)->format('F j, Y') }}) is not one of the selected attendance days for this participant. They will be registered but cannot be checked in today.
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <input type="hidden" name="check_in_today" value="0">
                    @else
                        <div class="mt-4 space-y-6">
                            <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-green-700">
                                            Today ({{ \Carbon\Carbon::parse($today->date)->format('F j, Y') }}) is one of the selected attendance days. You can check in this participant now.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <fieldset class="space-y-4" aria-label="Check-in options">
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input
                                            id="check_in_yes"
                                            name="check_in_today"
                                            type="radio"
                                            value="1"
                                            checked
                                            class="h-5 w-5 text-[#041E42] focus:ring-[#041E42]"
                                        >
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="check_in_yes" class="font-medium text-gray-800 cursor-pointer">Yes, check in the participant now</label>
                                        <p class="text-gray-600">The participant will be registered and checked in for today.</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input
                                            id="check_in_no"
                                            name="check_in_today"
                                            type="radio"
                                            value="0"
                                            class="h-5 w-5 text-[#041E42] focus:ring-[#041E42]"
                                        >
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="check_in_no" class="font-medium text-gray-800 cursor-pointer">No, just register without checking in</label>
                                        <p class="text-gray-600">The participant will only be registered. You can check them in later.</p>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    @endif
                </div>
                
                <div class="mt-8 pt-5 border-t border-gray-200">
                    <div class="flex flex-col sm:flex-row justify-between gap-4 sm:gap-0">
                        <a href="{{ route('usher.registration.step3') }}" 
                        class="inline-flex items-center justify-center py-2 px-5 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#041E42]"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 -ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            Back to Attendance Days
                        </a>

                        <button type="submit" 
                                class="inline-flex items-center justify-center py-2 px-5 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-[#041E42] hover:bg-[#02305e] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#041E42]"
                        >
                            Complete Registration
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2 -mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
