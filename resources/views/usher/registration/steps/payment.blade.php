@extends('layouts.app')

@section('title', 'Registration - Payment')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-[#041E42]">Registration</h1>
                <p class="text-gray-600 mt-1">Payment Information</p>
            </div>
        </div>
    </div>

    <!-- Steps Progress -->
    <div class="mb-8">
        <div class="max-w-4xl mx-auto">
            <div class="relative">
                <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-gray-200">
                    <div class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-[#041E42] w-3/5"></div>
                </div>
                <div class="flex text-xs text-gray-600 mt-1 justify-between">
                    <div>Category</div>
                    <div>Details</div>
                    <div class="text-[#041E42] font-semibold">Payment</div>
                    <div>Attendance</div>
                    <div>Ticket</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Content -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
        <div class="p-6">
            <form action="{{ route('usher.registration.process_payment') }}" method="POST">
                @csrf
                
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-bold text-gray-700 mb-2">Payment Required</h3>
                        <p class="text-gray-600 mb-4">Please complete the payment before continuing with the registration process.</p>
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700">
                                        To proceed with the registration, {{ $data['full_name'] ?? 'this participant' }} needs to complete payment.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="border-t border-gray-200 pt-6">
                        <h4 class="text-md font-semibold text-gray-700 mb-4">Select Payment Method</h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div class="border rounded-lg p-4 hover:shadow-md transition-shadow duration-200 @error('payment_method') border-red-500 @enderror">
                                <label class="flex items-start cursor-pointer">
                                    <input type="radio" name="payment_method" value="m_pesa" class="mt-1 h-5 w-5 text-[#041E42] border-gray-300 focus:ring-[#041E42]" {{ old('payment_method') == 'm_pesa' ? 'checked' : '' }}>
                                    <div class="ml-3">
                                        <span class="block font-medium text-gray-900">M-Pesa Payment</span>
                                        <span class="block text-sm text-gray-500">Pay using M-Pesa mobile money service</span>
                                        <div class="mt-2" id="mpesa-details" class="{{ old('payment_method') != 'm_pesa' ? 'hidden' : '' }}">
                                            <div class="bg-gray-50 rounded p-3 my-2">
                                                <p class="text-sm font-medium">Pay Bill Number: <span class="text-[#041E42]">123456</span></p>
                                                <p class="text-sm font-medium">Account Number: <span class="text-[#041E42]">KaribuConf-{{ substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 6) }}</span></p>
                                                <p class="text-sm font-medium">Amount: <span class="text-[#041E42]">Ksh 2,500</span></p>
                                            </div>
                                            <div class="mt-3">
                                                <label for="transaction_code" class="block text-gray-700 font-medium mb-1">Transaction Code <span class="text-red-500">*</span></label>
                                                <input 
                                                    type="text" 
                                                    name="transaction_code" 
                                                    id="transaction_code" 
                                                    value="{{ old('transaction_code') }}"
                                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#041E42]"
                                                    placeholder="e.g., QWE123456"
                                                >
                                                @error('transaction_code')
                                                <div class="text-red-500 mt-1 text-sm">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            
                            <div class="border rounded-lg p-4 hover:shadow-md transition-shadow duration-200">
                                <label class="flex items-start cursor-pointer">
                                    <input type="radio" name="payment_method" value="vabu" class="mt-1 h-5 w-5 text-[#041E42] border-gray-300 focus:ring-[#041E42]" {{ old('payment_method') == 'vabu' ? 'checked' : '' }}>
                                    <div class="ml-3">
                                        <span class="block font-medium text-gray-900">Vabu Payment</span>
                                        <span class="block text-sm text-gray-500">Pay using the Vabu payment system</span>
                                        <div class="mt-2" id="vabu-details" class="{{ old('payment_method') != 'vabu' ? 'hidden' : '' }}">
                                            <div class="bg-gray-50 rounded p-3">
                                                <p class="text-sm text-gray-600">By selecting this option, payment will be recorded as paid via Vabu system. This will be verified by the finance team.</p>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <label for="payment_confirmed" class="flex items-center text-gray-700 font-medium">
                                <input 
                                    type="checkbox" 
                                    name="payment_confirmed" 
                                    id="payment_confirmed" 
                                    value="1" 
                                    {{ old('payment_confirmed') ? 'checked' : '' }}
                                    class="focus:ring-[#041E42] h-5 w-5 text-[#041E42] border-gray-300 rounded mr-2"
                                >
                                <span>Payment Confirmed</span>
                            </label>
                            <p class="text-sm text-gray-500 mt-1 ml-7">Check this box only if payment has been verified</p>
                        </div>
                    </div>
                </div>
                
                <div class="mt-8 pt-5 border-t border-gray-200">
                    <div class="flex justify-between">
                        <a href="{{ route('usher.registration.step2') }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 -ml-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            Back to Participant Details
                        </a>
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-[#041E42] hover:bg-[#0A2E5C] focus:outline-none">
                            Complete Payment & Continue
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mpesaRadio = document.querySelector('input[value="m_pesa"]');
        const vabuRadio = document.querySelector('input[value="vabu"]');
        const mpesaDetails = document.getElementById('mpesa-details');
        const vabuDetails = document.getElementById('vabu-details');
        
        // Set initial state
        if (mpesaRadio.checked) {
            mpesaDetails.classList.remove('hidden');
            vabuDetails.classList.add('hidden');
        } else if (vabuRadio.checked) {
            mpesaDetails.classList.add('hidden');
            vabuDetails.classList.remove('hidden');
        } else {
            // Default state (both hidden until one is selected)
            mpesaDetails.classList.add('hidden');
            vabuDetails.classList.add('hidden');
        }
        
        mpesaRadio.addEventListener('change', function() {
            if (this.checked) {
                mpesaDetails.classList.remove('hidden');
                vabuDetails.classList.add('hidden');
            }
        });
        
        vabuRadio.addEventListener('change', function() {
            if (this.checked) {
                mpesaDetails.classList.add('hidden');
                vabuDetails.classList.remove('hidden');
            }
        });
    });
</script>
@endpush 