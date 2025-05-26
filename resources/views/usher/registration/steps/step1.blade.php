@extends('layouts.app')

@section('title', 'Registration - Step 1: Category Selection')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Sticky Header -->
    <div class="sticky top-0 z-10 bg-white pb-4 mb-6 text-center">
        <h1 class="text-2xl md:text-4xl font-bold text-[#041E42]">Registration</h1>
        <p class="text-gray-600 mt-2 md:text-lg">Step 1: Select Participant Category</p>
    </div>


    <!-- Stepper Progress -->
    <div class="mb-10">
        <div class="max-w-4xl mx-auto">
            <div class="flex justify-between text-sm md:text-base">
                @php
                    $steps = ['Category', 'Details', 'Attendance', 'Ticket', 'Check-in'];
                @endphp
                @foreach($steps as $index => $step)
                    <div class="flex flex-col items-center {{ $index === 0 ? 'text-[#041E42] font-bold' : 'text-gray-400' }}">
                        <div class="w-7 h-7 md:w-8 md:h-8 rounded-full flex items-center justify-center 
                                    {{ $index === 0 ? 'bg-[#041E42] text-white' : 'bg-gray-300 text-white' }}">
                            {{ $index + 1 }}
                        </div>
                        <span class="mt-1 text-xs md:text-sm">{{ $step }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Form Section -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
        <div class="p-6">
            <form action="{{ route('usher.registration.process_step1') }}" method="POST">
                @csrf

                <div class="mb-8">
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Select Participant Category</h3>
                    <p class="text-gray-600 mb-6">Choose the appropriate category for this participant</p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($categories as $key => $category)
                        <div class="border rounded-lg p-5 hover:border-[#041E42] hover:shadow-lg hover:scale-[1.02] transition-all duration-200 cursor-pointer">
                            <label class="flex items-start gap-3 cursor-pointer">
                                <input 
                                    type="radio" 
                                    name="category" 
                                    value="{{ $key }}" 
                                    aria-label="Category {{ $category }}"
                                    class="h-5 w-5 text-[#041E42] focus:ring-2 focus:ring-offset-2 focus:ring-[#041E42] mt-1"
                                    {{ old('category', $data['category'] ?? '') == $key ? 'checked' : '' }}
                                    required
                                >
                                <div>
                                    <span class="block font-semibold text-gray-900">{{ $category }}</span>
                                    <span class="block text-sm text-gray-600 mt-1">{{ $categoryDescriptions[$key] }}</span>
                                </div>
                            </label>
                        </div>
                        @endforeach
                    </div>

                    @error('category')
                    <div class="text-red-500 mt-2 text-sm">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="mt-10 pt-5 border-t border-gray-200">
                    <div class="flex justify-center md:justify-end">
                        <button type="submit" class="w-full md:w-auto ml-0 md:ml-3 inline-flex items-center justify-center py-3 px-6 border border-transparent shadow-md text-sm font-semibold rounded-lg text-white bg-[#041E42] hover:bg-[#0A2E5C] transition duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#041E42]">
                            Continue to Details
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
