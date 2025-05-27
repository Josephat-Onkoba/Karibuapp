@extends('layouts.app')

@section('title', 'Add Participant')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex items-center mb-2">
            <a href="{{ route('admin.participants.select-role', $category) }}" class="text-[#041E42] hover:text-[#0A2E5C] mr-3 flex items-center">
                <i data-lucide="arrow-left" class="h-5 w-5 mr-1"></i>
                <span>Back to Role Selection</span>
            </a>
        </div>
        <h1 class="text-2xl md:text-3xl font-bold text-[#041E42]">Add New {{ ucfirst($role) }}</h1>
        <p class="text-gray-600 mt-1">Enter participant details below</p>
    </div>

    <!-- Form Section -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
        <div class="p-6">
            @if(session('duplicate_warning'))
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 rounded">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700"><strong>Warning:</strong> A participant with the same name or email already exists.</p>
                        @if(session('duplicate_participant.has_active_ticket'))
                            <p class="text-sm text-yellow-700 mt-1">This participant already has an active ticket. You may be creating a duplicate registration.</p>
                        @endif
                    </div>
                </div>
            </div>
            @endif
            
            <form action="{{ route('admin.participants.store', $category) }}" method="POST" id="registration-form">
                @csrf
                
                @if($errors->any())
                    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded">
                        <div class="text-red-700 font-medium">Please correct the following errors:</div>
                        <ul class="list-disc ml-5 mt-2">
                            @foreach($errors->all() as $error)
                                <li class="text-red-700 text-sm">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <div class="space-y-6">
                    <div>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Participant Details</h3>
                        <p class="text-gray-600 mb-6">Enter the participant's personal information below</p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="full_name" class="block text-gray-700 font-medium mb-1">Full Name <span class="text-red-500">*</span></label>
                            <input 
                                type="text" 
                                name="full_name" 
                                id="full_name" 
                                value="{{ old('full_name') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#041E42]"
                                required
                            >
                            @error('full_name')
                            <div class="text-red-500 mt-2 text-sm">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="email" class="block text-gray-700 font-medium mb-1">Email Address <span class="text-red-500">*</span></label>
                            <input 
                                type="email" 
                                name="email" 
                                id="email" 
                                value="{{ old('email') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#041E42]"
                                required
                            >
                            @error('email')
                            <div class="text-red-500 mt-2 text-sm">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="phone_number" class="block text-gray-700 font-medium mb-1">Phone Number <span class="text-red-500">*</span></label>
                            <input 
                                type="tel" 
                                name="phone_number" 
                                id="phone_number" 
                                value="{{ old('phone_number') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#041E42]"
                                required
                            >
                            @error('phone_number')
                            <div class="text-red-500 mt-2 text-sm">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="role" class="block text-gray-700 font-medium mb-1">Role <span class="text-red-500">*</span></label>
                            <input 
                                type="hidden" 
                                name="role" 
                                id="role" 
                                value="{{ $role }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#041E42]"
                                readonly
                            >
                            <input 
                                type="text" 
                                value="{{ ucfirst($role) }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50 text-gray-500"
                                readonly
                            >
                            @error('role')
                            <div class="text-red-500 mt-2 text-sm">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="job_title" class="block text-gray-700 font-medium mb-1">Job Title</label>
                            <input 
                                type="text" 
                                name="job_title" 
                                id="job_title" 
                                value="{{ old('job_title') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#041E42]"
                            >
                        </div>
                        
                        <div>
                            <label for="organization" class="block text-gray-700 font-medium mb-1">Organization</label>
                            <input 
                                type="text" 
                                name="organization" 
                                id="organization" 
                                value="{{ old('organization') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#041E42]"
                            >
                        </div>
                    </div>
                    
                    @if($role == 'student')
                    <div class="border-t border-gray-200 pt-6 mt-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Student Information</h3>
                        <p class="text-gray-600 mb-6">Fill in the student details:</p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="student_admission_number" class="block text-gray-700 font-medium mb-1">Student Admission Number <span class="text-red-500">*</span></label>
                                <input 
                                    type="text" 
                                    name="student_admission_number" 
                                    id="student_admission_number" 
                                    value="{{ old('student_admission_number') }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#041E42]"
                                    required
                                >
                                @error('student_admission_number')
                                <div class="text-red-500 mt-2 text-sm">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    @if($role == 'staff')
                    <div class="border-t border-gray-200 pt-6 mt-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Staff Information</h3>
                        <p class="text-gray-600 mb-6">Fill in the staff details:</p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="staff_number" class="block text-gray-700 font-medium mb-1">Staff Number <span class="text-red-500">*</span></label>
                                <input 
                                    type="text" 
                                    name="staff_number" 
                                    id="staff_number" 
                                    value="{{ old('staff_number') }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#041E42]"
                                    required
                                >
                                @error('staff_number')
                                <div class="text-red-500 mt-2 text-sm">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    @if($role == 'presenter')
                    <div class="border-t border-gray-200 pt-6 mt-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Presenter Information</h3>
                        <p class="text-gray-600 mb-6">Select the presenter type:</p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="presenter_type" class="block text-gray-700 font-semibold mb-2">
                                    Presenter Type <span class="text-red-500">*</span>
                                </label>
                                <select 
                                    name="presenter_type" 
                                    id="presenter_type" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#041E42] focus:border-[#041E42]"
                                    required
                                >
                                    <option value="" disabled selected>Select presenter type</option>
                                    <option value="non_student" {{ old('presenter_type') == 'non_student' ? 'selected' : '' }}>Non_Student</option>
                                    <option value="student" {{ old('presenter_type') == 'student' ? 'selected' : '' }}>Student</option>
                                    <option value="international" {{ old('presenter_type') == 'international' ? 'selected' : '' }}>International</option>
                                </select>
                                @error('presenter_type')
                                <p class="text-red-500 mt-1 text-sm">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <div class="border-t border-gray-200 pt-6 mt-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Payment Information</h3>
                        <p class="text-gray-600 mb-6">Please provide payment details for this participant</p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Payment Status -->
                            <div>
                                <label for="payment_status" class="block text-gray-700 font-semibold mb-2">
                                    Payment Status <span class="text-red-500">*</span>
                                </label>
                                <select 
                                    name="payment_status" 
                                    id="payment_status" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#041E42] focus:border-[#041E42]"
                                    required
                                >
                                    <option value="" disabled selected>Select payment status</option>
                                    <option value="Not Paid" {{ old('payment_status') == 'Not Paid' ? 'selected' : '' }}>Not Paid</option>
                                    <option value="Paid via Vabu" {{ old('payment_status') == 'Paid via Vabu' ? 'selected' : '' }}>Paid via Vabu</option>
                                    <option value="Paid via M-Pesa" {{ old('payment_status') == 'Paid via M-Pesa' ? 'selected' : '' }}>Paid via M-Pesa</option>
                                    @if($role === 'delegate')
                                    <option value="Waived" {{ old('payment_status') == 'Waived' ? 'selected' : '' }}>Waived</option>
                                    @endif
                                </select>
                                @error('payment_status')
                                <p class="text-red-500 mt-1 text-sm">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Payment Amount -->
                            <div id="payment_amount_field">
                                <label for="payment_amount" class="block text-gray-700 font-semibold mb-2">
                                    Payment Amount <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="number" 
                                    name="payment_amount" 
                                    id="payment_amount" 
                                    value="{{ old('payment_amount') }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#041E42] focus:border-[#041E42]"
                                    placeholder="Enter amount in KES"
                                >
                                @error('payment_amount')
                                <p class="text-red-500 mt-1 text-sm">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Eligible Days -->
                            @if($role === 'delegate')
                            <div>
                                <label for="eligible_days" class="block text-gray-700 font-semibold mb-2">
                                    Eligible Days <span class="text-red-500">*</span>
                                </label>
                                <select 
                                    name="eligible_days" 
                                    id="eligible_days" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#041E42] focus:border-[#041E42]"
                                    required
                                >
                                    <option value="" disabled selected>Select number of days</option>
                                    <option value="1" {{ old('eligible_days') == '1' ? 'selected' : '' }}>1 Day (KSH 3,000)</option>
                                    <option value="2" {{ old('eligible_days') == '2' ? 'selected' : '' }}>2 Days (KSH 6,000)</option>
                                    <option value="3" {{ old('eligible_days') == '3' ? 'selected' : '' }}>3 Days (KSH 9,000)</option>
                                </select>
                                @error('eligible_days')
                                <p class="text-red-500 mt-1 text-sm">{{ $message }}</p>
                                @enderror
                            </div>
                            @elseif($role === 'presenter')
                                <!-- Presenters are eligible for all days by default -->
                                <input type="hidden" name="eligible_days" value="3">
                            @elseif($role === 'exhibitor')
                                <!-- Exhibitors are eligible for all days by default -->
                                <input type="hidden" name="eligible_days" value="3">
                            @endif

                            <!-- Payment Confirmation -->
                            <div class="md:col-span-2 mt-2">
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input
                                            type="checkbox"
                                            name="payment_confirmed"
                                            id="payment_confirmed"
                                            class="h-4 w-4 text-[#041E42] border-gray-300 rounded focus:ring-[#041E42]"
                                            {{ old('payment_confirmed') ? 'checked' : '' }}
                                        >
                                    </div>
                                    <div class="ml-3">
                                        <label for="payment_confirmed" class="text-gray-700 font-semibold">Payment Confirmed</label>
                                        <p class="text-gray-500 text-sm">Check this box if payment has been confirmed</p>
                                    </div>
                                </div>
                                @error('payment_confirmed')
                                <p class="text-red-500 mt-1 text-sm">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                
                    <!-- Submit Button -->
                    <div class="mt-10 pt-5 border-t border-gray-200">
                        <div class="flex flex-col space-y-3 sm:flex-row sm:space-y-0 sm:space-x-4">
                            <a href="{{ route('admin.participants.select-role', $category) }}" 
                                class="w-full sm:w-auto inline-flex justify-center items-center py-3 px-6 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 -ml-1" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                Back to Role Selection
                            </a>
                            <button 
                                type="submit" 
                                class="w-full sm:w-auto inline-flex items-center justify-center py-3 px-6 text-sm font-semibold rounded-lg text-white bg-[#041E42] hover:bg-[#0A2E5C] 
                                    border border-transparent shadow-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#041E42]">
                                <span>Save Participant</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2 -mr-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle payment status
        const paymentStatusSelect = document.getElementById('payment_status');
        const paymentAmountField = document.getElementById('payment_amount_field');
        
        paymentStatusSelect.addEventListener('change', function() {
            if (this.value === 'Waived') {
                paymentAmountField.classList.add('hidden');
            } else {
                paymentAmountField.classList.remove('hidden');
            }
        });
        
        // For delegates, update payment amount based on eligible days
        const eligibleDaysSelect = document.getElementById('eligible_days');
        const paymentAmountInput = document.getElementById('payment_amount');
        
        if (eligibleDaysSelect) {
            eligibleDaysSelect.addEventListener('change', function() {
                const days = parseInt(this.value) || 0;
                const amountPerDay = 3000; // KSH 3,000 per day
                paymentAmountInput.value = days * amountPerDay;
            });
        }
        
        // Initial status check
        if (paymentStatusSelect.value === 'Waived') {
            paymentAmountField.classList.add('hidden');
        }
        
        // If eligible days has a value on page load, calculate the payment amount
        if (eligibleDaysSelect && eligibleDaysSelect.value) {
            const days = parseInt(eligibleDaysSelect.value) || 0;
            const amountPerDay = 3000; // KSH 3,000 per day
            paymentAmountInput.value = days * amountPerDay;
        }
    });
</script>
@endpush
@endsection