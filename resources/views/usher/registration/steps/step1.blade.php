@extends('layouts.app')

@section('title', 'Registration - Step 1: Category Selection')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-[#041E42]">Registration</h1>
                <p class="text-gray-600 mt-1">Step 1: Select Participant Category</p>
            </div>
        </div>
    </div>

    <!-- Steps Progress -->
    <div class="mb-8">
        <div class="max-w-4xl mx-auto">
            <div class="relative">
                <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-gray-200">
                    <div class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-[#041E42] w-1/5"></div>
                </div>
                <div class="flex text-xs text-gray-600 mt-1 justify-between">
                    <div class="text-[#041E42] font-semibold">Category</div>
                    <div>Details</div>
                    <div>Attendance</div>
                    <div>Ticket</div>
                    <div>Check-in</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Content -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
        <div class="p-6">
            <form action="{{ route('usher.registration.process_step1') }}" method="POST">
                @csrf
                
                <div class="mb-6">
                    <h3 class="text-lg font-bold text-gray-700 mb-4">Select Participant Category</h3>
                    <p class="text-gray-600 mb-6">Choose the appropriate category for this participant</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($categories as $key => $category)
                        <div class="border rounded-lg p-4 hover:border-[#041E42] hover:shadow-md transition-all duration-300">
                            <label class="flex items-start cursor-pointer">
                                <input 
                                    type="radio" 
                                    name="category" 
                                    value="{{ $key }}" 
                                    class="h-5 w-5 text-[#041E42] focus:ring-[#041E42] mt-1"
                                    {{ old('category', $data['category'] ?? '') == $key ? 'checked' : '' }}
                                    required
                                >
                                <div class="ml-3">
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
                
                <div class="mt-8 pt-5 border-t border-gray-200">
                    <div class="flex justify-end">
                        <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-[#041E42] hover:bg-[#0A2E5C] focus:outline-none">
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