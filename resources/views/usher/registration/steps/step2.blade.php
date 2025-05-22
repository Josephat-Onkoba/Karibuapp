@extends('layouts.app')

@section('title', 'Registration - Step 2: Participant Details')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-[#041E42]">Registration</h1>
                <p class="text-gray-600 mt-1">Step 2: Enter Participant Details</p>
            </div>
        </div>
    </div>

    <!-- Steps Progress -->
    <div class="mb-8">
        <div class="max-w-4xl mx-auto">
            <div class="relative">
                <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-gray-200">
                    <div class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-[#041E42] w-2/5"></div>
                </div>
                <div class="flex text-xs text-gray-600 mt-1 justify-between">
                    <div>Category</div>
                    <div class="text-[#041E42] font-semibold">Details</div>
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
            <form action="{{ route('usher.registration.process_step2') }}" method="POST" id="registration-form">
                @csrf
                
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-bold text-gray-700 mb-4">Participant Details</h3>
                        <p class="text-gray-600 mb-4">Enter the participant's personal information below</p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="full_name" class="block text-gray-700 font-medium mb-1">Full Name <span class="text-red-500">*</span></label>
                            <input 
                                type="text" 
                                name="full_name" 
                                id="full_name" 
                                value="{{ old('full_name', $data['full_name'] ?? '') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#041E42]"
                                required
                            >
                            @error('full_name')
                            <div class="text-red-500 mt-1 text-sm">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="email" class="block text-gray-700 font-medium mb-1">Email Address <span class="text-red-500">*</span></label>
                            <input 
                                type="email" 
                                name="email" 
                                id="email" 
                                value="{{ old('email', $data['email'] ?? '') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#041E42]"
                                required
                            >
                            @error('email')
                            <div class="text-red-500 mt-1 text-sm">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-5">
                        <div>
                            <label for="phone_number" class="block text-gray-700 font-medium mb-1">Phone Number <span class="text-red-500">*</span></label>
                            <input 
                                type="tel" 
                                name="phone_number" 
                                id="phone_number" 
                                value="{{ old('phone_number', $data['phone_number'] ?? '') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#041E42]"
                                required
                            >
                            @error('phone_number')
                            <div class="text-red-500 mt-1 text-sm">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="role" class="block text-gray-700 font-medium mb-1">Role <span class="text-red-500">*</span></label>
                            <select 
                                name="role" 
                                id="role" 
                                class="appearance-none w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#041E42] pr-10"
                                required
                            >
                                <option value="">Select a role</option>
                                @foreach($roles as $role)
                                <option value="{{ $role }}" {{ old('role', $data['role'] ?? '') == $role ? 'selected' : '' }}>{{ $role }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                            </div>
                            @error('role')
                            <div class="text-red-500 mt-1 text-sm">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-5">
                        <div>
                            <label for="job_title" class="block text-gray-700 font-medium mb-1">Job Title</label>
                            <input 
                                type="text" 
                                name="job_title" 
                                id="job_title" 
                                value="{{ old('job_title', $data['job_title'] ?? '') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#041E42]"
                            >
                        </div>
                        
                        <div>
                            <label for="organization" class="block text-gray-700 font-medium mb-1">Organization</label>
                            <input 
                                type="text" 
                                name="organization" 
                                id="organization" 
                                value="{{ old('organization', $data['organization'] ?? '') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#041E42]"
                            >
                        </div>
                    </div>
                    
                    @if($data['category'] === 'internal')
                    <div class="border-t border-gray-200 pt-4 mt-6">
                        <h3 class="text-lg font-bold text-gray-700 mb-4">Internal Participant Information</h3>
                        <p class="text-gray-600 mb-4">Fill in the appropriate field based on participant role:</p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="student_admission_number" class="block text-sm font-medium text-gray-700">Student Admission Number <span class="text-gray-500">(For students only)</span></label>
                                <input 
                                    type="text" 
                                    name="student_admission_number" 
                                    id="student_admission_number" 
                                    value="{{ old('student_admission_number', $data['student_admission_number'] ?? '') }}"
                                    class="mt-1 focus:ring-[#041E42] focus:border-[#041E42] block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                >
                                @error('student_admission_number')
                                <div class="text-red-500 mt-1 text-sm">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="staff_number" class="block text-sm font-medium text-gray-700">Staff Number <span class="text-gray-500">(For staff only)</span></label>
                                <input 
                                    type="text" 
                                    name="staff_number" 
                                    id="staff_number" 
                                    value="{{ old('staff_number', $data['staff_number'] ?? '') }}"
                                    class="mt-1 focus:ring-[#041E42] focus:border-[#041E42] block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                >
                                @error('staff_number')
                                <div class="text-red-500 mt-1 text-sm">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    @if($data['category'] === 'general')
                    <div class="border-t border-gray-200 pt-4 mt-6">
                        <h3 class="text-lg font-bold text-gray-700 mb-4">Payment Information</h3>
                        <p class="text-gray-600 mb-4">Please provide payment details for this participant</p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="payment_status" class="block text-gray-700 font-medium mb-1">Payment Status <span class="text-red-500">*</span></label>
                                <select 
                                    name="payment_status" 
                                    id="payment_status" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#041E42]"
                                    required
                                >
                                    <option value="">Select payment status</option>
                                    <option value="Not Paid" {{ old('payment_status', $data['payment_status'] ?? '') == 'Not Paid' ? 'selected' : '' }}>Not Paid</option>
                                    <option value="Paid via Vabu" {{ old('payment_status', $data['payment_status'] ?? '') == 'Paid via Vabu' ? 'selected' : '' }}>Paid via Vabu</option>
                                    <option value="Paid via M-Pesa" {{ old('payment_status', $data['payment_status'] ?? '') == 'Paid via M-Pesa' ? 'selected' : '' }}>Paid via M-Pesa</option>
                                    <option value="Complimentary" {{ old('payment_status', $data['payment_status'] ?? '') == 'Complimentary' ? 'selected' : '' }}>Complimentary</option>
                                </select>
                                @error('payment_status')
                                <div class="text-red-500 mt-1 text-sm">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="payment_confirmed" class="flex items-center text-gray-700 font-medium mt-5">
                                    <input 
                                        type="checkbox" 
                                        name="payment_confirmed" 
                                        id="payment_confirmed" 
                                        value="1" 
                                        {{ old('payment_confirmed', $data['payment_confirmed'] ?? '') ? 'checked' : '' }}
                                        class="focus:ring-[#041E42] h-5 w-5 text-[#041E42] border-gray-300 rounded mr-2"
                                    >
                                    <span>Payment Confirmed</span>
                                </label>
                                <p class="text-sm text-gray-500 mt-1 ml-7">Check this box if payment has been verified</p>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <label for="payment_notes" class="block text-gray-700 font-medium mb-1">Payment Notes</label>
                            <textarea 
                                name="payment_notes" 
                                id="payment_notes" 
                                rows="2" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#041E42]"
                                placeholder="Enter details about the payment (e.g., M-Pesa transaction code, receipt number)"
                            >{{ old('payment_notes', $data['payment_notes'] ?? '') }}</textarea>
                        </div>
                    </div>
                    @endif
                </div>
                
                <div class="mt-8 pt-5 border-t border-gray-200">
                    <div class="flex justify-between">
                        <a href="{{ route('usher.registration.step1') }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 -ml-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            Back to Category
                        </a>
                        <button type="submit" id="continue-button" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-[#041E42] hover:bg-[#0A2E5C] focus:outline-none">
                            Continue to Attendance Days
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

<!-- M-Pesa Payment Modal -->
<div id="payment-modal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 overflow-hidden">
        <div class="px-6 py-4 bg-[#041E42] text-white flex justify-between items-center">
            <h3 class="text-lg font-medium">M-Pesa Payment Information</h3>
            <button onclick="closePaymentModal()" class="text-white hover:text-gray-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>
        <div class="p-6">
            <div class="text-center mb-6">
                <img src="{{ asset('images/mpesa-logo.png') }}" alt="M-Pesa Logo" class="h-12 mx-auto mb-4">
                <p class="text-gray-600">Please make payment using the following details:</p>
            </div>
            
            <div class="space-y-4 mb-6">
                <div class="bg-gray-50 p-3 rounded-lg border border-gray-200">
                    <p class="text-sm text-gray-500">Pay Bill Number</p>
                    <p class="text-xl font-bold text-gray-800">123456</p>
                </div>
                
                <div class="bg-gray-50 p-3 rounded-lg border border-gray-200">
                    <p class="text-sm text-gray-500">Account Number</p>
                    <p class="text-xl font-bold text-gray-800">KaribuConf-{{ substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 6) }}</p>
                </div>
                
                <div class="bg-gray-50 p-3 rounded-lg border border-gray-200">
                    <p class="text-sm text-gray-500">Amount</p>
                    <p class="text-xl font-bold text-gray-800">Ksh 2,500</p>
                </div>
            </div>
            
            <div class="flex flex-col space-y-3">
                <p class="text-sm text-gray-600">Once you've made the payment, enter the transaction details below:</p>
                
                <div>
                    <label for="mpesa-code" class="block text-sm font-medium text-gray-700">M-Pesa Transaction Code</label>
                    <input type="text" id="mpesa-code" placeholder="e.g., QWE123456" class="mt-1 focus:ring-[#041E42] focus:border-[#041E42] block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>
                
                <div class="flex space-x-3 mt-6">
                    <button onclick="confirmPayment()" class="flex-1 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none">
                        Confirm Payment
                    </button>
                    <button onclick="skipPayment()" class="flex-1 inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none">
                        Pay Later
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const paymentStatusSelect = document.getElementById('payment_status');
        const paymentConfirmedCheckbox = document.getElementById('payment_confirmed');
        const registrationForm = document.getElementById('registration-form');
        const continueButton = document.getElementById('continue-button');
        const category = "{{ $data['category'] }}";
        
        if (paymentStatusSelect) {
            paymentStatusSelect.addEventListener('change', function() {
                // If "Not Paid" is selected, uncheck the payment confirmed checkbox
                if (this.value === "Not Paid") {
                    paymentConfirmedCheckbox.checked = false;
                }
            });
        }
        
        // Add form submit handler for general category
        if (category === 'general' && registrationForm) {
            registrationForm.addEventListener('submit', function(event) {
                const paymentStatus = paymentStatusSelect.value;
                const isPaymentConfirmed = paymentConfirmedCheckbox.checked;
                
                // If not paid and payment not confirmed, show the payment modal
                if (paymentStatus === "Not Paid" && !isPaymentConfirmed) {
                    event.preventDefault();
                    document.getElementById('payment-modal').classList.remove('hidden');
                }
                // If payment status is empty, don't allow form submission
                else if (category === 'general' && paymentStatus === "") {
                    event.preventDefault();
                    alert("Please select a payment status before continuing.");
                }
            });
        }
        
        // M-Pesa payment modal functionality
        const openMpesaModal = document.getElementById('open-mpesa-modal');
        const paymentModal = document.getElementById('payment-modal');
        
        if (openMpesaModal && paymentModal) {
            openMpesaModal.addEventListener('click', function(e) {
                e.preventDefault();
                paymentModal.classList.remove('hidden');
            });
        }
        
        function closePaymentModal() {
            if (paymentModal) {
                paymentModal.classList.add('hidden');
            }
        }
        
        // Make closePaymentModal globally accessible
        window.closePaymentModal = closePaymentModal;
        
        // Handle M-Pesa transaction code copy
        const transactionCodeCopy = document.getElementById('copy-transaction-code');
        if (transactionCodeCopy) {
            transactionCodeCopy.addEventListener('click', function(e) {
                e.preventDefault();
                const code = document.getElementById('mpesa-transaction-code').textContent;
                
                // Copy to clipboard
                navigator.clipboard.writeText(code).then(() => {
                    transactionCodeCopy.textContent = 'Copied!';
                    setTimeout(() => {
                        transactionCodeCopy.textContent = 'Copy';
                    }, 2000);
                });
            });
        }
        
        // Apply M-Pesa payment from modal
        const applyMpesaBtn = document.getElementById('apply-mpesa-payment');
        if (applyMpesaBtn) {
            applyMpesaBtn.addEventListener('click', function() {
                document.getElementById('payment_status').value = "Paid via M-Pesa";
                document.getElementById('payment_confirmed').checked = true;
                
                const mpesaCode = document.getElementById('mpesa-transaction-code').textContent;
                const paymentNotes = document.getElementById('payment_notes');
                paymentNotes.value = "M-Pesa Transaction Code: " + mpesaCode + "\n" + paymentNotes.value;
                
                closePaymentModal();
            });
        }
        
        // Clear M-Pesa payment
        const clearMpesaBtn = document.getElementById('clear-mpesa-payment');
        if (clearMpesaBtn) {
            clearMpesaBtn.addEventListener('click', function() {
                document.getElementById('payment_status').value = "Not Paid";
                document.getElementById('payment_confirmed').checked = false;
                closePaymentModal();
            });
        }
    });
    
    function confirmPayment() {
        const mpesaCode = document.getElementById('mpesa-code').value.trim();
        
        if (mpesaCode === '') {
            alert('Please enter the M-Pesa transaction code.');
            return;
        }
        
        // Set payment status to "Paid via M-Pesa" and add transaction code to notes
        document.getElementById('payment_status').value = "Paid via M-Pesa";
        document.getElementById('payment_confirmed').checked = true;
        document.getElementById('payment_notes').value += `\nM-Pesa Transaction: ${mpesaCode}`;
        
        // Close modal and submit form
        closePaymentModal();
        document.getElementById('registration-form').submit();
    }
    
    function skipPayment() {
        // Keep "Not Paid" status but allow continuing
        document.getElementById('payment_status').value = "Not Paid";
        closePaymentModal();
        document.getElementById('registration-form').submit();
    }
</script>
@endpush 