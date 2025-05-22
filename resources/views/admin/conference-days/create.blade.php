@extends('layouts.app')

@section('title', 'Add Conference Day')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex items-center mb-2">
            <a href="{{ route('admin.conference-days.index') }}" class="text-[#041E42] hover:text-[#0A2E5C] mr-3 flex items-center">
                <i data-lucide="arrow-left" class="h-5 w-5 mr-1"></i>
                <span>Back to Conference Days</span>
            </a>
        </div>
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-[#041E42]">Add Conference Day</h1>
                <p class="text-gray-600 mt-1">Create a new attendance day for the conference</p>
            </div>
        </div>
    </div>
    
    <!-- Form Section -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200 max-w-3xl mx-auto">
        <div class="px-6 py-4 bg-[#041E42] text-white">
            <h2 class="font-bold text-xl">Conference Day Information</h2>
            <p class="text-white/80 text-sm">Please fill in all required fields</p>
        </div>
        
        <form action="{{ route('admin.conference-days.store') }}" method="POST" class="p-6">
            @csrf
            
            @if($errors->any())
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i data-lucide="alert-triangle" class="h-5 w-5 text-red-500"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Please fix the following errors:</h3>
                            <ul class="mt-1 text-sm text-red-700 list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif
            
            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Day Name -->
                    <div>
                        <label for="name" class="block text-gray-700 font-medium mb-1">Day Name <span class="text-red-600">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#041E42]"
                               placeholder="e.g. Day 1, Opening Day" required>
                        @error('name')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <!-- Day Date -->
                    <div>
                        <label for="date" class="block text-gray-700 font-medium mb-1">Date <span class="text-red-600">*</span></label>
                        <input type="date" name="date" id="date" value="{{ old('date') }}" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#041E42]"
                               required>
                        @error('date')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                
                <!-- Description -->
                <div>
                    <label for="description" class="block text-gray-700 font-medium mb-1">Description (Optional)</label>
                    <textarea name="description" id="description" rows="3" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#041E42]"
                           placeholder="Describe this conference day">{{ old('description') }}</textarea>
                    @error('description')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>
                
                <!-- Form Actions -->
                <div class="pt-6 flex justify-end space-x-3">
                    <a href="{{ route('admin.conference-days.index') }}" class="px-5 py-3 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#041E42]">
                        Cancel
                    </a>
                    <button type="submit" class="px-5 py-3 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-[#041E42] hover:bg-[#0A2E5C] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#041E42] flex items-center">
                        <i data-lucide="plus" class="h-4 w-4 mr-2"></i>
                        Create Day
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection 