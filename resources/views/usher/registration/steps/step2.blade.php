@extends('layouts.app')

@section('title', 'Registration - Step 2: Participant Details')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Sticky Header -->
    <div class="sticky top-0 z-10 bg-white pb-4 mb-6 text-center">
        <h1 class="text-2xl md:text-4xl font-bold text-[#041E42]">Registration</h1>
        <p class="text-gray-600 mt-2 md:text-lg">Step 2: Enter Participant Details</p>
    </div>

    <!-- Stepper Progress -->
    <div class="mb-10">
        <div class="max-w-4xl mx-auto">
            <div class="flex justify-between text-sm md:text-base">
                @php
                    $steps = ['Category', 'Details', 'Attendance', 'Ticket', 'Check-in'];
                @endphp
                @foreach($steps as $index => $step)
                    <div class="flex flex-col items-center {{ $index === 1 ? 'text-[#041E42] font-bold' : 'text-gray-400' }}">
                        <div class="w-7 h-7 md:w-8 md:h-8 rounded-full flex items-center justify-center 
                                    {{ $index === 1 ? 'bg-[#041E42] text-white' : ($index < 1 ? 'bg-[#041E42] text-white' : 'bg-gray-300 text-white') }}">
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
                        <div class="mt-2">
                            <a href="{{ url('usher/participant') }}/{{ session('duplicate_participant.id') }}/view" target="_blank" class="text-sm text-yellow-700 font-medium underline hover:text-yellow-600">View Existing Participant</a>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            <form action="{{ route('usher.registration.process_step2') }}" method="POST" id="registration-form">
                @csrf
                
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
                                value="{{ old('full_name', $data['full_name'] ?? '') }}"
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
                                value="{{ old('email', $data['email'] ?? '') }}"
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
                                value="{{ old('phone_number', $data['phone_number'] ?? '') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#041E42]"
                                required
                            >
                            @error('phone_number')
                            <div class="text-red-500 mt-2 text-sm">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="role" class="block text-gray-700 font-medium mb-1">Role <span class="text-red-500">*</span></label>
                            <select 
                                name="role" 
                                id="role" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#041E42]"
                                required
                            >
                                <option value="">Select a role</option>
                                @foreach($roles as $role)
                                <option value="{{ $role }}" {{ old('role', $data['role'] ?? '') == $role ? 'selected' : '' }}>{{ $role }}</option>
                                @endforeach
                            </select>
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
                    <div class="border-t border-gray-200 pt-6 mt-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Internal Participant Information</h3>
                        <p class="text-gray-600 mb-6">Fill in the appropriate field based on participant role:</p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="student_admission_number" class="block text-gray-700 font-medium mb-1">Student Admission Number <span class="text-gray-500">(For students only)</span></label>
                                <input 
                                    type="text" 
                                    name="student_admission_number" 
                                    id="student_admission_number" 
                                    value="{{ old('student_admission_number', $data['student_admission_number'] ?? '') }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#041E42]"
                                >
                                @error('student_admission_number')
                                <div class="text-red-500 mt-2 text-sm">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="staff_number" class="block text-gray-700 font-medium mb-1">Staff Number <span class="text-gray-500">(For staff only)</span></label>
                                <input 
                                    type="text" 
                                    name="staff_number" 
                                    id="staff_number" 
                                    value="{{ old('staff_number', $data['staff_number'] ?? '') }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#041E42]"
                                >
                                @error('staff_number')
                                <div class="text-red-500 mt-2 text-sm">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    @if(in_array($data['category'], ['general', 'exhibitor', 'presenter']))
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
                                    <option value="Not Paid" {{ old('payment_status', $data['payment_status'] ?? '') == 'Not Paid' ? 'selected' : '' }}>Not Paid</option>
                                    <option value="Paid via Vabu" {{ old('payment_status', $data['payment_status'] ?? '') == 'Paid via Vabu' ? 'selected' : '' }}>Paid via Vabu</option>
                                    <option value="Paid via M-Pesa" {{ old('payment_status', $data['payment_status'] ?? '') == 'Paid via M-Pesa' ? 'selected' : '' }}>Paid via M-Pesa</option>
                                    @if($data['category'] === 'general')
                                    <option value="Waived" {{ old('payment_status', $data['payment_status'] ?? '') == 'Waived' ? 'selected' : '' }}>Waived</option>
                                    @endif
                                </select>
                                @error('payment_status')
                                <p class="text-red-500 mt-1 text-sm">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Presenter Type -->
                            @if($data['category'] === 'presenter')
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
                                    <option value="non_student" {{ old('presenter_type', $data['presenter_type'] ?? '') == 'non_student' ? 'selected' : '' }}>Non-Student (KSH 6,000)</option>
                                    <option value="student" {{ old('presenter_type', $data['presenter_type'] ?? '') == 'student' ? 'selected' : '' }}>Student (KSH 4,000)</option>
                                    <option value="international" {{ old('presenter_type', $data['presenter_type'] ?? '') == 'international' ? 'selected' : '' }}>International (USD 100)</option>
                                </select>
                                @error('presenter_type')
                                <p class="text-red-500 mt-1 text-sm">{{ $message }}</p>
                                @enderror
                            </div>
                            @endif

                            <!-- Eligible Days -->
                            @if($data['category'] === 'general')
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
                                    <option value="1" {{ old('eligible_days', $data['eligible_days'] ?? '') == '1' ? 'selected' : '' }}>1 Day (KSH 3,000)</option>
                                    <option value="2" {{ old('eligible_days', $data['eligible_days'] ?? '') == '2' ? 'selected' : '' }}>2 Days (KSH 6,000)</option>
                                    <option value="3" {{ old('eligible_days', $data['eligible_days'] ?? '') == '3' ? 'selected' : '' }}>3 Days (KSH 9,000)</option>
                                </select>
                                @error('eligible_days')
                                <p class="text-red-500 mt-1 text-sm">{{ $message }}</p>
                                @enderror
                        </div>
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
                                            {{ old('payment_confirmed', $data['payment_confirmed'] ?? '') ? 'checked' : '' }}
                                            onchange="handlePaymentConfirmation(this)"
                                        >
                                    </div>
                                    <div class="ml-3">
                                        <label for="payment_confirmed" class="text-gray-700 font-semibold">Payment Confirmed</label>
                                        <p class="text-gray-500 text-sm">Check this box if payment has been confirmed (required for "Paid" options)</p>
                                    </div>
                                </div>
                                @error('payment_confirmed')
                                <p class="text-red-500 mt-1 text-sm">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Hidden inputs -->
                            <input type="hidden" name="registered_by_user_id" value="{{ Auth::id() }}">
                            <input type="hidden" name="transaction_code" id="transaction_code" value="{{ old('transaction_code', $data['transaction_code'] ?? '') }}">
                            <input type="hidden" name="payment_notes" id="payment_notes" value="{{ old('payment_notes', $data['payment_notes'] ?? '') }}">
                            
                            <!-- Hidden inputs for Exhibitor -->
                            @if($data['category'] === 'exhibitor')
                            <input type="hidden" name="payment_amount" value="30000">
                            <input type="hidden" name="eligible_days" value="3">
                            @endif
                        </div>
                    </div>

                    @endif
                </div>
                
                <!-- Submit Button -->
                <div class="mt-10 pt-5 border-t border-gray-200">
                <div class="flex flex-col space-y-3 sm:flex-row sm:space-y-0 sm:space-x-4">
                    <a href="{{ route('usher.registration.step1') }}" 
                    class="w-full sm:w-auto inline-flex justify-center items-center py-3 px-6 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 -ml-1" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                        Back to Category
                    </a>
                    <button 
                        type="submit" 
                        class="w-full sm:w-auto inline-flex items-center justify-center py-3 px-6 text-sm font-semibold rounded-lg text-white bg-[#041E42] hover:bg-[#0A2E5C] 
                            border border-transparent shadow-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#041E42]">
                        <span>Continue to Attendance</span>
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
        const eligibleDaysSelect = document.getElementById('eligible_days');
        const presenterTypeSelect = document.getElementById('presenter_type');
        const paymentConfirmedCheckbox = document.getElementById('payment_confirmed');
        const emailField = document.getElementById('email');
        const fullNameField = document.getElementById('full_name');
        
        // Add duplicate participant check functionality
        let typingTimer;
        const doneTypingInterval = 800; // ms
        
        // Set up input event listeners for duplicate checks
        if (emailField) {
            emailField.addEventListener('input', function() {
                clearTimeout(typingTimer);
                if (emailField.value) {
                    typingTimer = setTimeout(checkExistingParticipant, doneTypingInterval);
                }
            });
        }
        
        if (fullNameField) {
            fullNameField.addEventListener('input', function() {
                clearTimeout(typingTimer);
                if (fullNameField.value) {
                    typingTimer = setTimeout(checkExistingParticipant, doneTypingInterval);
                }
            });
        }
        
        // Function to check for existing participants
        function checkExistingParticipant() {
            const email = emailField.value.trim();
            const fullName = fullNameField.value.trim();
            
            if (!email || !fullName) return; // Don't check if either field is empty
            
            console.log('Checking for existing participant:', { email, fullName });
            
            // Show loading indicator
            if (!document.getElementById('duplicate-check-loading')) {
                const loadingEl = document.createElement('div');
                loadingEl.id = 'duplicate-check-loading';
                loadingEl.className = 'text-gray-500 text-sm mt-2';
                loadingEl.innerHTML = 'Checking for existing participants...';
                fullNameField.parentNode.appendChild(loadingEl);
            }
            
            // Clear any existing warning
            const existingWarning = document.getElementById('duplicate-participant-warning');
            if (existingWarning) {
                existingWarning.remove();
            }
            
            // Create form data with proper content type
            const formData = new FormData();
            formData.append('email', email);
            formData.append('full_name', fullName);
            formData.append('_token', '{{ csrf_token() }}');
            
            // Convert form data to URL-encoded format - this is important for Laravel
            const urlEncodedData = new URLSearchParams(formData).toString();
            
            // Call the check-existing endpoint
            fetch('{{ route("usher.registration.check-existing") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest', // Important for Laravel to recognize as AJAX
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: urlEncodedData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Server returned ${response.status}: ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                
                // Remove loading indicator
                const loadingEl = document.getElementById('duplicate-check-loading');
                if (loadingEl) {
                    loadingEl.remove();
                }
                
                // Display warning if participant exists
                if (data.exists) {
                    // Create warning element
                    const warningEl = document.createElement('div');
                    warningEl.id = 'duplicate-participant-warning';
                    warningEl.className = 'bg-yellow-50 border-l-4 border-yellow-400 p-4 mt-4 mb-4 rounded';
                    
                    let warningHTML = `
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700"><strong>Warning:</strong> A participant with the same name or email already exists.</p>
                    `;
                    
                    if (data.participant && data.participant.has_active_ticket) {
                        warningHTML += `<p class="text-sm text-yellow-700 mt-1">This participant already has an active ticket. You may be creating a duplicate registration.</p>`;
                    }
                    
                    warningHTML += `
                                <div class="mt-2">
                                    <button type="button" onclick="viewExistingParticipant(${data.participant.id})" class="text-sm text-yellow-700 font-medium underline hover:text-yellow-600">View Existing Participant</button>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    warningEl.innerHTML = warningHTML;
                    
                    // Insert the warning after the full name field
                    const formGroup = document.querySelector('.grid.grid-cols-1.md\\:grid-cols-2.gap-6');
                    if (formGroup) {
                        formGroup.after(warningEl);
                    } else {
                        // Fallback insertion
                        fullNameField.parentNode.appendChild(warningEl);
                    }
                    
                    // Highlight the fields to make the warning more noticeable
                    fullNameField.classList.add('border-yellow-400');
                    emailField.classList.add('border-yellow-400');
                }
            })
            .catch(error => {
                console.error('Error checking for existing participant:', error);
                
                // Remove loading indicator on error
                const loadingEl = document.getElementById('duplicate-check-loading');
                if (loadingEl) {
                    loadingEl.remove();
                }
                
                // Show error message to user
                const errorEl = document.createElement('div');
                errorEl.id = 'duplicate-check-error';
                errorEl.className = 'bg-red-50 border-l-4 border-red-400 p-4 mt-4 rounded';
                errorEl.innerHTML = `
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">Error checking for duplicate participants. You may continue with registration.</p>
                        </div>
                    </div>
                `;
                fullNameField.parentNode.appendChild(errorEl);
                
                // Remove error message after 5 seconds
                setTimeout(() => {
                    const errorEl = document.getElementById('duplicate-check-error');
                    if (errorEl) {
                        errorEl.remove();
                    }
                }, 5000);
            });
        }
        
        // Set initial payment confirmation status based on payment status
        if (paymentStatusSelect) {
            updatePaymentConfirmation(paymentStatusSelect.value);
            
            // Update payment confirmation when payment status changes
            paymentStatusSelect.addEventListener('change', function() {
                updatePaymentConfirmation(this.value);
            });
        }
        // Function to update payment confirmation checkbox based on payment status
        function updatePaymentConfirmation(status) {
            if (paymentConfirmedCheckbox) {
                if (status === 'Paid via Vabu' || status === 'Paid via M-Pesa' || status === 'Waived') {
                    paymentConfirmedCheckbox.checked = true;
                    paymentConfirmedCheckbox.disabled = true; // Lock it when payment is confirmed
                    
                    // For waived status, always set payment_confirmed to true
                    if (status === 'Waived') {
                        // Waived payments are automatically confirmed
                        const hiddenPaymentConfirmed = document.createElement('input');
                        hiddenPaymentConfirmed.type = 'hidden';
                        hiddenPaymentConfirmed.name = 'payment_confirmed';
                        hiddenPaymentConfirmed.value = '1';
                        
                        // Replace any existing hidden field
                        const existingHidden = document.querySelector('input[type="hidden"][name="payment_confirmed"]');
                        if (existingHidden) {
                            existingHidden.remove();
                        }
                        
                        document.getElementById('registration-form').appendChild(hiddenPaymentConfirmed);
                    }
                } else {
                    paymentConfirmedCheckbox.checked = false;
                    paymentConfirmedCheckbox.disabled = false;
                }
            }
        }
        
        // Make handlePaymentConfirmation available globally
        window.handlePaymentConfirmation = function(checkbox) {
            const paymentStatus = paymentStatusSelect ? paymentStatusSelect.value : '';
            
            // If payment is marked as paid but confirmation is unchecked, show warning
            if (!checkbox.checked && (paymentStatus === 'Paid via Vabu' || paymentStatus === 'Paid via M-Pesa')) {
                alert('Warning: You have selected a paid payment status but have not confirmed the payment.');
            }
        };
        
        const category = "{{ $data['category'] }}";
        
        // Handle payment amount updates for general category
        if (category === 'general' && eligibleDaysSelect) {
            eligibleDaysSelect.addEventListener('change', function() {
                const days = parseInt(this.value);
                let amount = 0;
                switch(days) {
                    case 1:
                        amount = 2500;
                        break;
                    case 2:
                        amount = 4500;
                        break;
                    case 3:
                        amount = 6000;
                        break;
                }
                document.getElementById('payment_amount').value = amount;
            });
        }

        // Handle form submission
        const form = document.getElementById('registration-form');
        form.addEventListener('submit', function(event) {
            const paidCategories = ['general', 'exhibitor', 'presenter'];
            const paymentStatus = paymentStatusSelect ? paymentStatusSelect.value : null;
            
            // Only validate payment status for paid categories
            if (paidCategories.includes(category)) {
                if (!paymentStatus) {
                    event.preventDefault();
                    alert('Please select a payment status.');
                    return;
                }
                
                // Additional validation for general category
                if (category === 'general') {
                    const eligibleDays = eligibleDaysSelect ? eligibleDaysSelect.value : null;
                    if (!eligibleDays && paymentStatus !== 'Waived') {
                        event.preventDefault();
                        alert('Please select the number of eligible days.');
                        return;
                    }
                }
                
                // Additional validation for presenters
                if (category === 'presenter') {
                    const presenterType = presenterTypeSelect ? presenterTypeSelect.value : null;
                    if (!presenterType) {
                        event.preventDefault();
                        alert('Please select the presenter type.');
                        return;
                    }
                }
            }
        });
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
        
        // Set the transaction code in the hidden field
        document.getElementById('transaction_code').value = mpesaCode;
        
        // Add the transaction code to notes as well
        const notesField = document.getElementById('payment_notes');
        if (notesField) {
            notesField.value += `\nM-Pesa Transaction: ${mpesaCode}`;
        }
        
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
    
    // Function to view existing participant
    function viewExistingParticipant(participantId) {
        window.open(`{{ url('usher/participant') }}/${participantId}/view`, '_blank');
    }

    function closePaymentModal() {
        const paymentModal = document.getElementById('payment-modal');
        if (paymentModal) {
            paymentModal.classList.add('hidden');
        }
    }
</script>
@endpush 