@extends('layouts.app')

@section('title', 'Edit ' . $title)

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex items-center mb-2">
            <a href="{{ route('admin.participants.category', $category) }}" class="text-[#041E42] hover:text-[#0A2E5C] mr-3 flex items-center">
                <i data-lucide="arrow-left" class="h-5 w-5 mr-1"></i>
                <span>Back</span>
            </a>
        </div>
        <h1 class="text-2xl md:text-3xl font-bold text-[#041E42]">Edit {{ $title }}</h1>
        <p class="text-gray-600 mt-1">Update participant information</p>
    </div>

    <!-- Form Container -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200 max-w-3xl mx-auto">
        <div class="px-6 py-4 bg-[#041E42] text-white">
            <h2 class="font-bold text-xl">Participant Information</h2>
            <p class="text-white/80 text-sm">Please fill in all required fields</p>
        </div>

        <form action="{{ route('admin.participants.update', ['category' => $category, 'id' => $participant->id]) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')
            
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
                <!-- Basic Information -->
                <div class="border-b border-gray-200 pb-6">
                    <div class="flex items-center mb-4">
                        <div class="w-8 h-8 rounded-full bg-[#041E42] text-white flex items-center justify-center mr-3">
                            <i data-lucide="user" class="h-4 w-4"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800">Basic Information</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="full_name" class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-red-600">*</span></label>
                            <input type="text" id="full_name" name="full_name" value="{{ old('full_name', $participant->full_name) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-[#041E42] focus:border-[#041E42]" placeholder="Enter full name">
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address <span class="text-red-600">*</span></label>
                            <input type="email" id="email" name="email" value="{{ old('email', $participant->email) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-[#041E42] focus:border-[#041E42]" placeholder="Enter email address">
                        </div>

                        <div>
                            <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-1">Phone Number <span class="text-red-600">*</span></label>
                            <input type="text" id="phone_number" name="phone_number" value="{{ old('phone_number', $participant->phone_number) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-[#041E42] focus:border-[#041E42]" placeholder="e.g., +254712345678">
                        </div>

                        <div>
                            <label for="organization" class="block text-sm font-medium text-gray-700 mb-1">Organization</label>
                            <input type="text" id="organization" name="organization" value="{{ old('organization', $participant->organization) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-[#041E42] focus:border-[#041E42]" placeholder="Enter organization name">
                        </div>
                    </div>
                </div>

                <!-- Role Information -->
                <div class="border-b border-gray-200 pb-6">
                    <div class="flex items-center mb-4">
                        <div class="w-8 h-8 rounded-full bg-[#041E42] text-white flex items-center justify-center mr-3">
                            <i data-lucide="briefcase" class="h-4 w-4"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800">Role Information</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role <span class="text-red-600">*</span></label>
                            <select id="role" name="role" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-[#041E42] focus:border-[#041E42]">
                                @foreach($roles as $roleOption)
                                    <option value="{{ strtolower(str_replace(' ', '_', $roleOption)) }}" {{ old('role', $participant->role) == strtolower(str_replace(' ', '_', $roleOption)) ? 'selected' : '' }}>
                                        {{ $roleOption }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Conditional Fields based on Role -->
                        <div id="student_fields" class="{{ $participant->role == 'student' ? '' : 'hidden' }}">
                            <label for="student_admission_number" class="block text-sm font-medium text-gray-700 mb-1">Student Admission Number <span class="text-red-600">*</span></label>
                            <input type="text" id="student_admission_number" name="student_admission_number" value="{{ old('student_admission_number', $participant->student_admission_number) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-[#041E42] focus:border-[#041E42]" placeholder="Enter admission number">
                        </div>

                        <div id="staff_fields" class="{{ $participant->role == 'staff' ? '' : 'hidden' }}">
                            <label for="staff_number" class="block text-sm font-medium text-gray-700 mb-1">Staff Number <span class="text-red-600">*</span></label>
                            <input type="text" id="staff_number" name="staff_number" value="{{ old('staff_number', $participant->staff_number) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-[#041E42] focus:border-[#041E42]" placeholder="Enter staff number">
                        </div>

                        <div id="presenter_fields" class="{{ $participant->role == 'presenter' ? '' : 'hidden' }}">
                            <label for="presenter_type" class="block text-sm font-medium text-gray-700 mb-1">Presenter Type <span class="text-red-600">*</span></label>
                            <select id="presenter_type" name="presenter_type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-[#041E42] focus:border-[#041E42]">
                                <option value="student" {{ old('presenter_type', $participant->presenter_type) == 'student' ? 'selected' : '' }}>Student</option>
                                <option value="non_student" {{ old('presenter_type', $participant->presenter_type) == 'non_student' ? 'selected' : '' }}>Non-Student</option>
                                <option value="international" {{ old('presenter_type', $participant->presenter_type) == 'international' ? 'selected' : '' }}>International</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Payment Information -->
                <div class="border-b border-gray-200 pb-6">
                    <div class="flex items-center mb-4">
                        <div class="w-8 h-8 rounded-full bg-[#041E42] text-white flex items-center justify-center mr-3">
                            <i data-lucide="credit-card" class="h-4 w-4"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800">Payment Information</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="payment_status" class="block text-sm font-medium text-gray-700 mb-1">Payment Status <span class="text-red-600">*</span></label>
                            <select id="payment_status" name="payment_status" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-[#041E42] focus:border-[#041E42]">
                                <option value="pending" {{ old('payment_status', $participant->payment_status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="paid" {{ old('payment_status', $participant->payment_status) == 'paid' ? 'selected' : '' }}>Paid</option>
                                <option value="exempted" {{ old('payment_status', $participant->payment_status) == 'exempted' ? 'selected' : '' }}>Exempted</option>
                            </select>
                        </div>

                        <div id="payment_amount_field" class="{{ old('payment_status', $participant->payment_status) != 'exempted' ? '' : 'hidden' }}">
                            <label for="payment_amount" class="block text-sm font-medium text-gray-700 mb-1">Payment Amount (KES)</label>
                            <input type="number" id="payment_amount" name="payment_amount" value="{{ old('payment_amount', $participant->payment_amount) }}" min="0" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-[#041E42] focus:border-[#041E42]" placeholder="Enter amount">
                        </div>

                        <div class="col-span-2">
                            <div class="flex items-center">
                                <input type="checkbox" id="payment_confirmed" name="payment_confirmed" value="1" {{ old('payment_confirmed', $participant->payment_confirmed) ? 'checked' : '' }} class="h-4 w-4 text-[#041E42] focus:ring-[#041E42] border-gray-300 rounded">
                                <label for="payment_confirmed" class="ml-2 block text-sm text-gray-700">Payment confirmed and verified</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Eligibility Information -->
                <div class="pb-6">
                    <div class="flex items-center mb-4">
                        <div class="w-8 h-8 rounded-full bg-[#041E42] text-white flex items-center justify-center mr-3">
                            <i data-lucide="calendar" class="h-4 w-4"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800">Conference Eligibility</h3>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Eligible Days <span class="text-red-600">*</span></label>
                        <input type="hidden" name="eligible_days" id="eligible_days" value="{{ old('eligible_days', $participant->eligible_days) }}">
                        
                        <div class="space-y-2">
                            @foreach($conferenceDays as $day)
                                <div class="flex items-center">
                                    <input type="checkbox" 
                                        class="eligibility-day h-4 w-4 text-[#041E42] focus:ring-[#041E42] border-gray-300 rounded"
                                        value="{{ $day->id }}" 
                                        id="day_{{ $day->id }}"
                                        {{ in_array($day->id, explode(',', old('eligible_days', $participant->eligible_days))) ? 'checked' : '' }}
                                        >
                                    <label for="day_{{ $day->id }}" class="ml-2 block text-sm text-gray-700">
                                        {{ \Carbon\Carbon::parse($day->date)->format('l, F j, Y') }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="pt-6 flex justify-end space-x-3">
                    <a href="{{ route('admin.participants.category', $category) }}" class="px-5 py-3 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#041E42]">
                        Cancel
                    </a>
                    <button type="submit" class="px-5 py-3 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-[#041E42] hover:bg-[#0A2E5C] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#041E42] flex items-center">
                        <i data-lucide="save" class="h-4 w-4 mr-2"></i>
                        Update Participant
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle role changes
        const roleSelect = document.getElementById('role');
        const studentFields = document.getElementById('student_fields');
        const staffFields = document.getElementById('staff_fields');
        const presenterFields = document.getElementById('presenter_fields');
        
        roleSelect.addEventListener('change', function() {
            // Hide all conditional fields first
            studentFields.classList.add('hidden');
            staffFields.classList.add('hidden');
            presenterFields.classList.add('hidden');
            
            // Show fields based on selected role
            if (this.value === 'student') {
                studentFields.classList.remove('hidden');
            } else if (this.value === 'staff') {
                staffFields.classList.remove('hidden');
            } else if (this.value === 'presenter') {
                presenterFields.classList.remove('hidden');
            }
        });
        
        // Handle payment status changes
        const paymentStatusSelect = document.getElementById('payment_status');
        const paymentAmountField = document.getElementById('payment_amount_field');
        
        paymentStatusSelect.addEventListener('change', function() {
            if (this.value === 'exempted') {
                paymentAmountField.classList.add('hidden');
            } else {
                paymentAmountField.classList.remove('hidden');
            }
        });
        
        // Handle eligibility days
        const eligibilityCheckboxes = document.querySelectorAll('.eligibility-day');
        const eligibilityInput = document.getElementById('eligible_days');
        
        function updateEligibilityDays() {
            const selectedDays = [];
            eligibilityCheckboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    selectedDays.push(checkbox.value);
                }
            });
            eligibilityInput.value = selectedDays.join(',');
        }
        
        eligibilityCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateEligibilityDays);
        });
        
        // Initial update
        updateEligibilityDays();
    });
</script>
@endpush
@endsection
