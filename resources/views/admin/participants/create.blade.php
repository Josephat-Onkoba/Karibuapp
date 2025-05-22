@extends('layouts.app')

@section('title', 'Add ' . $title)

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
                <h1 class="text-2xl md:text-3xl font-bold text-[#041E42]">Add New Participant</h1>
                <p class="text-gray-600 mt-1">Create a new {{ strtolower($title) }} record</p>
            </div>
        </div>
    </div>
    
    <!-- Form Section -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200 max-w-3xl mx-auto">
        <div class="px-6 py-4 bg-[#041E42] text-white">
            <h2 class="font-bold text-xl">Participant Information</h2>
            <p class="text-white/80 text-sm">Please fill in all required fields</p>
        </div>
        
        <form action="{{ route('admin.participants.store', $category) }}" method="POST" class="p-6">
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
                <!-- Basic Information -->
                <div class="border-b border-gray-200 pb-6">
                    <div class="flex items-center mb-4">
                        <div class="w-8 h-8 rounded-full bg-[#041E42] text-white flex items-center justify-center mr-3">
                            <i data-lucide="user" class="h-4 w-4"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900">Basic Information</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Full Name -->
                        <div>
                            <label for="full_name" class="block text-gray-700 font-medium mb-1">Full Name <span class="text-red-600">*</span></label>
                            <input type="text" name="full_name" id="full_name" value="{{ old('full_name') }}" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#041E42]"
                                   placeholder="Enter full name" required>
                            @error('full_name')
                                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-gray-700 font-medium mb-1">Email Address <span class="text-red-600">*</span></label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#041E42]"
                                   placeholder="user@example.com" required>
                            @error('email')
                                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <!-- Phone -->
                        <div class="md:col-span-2">
                            <label for="phone_number" class="block text-gray-700 font-medium mb-1">Phone Number</label>
                            <div class="flex rounded-lg shadow-sm">
                                <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                                    <i data-lucide="phone" class="h-4 w-4 mr-1"></i> +254
                                </span>
                                <input type="tel" name="phone_number" id="phone_number" value="{{ old('phone_number') }}" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-r-lg focus:outline-none focus:ring-2 focus:ring-[#041E42]"
                                       placeholder="712 345678">
                            </div>
                            @error('phone_number')
                                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <!-- Professional Information -->
                <div class="border-b border-gray-200 pb-6">
                    <div class="flex items-center mb-4">
                        <div class="w-8 h-8 rounded-full bg-[#041E42] text-white flex items-center justify-center mr-3">
                            <i data-lucide="briefcase" class="h-4 w-4"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900">Professional Information</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Job Title -->
                        <div>
                            <label for="job_title" class="block text-gray-700 font-medium mb-1">Job Title</label>
                            <input type="text" name="job_title" id="job_title" value="{{ old('job_title') }}" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#041E42]"
                                   placeholder="e.g. Professor, Researcher, Manager">
                            @error('job_title')
                                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <!-- Organization -->
                        <div>
                            <label for="organization" class="block text-gray-700 font-medium mb-1">Organization</label>
                            <input type="text" name="organization" id="organization" value="{{ old('organization') }}" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#041E42]"
                                   placeholder="e.g. University, Company, Institution">
                            @error('organization')
                                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <!-- Role -->
                        <div class="md:col-span-2">
                            <label for="role" class="block text-gray-700 font-medium mb-1">Role <span class="text-red-600">*</span></label>
                            <div class="relative">
                                <select name="role" id="role" class="appearance-none w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#041E42] pr-10" required>
                                    <option value="">Select a role</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role }}" {{ old('role') == $role ? 'selected' : '' }}>{{ $role }}</option>
                                    @endforeach
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                    <i data-lucide="chevron-down" class="h-4 w-4"></i>
                                </div>
                            </div>
                            @error('role')
                                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <!-- Payment Information -->
                <div class="border-b border-gray-200 pb-6">
                    <div class="flex items-center mb-4">
                        <div class="w-8 h-8 rounded-full bg-[#041E42] text-white flex items-center justify-center mr-3">
                            <i data-lucide="credit-card" class="h-4 w-4"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900">Payment Information</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Payment Status -->
                        <div>
                            <label for="payment_status" class="block text-gray-700 font-medium mb-1">Payment Status <span class="text-red-600">*</span></label>
                            <div class="relative">
                                <select name="payment_status" id="payment_status" class="appearance-none w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#041E42] pr-10" required>
                                    <option value="">Select payment status</option>
                                    <option value="Paid via Vabu" {{ old('payment_status') == 'Paid via Vabu' ? 'selected' : '' }}>Paid via Vabu</option>
                                    <option value="Paid via M-Pesa" {{ old('payment_status') == 'Paid via M-Pesa' ? 'selected' : '' }}>Paid via M-Pesa</option>
                                    <option value="Complimentary" {{ old('payment_status') == 'Complimentary' ? 'selected' : '' }}>Complimentary</option>
                                    <option value="Not Applicable" {{ old('payment_status') == 'Not Applicable' ? 'selected' : '' }}>Not Applicable</option>
                                    <option value="Waived" {{ old('payment_status') == 'Waived' ? 'selected' : '' }}>Waived</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                    <i data-lucide="chevron-down" class="h-4 w-4"></i>
                                </div>
                            </div>
                            @error('payment_status')
                                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <!-- Payment Confirmation -->
                        <div>
                            <label for="payment_confirmed" class="block text-gray-700 font-medium mb-1">Payment Confirmation</label>
                            <textarea name="payment_confirmed" id="payment_confirmed" rows="3" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#041E42]"
                                   placeholder="Enter payment confirmation details (e.g. receipt number, confirmation method)">{{ old('payment_confirmed') }}</textarea>
                            @error('payment_confirmed')
                                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <!-- Additional Notes -->
                <div>
                    <div class="flex items-center mb-4">
                        <div class="w-8 h-8 rounded-full bg-[#041E42] text-white flex items-center justify-center mr-3">
                            <i data-lucide="clipboard" class="h-4 w-4"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900">Additional Information</h3>
                    </div>
                    
                    <label for="notes" class="block text-gray-700 font-medium mb-1">Notes (Optional)</label>
                    <textarea name="notes" id="notes" rows="3" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#041E42]"
                            placeholder="Any additional information about this participant">{{ old('notes') }}</textarea>
                </div>
                
                <!-- Hidden fields -->
                <input type="hidden" name="category" value="{{ $category }}">
                
                <!-- Form Actions -->
                <div class="pt-6 flex justify-end space-x-3">
                    <a href="{{ route('admin.participants.category', $category) }}" class="px-5 py-3 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#041E42]">
                        Cancel
                    </a>
                    <button type="submit" class="px-5 py-3 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-[#041E42] hover:bg-[#0A2E5C] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#041E42] flex items-center">
                        <i data-lucide="plus" class="h-4 w-4 mr-2"></i>
                        Create Participant
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection 