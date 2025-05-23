@extends('layouts.app')

@section('title', 'Import ' . $title)

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex items-center mb-2">
            <a href="{{ route('admin.participants.category', $category) }}" class="text-[#041E42] hover:text-[#0A2E5C] mr-3 flex items-center">
                <i data-lucide="arrow-left" class="h-5 w-5 mr-1"></i>
                <span>Back to {{ $title }}</span>
            </a>
        </div>
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-[#041E42]">Import Participants</h1>
                <p class="text-gray-600 mt-1">Bulk import {{ strtolower($title) }} from Excel spreadsheet</p>
            </div>
        </div>
    </div>
    
    <!-- Form Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
                <div class="px-6 py-4 bg-gradient-to-r from-[#041E42] to-[#0A2E5C] text-white">
                    <h2 class="font-bold text-xl">Upload Excel File</h2>
                    <p class="text-white/80 text-sm">Select your Excel file with participant data</p>
                </div>
                
                <form action="{{ route('admin.participants.import.process', $category) }}" method="POST" enctype="multipart/form-data" class="p-6">
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
                    
                    <!-- File Upload -->
                    <div class="mb-6">
                        <label class="block text-gray-700 font-medium mb-2">Excel File <span class="text-red-600">*</span></label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                            <div class="space-y-2 text-center">
                                <div class="flex flex-col items-center">
                                    <i data-lucide="file-spreadsheet" class="mx-auto h-12 w-12 text-gray-400"></i>
                                    <div class="mt-4 flex text-sm text-gray-600">
                                        <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-[#041E42] hover:text-[#0A2E5C] focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-[#041E42]">
                                            <span>Upload a file</span>
                                            <input id="file-upload" name="excel_file" type="file" class="sr-only" accept=".xlsx,.xls,.csv" required>
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-2">Excel or CSV files only (max 10MB)</p>
                                </div>
                                <div id="file-name" class="hidden mt-2 text-sm text-gray-800 bg-gray-100 p-2 rounded"></div>
                            </div>
                        </div>
                        @error('excel_file')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <!-- Options -->
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Import Options</h3>
                        
                        <div class="space-y-4">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="header_row" name="header_row" type="checkbox" checked class="rounded border-gray-300 text-[#041E42] focus:ring-[#041E42]">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="header_row" class="font-medium text-gray-700">First row contains headers</label>
                                    <p class="text-gray-500">Check this if your file has column headers in the first row</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="skip_duplicates" name="skip_duplicates" type="checkbox" checked class="rounded border-gray-300 text-[#041E42] focus:ring-[#041E42]">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="skip_duplicates" class="font-medium text-gray-700">Skip duplicate entries</label>
                                    <p class="text-gray-500">Skip importing participants with email addresses that already exist</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Hidden fields -->
                    <input type="hidden" name="category" value="{{ $category }}">
                    
                    <!-- Form Actions -->
                    <div class="mt-6 flex justify-end space-x-3">
                        <a href="{{ route('admin.participants.category', $category) }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#041E42]">
                            Cancel
                        </a>
                        <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-[#041E42] hover:bg-[#0A2E5C] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#041E42]">
                            Import Participants
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Instructions Panel -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h2 class="font-bold text-gray-800">Import Instructions</h2>
                </div>
                
                <div class="p-6">
                    <div class="prose prose-sm max-w-none">
                        <h3>File Format Requirements</h3>
                        <p>Your Excel file should contain the following columns:</p>
                        
                        <ul class="list-disc pl-5 space-y-1">
                            <li><strong>full_name</strong> - Full name of participant</li>
                            <li><strong>email</strong> - Email address (must be unique)</li>
                            <li><strong>phone_number</strong> - Phone number (optional)</li>
                            <li><strong>job_title</strong> - Job title (optional)</li>
                            <li><strong>organization</strong> - Organization name (optional)</li>
                            <li><strong>role</strong> - Role must be one of the following for {{ strtolower($title) }}:
                                <ul class="list-disc pl-5 space-y-1 mt-1">
                                    @foreach($roles ?? [] as $role)
                                        <li>{{ $role }}</li>
                                    @endforeach
                                </ul>
                            </li>
                            <li><strong>payment_status</strong> - Payment status (Paid via Vabu, Paid via M-Pesa, Complimentary, Not Applicable, Waived)</li>
                        </ul>
                        
                        <div class="mt-4 p-3 bg-blue-50 text-blue-800 rounded-md">
                            <p class="flex items-start">
                                <i data-lucide="info" class="h-5 w-5 mr-2 flex-shrink-0"></i>
                                <span>Need a template? <a href="#" class="font-medium underline">Download Excel template</a> to get started.</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('file-upload');
        const fileNameDisplay = document.getElementById('file-name');
        
        fileInput.addEventListener('change', function() {
            if (this.files && this.files.length > 0) {
                fileNameDisplay.textContent = 'Selected file: ' + this.files[0].name;
                fileNameDisplay.classList.remove('hidden');
            } else {
                fileNameDisplay.classList.add('hidden');
            }
        });
        
        // Drag and drop functionality
        const dropZone = document.querySelector('.border-dashed');
        
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });
        
        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, highlight, false);
        });
        
        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, unhighlight, false);
        });
        
        function highlight() {
            dropZone.classList.add('border-[#041E42]', 'bg-blue-50');
        }
        
        function unhighlight() {
            dropZone.classList.remove('border-[#041E42]', 'bg-blue-50');
        }
        
        dropZone.addEventListener('drop', handleDrop, false);
        
        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            
            if (files && files.length > 0) {
                fileInput.files = files;
                fileNameDisplay.textContent = 'Selected file: ' + files[0].name;
                fileNameDisplay.classList.remove('hidden');
            }
        }
    });
</script>

@endsection 