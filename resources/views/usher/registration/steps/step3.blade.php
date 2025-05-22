@extends('layouts.app')

@section('title', 'Registration - Step 3: Attendance Days')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-[#041E42]">Registration</h1>
                <p class="text-gray-600 mt-1">Step 3: Select Attendance Days</p>
            </div>
        </div>
    </div>

    <!-- Steps Progress -->
    <div class="mb-8">
        <div class="max-w-4xl mx-auto">
            <div class="relative">
                <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-gray-200">
                    <div class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-[#041E42] w-3/5"></div>
                </div>
                <div class="flex text-xs text-gray-600 mt-1 justify-between">
                    <div>Category</div>
                    <div>Details</div>
                    <div class="text-[#041E42] font-semibold">Attendance</div>
                    <div>Ticket</div>
                    <div>Check-in</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Content -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
        <div class="p-6">
            <form action="{{ route('usher.registration.process_step3') }}" method="POST">
                @csrf
                
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-bold text-gray-700 mb-4">Conference Attendance</h3>
                        <p class="text-gray-600 mb-4">Select the days this participant will attend</p>
                        
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
                                <div class="border rounded-lg p-4 relative overflow-hidden">
                                    <label class="flex items-start cursor-pointer">
                                        <div class="flex items-center h-5">
                                            <input
                                                type="checkbox"
                                                name="attendance_days[]"
                                                value="{{ $day->id }}"
                                                class="h-5 w-5 text-[#041E42] focus:ring-[#041E42] rounded"
                                                {{ (old('attendance_days') && in_array($day->id, old('attendance_days'))) || 
                                                   (isset($data['attendance_days']) && in_array($day->id, $data['attendance_days'])) ? 'checked' : '' }}
                                            >
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <span class="block text-gray-900 font-medium">
                                                {{ \Carbon\Carbon::parse($day->date)->format('l, F j, Y') }}
                                                @if($day->isToday())
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 ml-2">
                                                        Today
                                                    </span>
                                                @endif
                                            </span>
                                            <span class="block text-gray-500 mt-1">{{ $day->description }}</span>
                                            <span class="text-sm text-gray-600 mt-1">
                                                Location: {{ $day->location ?: 'TBD' }}
                                            </span>
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
                    <div class="flex justify-between">
                        <a href="{{ route('usher.registration.step2') }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 -ml-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            Back to Details
                        </a>
                        <button 
                            type="submit" 
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-[#041E42] hover:bg-[#0A2E5C] focus:outline-none"
                            {{ $conferenceDays->isEmpty() ? 'disabled' : '' }}
                        >
                            Continue to Ticket Preview
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2 -mr-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 