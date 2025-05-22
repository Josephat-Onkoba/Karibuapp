@extends('layouts.app')

@section('title', 'Participant Registration')

@section('content')
<script>
    // Global state for the form
    let formCurrentStep = 1;
    const formTotalSteps = 6;
    
    // Global functions for button onclick handlers
    function nextStep() {
        console.log("Global nextStep called");
        const event = new Event('click');
        document.getElementById('next-step').dispatchEvent(event);
    }
    
    function prevStep() {
        console.log("Global prevStep called");
        const event = new Event('click');
        document.getElementById('prev-step').dispatchEvent(event);
    }
</script>

<div class="container mx-auto px-4 py-6">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-[#041E42]">Participant Registration</h1>
                <p class="text-gray-600 mt-1">Register and check-in new participants</p>
            </div>
        </div>
    </div>

    <!-- Multi-step Form -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
        <!-- Steps Progress -->
        <div class="px-4 py-5 sm:p-6 bg-gray-50 border-b border-gray-200">
            <div class="max-w-4xl mx-auto">
                <div class="flex items-center justify-between">
                    <div class="w-full flex items-center">
                        <div class="relative flex flex-col items-center text-center">
                            <div class="rounded-full h-10 w-10 flex items-center justify-center bg-[#041E42] text-white z-10 text-sm step-circle" id="step-circle-1">
                                1
                            </div>
                            <div class="text-xs mt-2 font-medium text-[#041E42] step-text" id="step-text-1">Category</div>
                        </div>
                        <div class="flex-1 h-1 bg-gray-200 step-line" id="step-line-1">
                            <div class="h-1 bg-[#041E42] step-line-progress" style="width: 0%"></div>
                        </div>
                        
                        <div class="relative flex flex-col items-center text-center">
                            <div class="rounded-full h-10 w-10 flex items-center justify-center bg-gray-300 text-white z-10 text-sm step-circle" id="step-circle-2">
                                2
                            </div>
                            <div class="text-xs mt-2 font-medium text-gray-500 step-text" id="step-text-2">Details</div>
                        </div>
                        <div class="flex-1 h-1 bg-gray-200 step-line" id="step-line-2">
                            <div class="h-1 bg-[#041E42] step-line-progress" style="width: 0%"></div>
                        </div>
                        
                        <div class="relative flex flex-col items-center text-center">
                            <div class="rounded-full h-10 w-10 flex items-center justify-center bg-gray-300 text-white z-10 text-sm step-circle" id="step-circle-3">
                                3
                            </div>
                            <div class="text-xs mt-2 font-medium text-gray-500 step-text" id="step-text-3">Attendance</div>
                        </div>
                        <div class="flex-1 h-1 bg-gray-200 step-line" id="step-line-3">
                            <div class="h-1 bg-[#041E42] step-line-progress" style="width: 0%"></div>
                        </div>
                        
                        <div class="relative flex flex-col items-center text-center">
                            <div class="rounded-full h-10 w-10 flex items-center justify-center bg-gray-300 text-white z-10 text-sm step-circle" id="step-circle-4">
                                4
                            </div>
                            <div class="text-xs mt-2 font-medium text-gray-500 step-text" id="step-text-4">Ticket</div>
                        </div>
                        <div class="flex-1 h-1 bg-gray-200 step-line" id="step-line-4">
                            <div class="h-1 bg-[#041E42] step-line-progress" style="width: 0%"></div>
                        </div>
                        
                        <div class="relative flex flex-col items-center text-center">
                            <div class="rounded-full h-10 w-10 flex items-center justify-center bg-gray-300 text-white z-10 text-sm step-circle" id="step-circle-5">
                                5
                            </div>
                            <div class="text-xs mt-2 font-medium text-gray-500 step-text" id="step-text-5">Check-in</div>
                        </div>
                        <div class="flex-1 h-1 bg-gray-200 step-line" id="step-line-5">
                            <div class="h-1 bg-[#041E42] step-line-progress" style="width: 0%"></div>
                        </div>
                        
                        <div class="relative flex flex-col items-center text-center">
                            <div class="rounded-full h-10 w-10 flex items-center justify-center bg-gray-300 text-white z-10 text-sm step-circle" id="step-circle-6">
                                6
                            </div>
                            <div class="text-xs mt-2 font-medium text-gray-500 step-text" id="step-text-6">Complete</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Form Content -->
        <form id="registration-form" method="POST" action="{{ route('usher.registration.store') }}" class="px-4 py-5 sm:p-6 max-w-4xl mx-auto">
            @csrf
            
            <!-- Step 1: Select Participant Category -->
            <div class="step-content" id="step-1">
                <h2 class="text-xl font-bold text-[#041E42] mb-6">Step 1: Select Participant Category</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($categories as $key => $category)
                    <div class="border rounded-lg p-4 hover:bg-gray-50 transition-all duration-300" onclick="selectCategoryDirect('{{ $key }}')">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <input type="radio" name="category" value="{{ $key }}" id="category-{{ $key }}" class="h-5 w-5 text-[#041E42] focus:ring-[#041E42] mt-1" required>
                            </div>
                            <label for="category-{{ $key }}" class="ml-3 cursor-pointer block">
                                <div class="text-lg font-semibold text-gray-900">{{ $category }}</div>
                                <p class="text-gray-600 text-sm">{{ $categoryDescriptions[$key] }}</p>
                            </label>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            
            <!-- Step 2: Enter Participant Details -->
            <div class="step-content hidden" id="step-2">
                <h2 class="text-xl font-bold text-[#041E42] mb-6">Step 2: Participant Details</h2>
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="full_name" class="block text-sm font-medium text-gray-700">Full Name</label>
                            <input type="text" name="full_name" id="full_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#041E42] focus:ring-[#041E42]" required>
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                            <input type="email" name="email" id="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#041E42] focus:ring-[#041E42]" required>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="phone_number" class="block text-sm font-medium text-gray-700">Phone Number</label>
                            <input type="tel" name="phone_number" id="phone_number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#041E42] focus:ring-[#041E42]" required>
                        </div>
                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                            <select name="role" id="role" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#041E42] focus:ring-[#041E42]" required>
                                <option value="">Select a role</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="job_title" class="block text-sm font-medium text-gray-700">Job Title</label>
                            <input type="text" name="job_title" id="job_title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#041E42] focus:ring-[#041E42]">
                        </div>
                        <div>
                            <label for="organization" class="block text-sm font-medium text-gray-700">Organization</label>
                            <input type="text" name="organization" id="organization" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#041E42] focus:ring-[#041E42]">
                        </div>
                    </div>
                    
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                        <textarea name="notes" id="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#041E42] focus:ring-[#041E42]"></textarea>
                    </div>
                </div>
            </div>
            
            <!-- Step 3: Select Attendance Days -->
            <div class="step-content hidden" id="step-3">
                <h2 class="text-xl font-bold text-[#041E42] mb-6">Step 3: Attendance Days</h2>
                <div class="space-y-4">
                    <p class="text-gray-600">Select which conference days the participant will attend:</p>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        @forelse($conferenceDays as $day)
                        <div class="border rounded-lg p-4 hover:border-[#041E42] cursor-pointer">
                            <label class="flex items-start cursor-pointer">
                                <input type="checkbox" name="attendance_days[]" value="{{ $day->id }}" class="rounded border-gray-300 text-[#041E42] focus:ring-[#041E42] h-5 w-5 mt-0.5">
                                <div class="ml-3">
                                    <span class="block font-medium text-gray-900">{{ $day->name }}</span>
                                    <span class="block text-sm text-gray-500">{{ $day->date->format('F j, Y') }}</span>
                                    @if($day->date->isToday())
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 mt-1">Today</span>
                                    @endif
                                </div>
                            </label>
                        </div>
                        @empty
                        <div class="col-span-3 text-center py-8">
                            <div class="text-gray-500">No conference days have been set up.</div>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
            
            <!-- Step 4: Generate Ticket -->
            <div class="step-content hidden" id="step-4">
                <h2 class="text-xl font-bold text-[#041E42] mb-6">Step 4: Generate Ticket</h2>
                <div class="mx-auto max-w-lg">
                    <div class="border rounded-lg p-6 bg-gray-50 mb-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900" id="preview-name"></h3>
                                <p class="text-gray-600 text-sm" id="preview-email"></p>
                                <p class="text-gray-600 text-sm" id="preview-phone"></p>
                            </div>
                            <div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800" id="preview-category"></span>
                            </div>
                        </div>
                        
                        <div class="border-t border-dashed my-4 pt-4">
                            <div class="flex justify-between text-sm text-gray-600 mb-2">
                                <span>Role:</span>
                                <span id="preview-role"></span>
                            </div>
                            <div class="flex justify-between text-sm text-gray-600 mb-2">
                                <span>Organization:</span>
                                <span id="preview-org"></span>
                            </div>
                        </div>
                        
                        <div class="border-t border-dashed my-4 pt-4">
                            <div class="mb-2">
                                <span class="text-sm text-gray-600">Valid for:</span>
                                <div id="preview-days" class="mt-1 space-y-1"></div>
                            </div>
                        </div>
                        
                        <div class="mt-4 flex justify-center">
                            <div class="bg-white p-3 rounded-lg border">
                                <div class="text-center text-xs text-gray-600 mb-1">Your ticket will be generated with a unique ID</div>
                                <div class="text-lg font-bold text-[#041E42] text-center" id="ticket-placeholder">KC-YYYYMMDD-XXXXXX</div>
                            </div>
                        </div>
                    </div>
                    
                    <p class="text-sm text-gray-600 text-center">This ticket will be generated after submission and will be valid only for the selected attendance days.</p>
                </div>
            </div>
            
            <!-- Step 5: Check-in -->
            <div class="step-content hidden" id="step-5">
                <h2 class="text-xl font-bold text-[#041E42] mb-6">Step 5: Check-in</h2>
                <div class="mx-auto max-w-lg">
                    <div class="border rounded-lg p-6 mb-6">
                        <div class="flex flex-col items-center">
                            <div class="mb-4 rounded-full bg-green-100 p-3">
                                <i data-lucide="check-circle" class="h-8 w-8 text-green-600"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Check-in for Today</h3>
                            <p class="text-gray-600 text-sm text-center mb-4">Would you like to check in this participant for today's conference day?</p>
                            
                            <div class="flex space-x-4">
                                <button type="button" id="checkin-yes" class="bg-[#041E42] hover:bg-[#0A2E5C] text-white px-4 py-2 rounded-md">Yes, Check In Now</button>
                                <button type="button" id="checkin-no" class="border border-[#041E42] text-[#041E42] hover:bg-gray-50 px-4 py-2 rounded-md">No, Skip Check-in</button>
                            </div>
                        </div>
                    </div>
                    
                    <div id="checkin-result" class="hidden">
                        <div class="flex items-center mb-4">
                            <div class="rounded-full bg-green-100 p-2 mr-3">
                                <i data-lucide="check" class="h-5 w-5 text-green-600"></i>
                            </div>
                            <div>
                                <p class="font-medium">Ready to complete registration</p>
                                <p class="text-sm text-gray-600" id="checkin-status"></p>
                            </div>
                        </div>
                    </div>
                    
                    <input type="hidden" name="checkin_today" id="checkin_today" value="0">
                </div>
            </div>
            
            <!-- Step 6: Complete -->
            <div class="step-content hidden" id="step-6">
                <div class="text-center py-8">
                    <div class="mx-auto w-16 h-16 rounded-full bg-green-100 flex items-center justify-center mb-4">
                        <i data-lucide="check" class="h-8 w-8 text-green-600"></i>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 mb-2">Ready to Complete Registration</h2>
                    <p class="text-gray-600 max-w-md mx-auto mb-6">Please review all the information before submitting. You will be able to print the ticket after submission.</p>
                    <div class="inline-flex rounded-md shadow">
                        <button type="submit" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-[#041E42] hover:bg-[#0A2E5C] transition-colors">
                            Complete Registration
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Navigation Buttons -->
            <div class="mt-8 border-t border-gray-200 pt-5">
                <div class="flex justify-between">
                    <button type="button" id="prev-step" onclick="prevStep()" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50">
                        <i data-lucide="arrow-left" class="h-4 w-4 mr-2"></i>
                        Previous
                    </button>
                    <button type="button" id="next-step" onclick="nextStep()" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-[#041E42] hover:bg-[#0A2E5C]">
                        Next
                        <i data-lucide="arrow-right" class="h-4 w-4 ml-2"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let currentStep = 1;
        const totalSteps = 6;
        
        // DOM elements - using robust selectors
        const prevBtn = document.querySelector('#prev-step');
        const nextBtn = document.querySelector('#next-step');
        const form = document.querySelector('#registration-form');
        
        console.log('Initialized step form. Next button:', nextBtn ? 'Found' : 'Not found');
        
        // Direct category selection
        document.querySelectorAll('.category-card').forEach(card => {
            card.addEventListener('click', function() {
                // Get the radio input inside this card
                const radio = this.querySelector('input[type="radio"]');
                if (radio) {
                    // Clear all selections
                    document.querySelectorAll('.category-card').forEach(c => {
                        c.classList.remove('border-[#041E42]', 'ring-2', 'ring-[#041E42]', 'bg-blue-50');
                        const check = c.querySelector('.category-check');
                        if (check) check.classList.add('hidden');
                    });
                    
                    // Mark this one as selected
                    radio.checked = true;
                    this.classList.add('border-[#041E42]', 'ring-2', 'ring-[#041E42]', 'bg-blue-50');
                    const check = this.querySelector('.category-check');
                    if (check) check.classList.remove('hidden');
                    
                    console.log('Category selected:', radio.value);
                    
                    // Load roles for this category
                    const category = radio.value;
                    fetch(`{{ route('usher.registration.roles') }}?category=${category}`)
                        .then(response => response.json())
                        .then(data => {
                            const roleSelect = document.getElementById('role');
                            roleSelect.innerHTML = '<option value="">Select a role</option>';
                            
                            data.roles.forEach(role => {
                                const option = document.createElement('option');
                                option.value = role;
                                option.textContent = role;
                                roleSelect.appendChild(option);
                            });
                        });
                }
            });
        });
        
        // Add direct onclick handlers to ensure they work
        if (nextBtn) {
            console.log('Adding click handler to Next button');
            nextBtn.onclick = function() {
                console.log('Next button clicked');
                goToNextStep();
            };
        }
        
        if (prevBtn) {
            prevBtn.onclick = function() {
                console.log('Previous button clicked');
                goToPrevStep();
            };
        }
        
        // Initialize buttons
        updateButtonStates();
        
        // Check-in choice
        const checkinYes = document.getElementById('checkin-yes');
        const checkinNo = document.getElementById('checkin-no');
        
        if (checkinYes) {
            checkinYes.addEventListener('click', function() {
                document.getElementById('checkin_today').value = '1';
                document.getElementById('checkin-status').textContent = 'Participant will be checked in for today.';
                document.getElementById('checkin-result').classList.remove('hidden');
                goToNextStep();
            });
        }
        
        if (checkinNo) {
            checkinNo.addEventListener('click', function() {
                document.getElementById('checkin_today').value = '0';
                document.getElementById('checkin-status').textContent = 'Participant will not be checked in for today.';
                document.getElementById('checkin-result').classList.remove('hidden');
                goToNextStep();
            });
        }
        
        // Form validation functions
        function validateStep1() {
            const categorySelected = document.querySelector('input[name="category"]:checked');
            console.log('Step 1 validation:', categorySelected ? 'Category selected' : 'No category selected');
            return !!categorySelected;
        }
        
        function validateStep2() {
            const requiredFields = ['full_name', 'email', 'phone_number', 'role'];
            const valid = requiredFields.every(field => {
                const input = document.getElementById(field);
                return input && input.value.trim() !== '';
            });
            console.log('Step 2 validation:', valid ? 'All fields filled' : 'Missing required fields');
            return valid;
        }
        
        function validateStep3() {
            const attendanceDays = document.querySelectorAll('input[name="attendance_days[]"]:checked');
            console.log('Step 3 validation:', attendanceDays.length > 0 ? 'Days selected' : 'No days selected');
            return attendanceDays.length > 0;
        }
        
        // Step validation before proceeding
        function validateCurrentStep() {
            console.log('Validating step:', currentStep);
            switch(currentStep) {
                case 1:
                    return validateStep1();
                case 2:
                    return validateStep2();
                case 3:
                    return validateStep3();
                default:
                    return true;
            }
        }
        
        // Update ticket preview in step 4
        function updateTicketPreview() {
            console.log('Updating ticket preview');
            const fullName = document.getElementById('full_name').value;
            const email = document.getElementById('email').value;
            const phone = document.getElementById('phone_number').value;
            const role = document.getElementById('role').value;
            const org = document.getElementById('organization').value || 'Not specified';
            
            const categoryInput = document.querySelector('input[name="category"]:checked');
            const categoryLabel = categoryInput && document.querySelector(`label[for="category-${categoryInput.value}"] div`) 
                ? document.querySelector(`label[for="category-${categoryInput.value}"] div`).textContent.trim() 
                : '';
            
            document.getElementById('preview-name').textContent = fullName;
            document.getElementById('preview-email').textContent = email;
            document.getElementById('preview-phone').textContent = phone;
            document.getElementById('preview-role').textContent = role;
            document.getElementById('preview-org').textContent = org;
            document.getElementById('preview-category').textContent = categoryLabel;
            
            // Selected days
            const daysContainer = document.getElementById('preview-days');
            daysContainer.innerHTML = '';
            
            document.querySelectorAll('input[name="attendance_days[]"]:checked').forEach(checkbox => {
                const dayLabel = checkbox.closest('label').querySelector('span:first-child').textContent;
                const dayDate = checkbox.closest('label').querySelector('span:nth-child(2)').textContent;
                
                const dayElement = document.createElement('div');
                dayElement.className = 'flex items-center';
                dayElement.innerHTML = `
                    <i data-lucide="calendar" class="h-4 w-4 mr-2 text-[#041E42]"></i>
                    <span class="text-sm font-medium">${dayLabel} - ${dayDate}</span>
                `;
                daysContainer.appendChild(dayElement);
            });
            
            // Re-init Lucide icons for dynamically added elements
            if (typeof lucide !== 'undefined' && lucide.createIcons) {
                lucide.createIcons();
            }
        }
        
        // Previous button click handler
        function goToPrevStep() {
            console.log('Going to previous step from', currentStep);
            if (currentStep > 1) {
                updateStepUI(currentStep, false);
                currentStep--;
                updateStepUI(currentStep, true);
                updateButtonStates();
                console.log('Now at step', currentStep);
            }
        }
        
        // Next button click handler
        function goToNextStep() {
            console.log('Attempting to go to next step from', currentStep);
            if (!validateCurrentStep()) {
                console.log('Validation failed for step', currentStep);
                alert('Please complete all required fields before proceeding.');
                return;
            }
            
            if (currentStep < totalSteps) {
                console.log('Moving from step', currentStep, 'to', currentStep + 1);
                updateStepUI(currentStep, false);
                currentStep++;
                updateStepUI(currentStep, true);
                updateButtonStates();
                console.log('Now at step', currentStep);
                
                // If moving to step 4, update the ticket preview
                if (currentStep === 4) {
                    updateTicketPreview();
                }
            }
        }
        
        // Update the UI for the given step
        function updateStepUI(step, isActive) {
            console.log('Updating UI for step', step, isActive ? 'active' : 'inactive');
            const stepContent = document.getElementById(`step-${step}`);
            const stepCircle = document.getElementById(`step-circle-${step}`);
            const stepText = document.getElementById(`step-text-${step}`);
            
            if (!stepContent) {
                console.error('Step content not found for step', step);
                return;
            }
            
            if (isActive) {
                stepContent.classList.remove('hidden');
                if (stepCircle) {
                    stepCircle.classList.add('bg-[#041E42]');
                    stepCircle.classList.remove('bg-gray-300');
                }
                if (stepText) {
                    stepText.classList.add('text-[#041E42]');
                    stepText.classList.remove('text-gray-500');
                }
            } else {
                stepContent.classList.add('hidden');
            }
            
            // Update progress lines
            if (step < totalSteps) {
                const progressLine = document.querySelector(`#step-line-${step} .step-line-progress`);
                if (progressLine) {
                    progressLine.style.width = isActive ? '0%' : '100%';
                }
            }
            
            // Mark previous steps as completed
            for (let i = 1; i < step; i++) {
                const prevStepCircle = document.getElementById(`step-circle-${i}`);
                const prevStepText = document.getElementById(`step-text-${i}`);
                const prevStepLine = document.querySelector(`#step-line-${i} .step-line-progress`);
                
                if (prevStepCircle) prevStepCircle.classList.add('bg-[#041E42]');
                if (prevStepCircle) prevStepCircle.classList.remove('bg-gray-300');
                if (prevStepText) prevStepText.classList.add('text-[#041E42]');
                if (prevStepText) prevStepText.classList.remove('text-gray-500');
                if (prevStepLine) prevStepLine.style.width = '100%';
            }
        }
        
        // Update the next/prev button states
        function updateButtonStates() {
            console.log('Updating button states for step', currentStep);
            if (prevBtn) {
                prevBtn.disabled = currentStep === 1;
                prevBtn.classList.toggle('opacity-50', currentStep === 1);
            }
            
            if (nextBtn) {
                if (currentStep === totalSteps) {
                    nextBtn.classList.add('hidden');
                } else {
                    nextBtn.classList.remove('hidden');
                }
            }
        }
    });
</script>
@endpush 