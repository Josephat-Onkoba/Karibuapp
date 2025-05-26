@extends('layouts.app')

@section('title', 'Additional Day Payment Required')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header Section -->
    <div class="mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-[#041E42]">Additional Day Payment Required</h1>
        <p class="text-gray-600 mt-1">Please process payment for the additional conference day</p>
    </div>

    <!-- Payment Information -->
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
            <div class="p-6">
                <!-- Current Status -->
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                {{ Session::get('warning') }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Payment Amount Information -->
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 mb-6">
                    <h4 class="font-medium text-gray-700 mb-3">Payment Details</h4>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-600">Current Paid Days:</span>
                            <span class="font-medium">{{ session('additional_day_payment.current_days', 0) }} day(s)</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-600">Additional Day Payment:</span>
                            <span class="font-medium">
                                @if(session('participant.presenter_type') === 'international')
                                    <span class="flex items-center">
                                        <span class="text-sm mr-1">USD</span>
                                        <span>{{ number_format(session('additional_day_payment.required_payment', 0), 2) }}</span>
                                    </span>
                                @else
                                    <span class="flex items-center">
                                        <span class="text-sm mr-1">KES</span>
                                        <span>{{ number_format(session('additional_day_payment.required_payment', 0), 2) }}</span>
                                    </span>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Payment Method Tabs -->
                <div class="border-b border-gray-200 mb-6">
                    <div class="flex space-x-8">
                        <form action="{{ route('usher.registration.additional_day_payment') }}" method="GET" class="flex-1">
                            <input type="hidden" name="method" value="mpesa">
                            <button type="submit" 
                                class="w-full inline-flex items-center justify-center py-3 px-1 border-b-2 font-medium text-sm focus:outline-none {{ request('method', 'mpesa') === 'mpesa' ? 'border-[#041E42] text-[#041E42]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                            >
                                <img src="{{ asset('images/mpesa-logo.png') }}" alt="M-Pesa" class="h-8 w-auto mr-2">
                                M-Pesa Payment
                            </button>
                        </form>
                        <form action="{{ route('usher.registration.additional_day_payment') }}" method="GET" class="flex-1">
                            <input type="hidden" name="method" value="vabu">
                            <button type="submit" 
                                class="w-full inline-flex items-center justify-center py-3 px-1 border-b-2 font-medium text-sm focus:outline-none {{ request('method') === 'vabu' ? 'border-[#041E42] text-[#041E42]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                            >
                                <img 
                                    src="{{ asset('images/vabu-logo.png') }}" 
                                    alt="Vabu" 
                                    class="h-8 w-auto mr-2 {{ request('method') === 'vabu' ? 'brightness-0 invert-[0.13] sepia-[0.82] saturate-[2078] hue-rotate-[201deg] brightness-95 contrast-[106%]' : 'brightness-0' }}"
                                >
                                Vabu Payment
                            </button>
                        </form>
                    </div>
                </div>

                @if(request('method', 'mpesa') === 'mpesa')
                <!-- M-Pesa Payment Form -->
                <form action="{{ route('usher.registration.process_additional_day_payment') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="payment_method" value="mpesa">
                    
                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                        <h4 class="font-medium text-gray-700 mb-3">M-Pesa Payment Instructions</h4>
                        <ol class="list-decimal list-inside space-y-2 text-gray-600 text-sm">
                            <li>Go to M-Pesa menu on your phone</li>
                            <li>Select Pay Bill option</li>
                            <li>Enter Business number: <span class="font-medium">303030</span></li>
                            <li>Enter Account number: <span class="font-medium">2031653161</span></li>
                            <li>Enter Amount: 
                                <span class="font-medium">
                                    @if(session('participant.presenter_type') === 'international')
                                        USD {{ number_format(session('additional_day_payment.required_payment', 0), 2) }}
                                        <span class="text-sm text-gray-500">(Please convert to KES at current exchange rate)</span>
                                    @else
                                        KES {{ number_format(session('additional_day_payment.required_payment', 0), 2) }}
                                    @endif
                                </span>
                            </li>
                            <li>Enter your M-Pesa PIN and confirm payment</li>
                            <li>Enter the M-Pesa transaction code below</li>
                        </ol>
                    </div>

                    <div>
                        <label for="mpesa_code" class="block text-gray-700 font-medium mb-2 text-sm">M-Pesa Transaction Code <span class="text-red-500">*</span></label>
                        <input 
                            type="text" 
                            name="mpesa_code" 
                            id="mpesa_code" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#041E42] text-sm"
                            placeholder="Enter M-Pesa transaction code"
                            required
                        >
                    </div>

                    <div>
                        <label for="payment_notes" class="block text-gray-700 font-medium mb-2 text-sm">Additional Notes</label>
                        <textarea 
                            name="payment_notes" 
                            id="payment_notes" 
                            rows="2"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#041E42] text-sm"
                            placeholder="Any additional payment information"
                        ></textarea>
                    </div>

                    <div class="mt-6 flex justify-between">
                        <a href="{{ route('usher.check-in') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Cancel
                        </a>
                        <button type="submit" class="inline-flex items-center px-6 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-[#041E42] hover:bg-[#0A2E5C]">
                            Process Payment
                        </button>
                    </div>
                </form>

                @else
                <!-- Vabu Payment Form -->
                <form action="{{ route('usher.registration.process_additional_day_payment') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="payment_method" value="vabu">
                    
                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                        <h4 class="font-medium text-gray-700 mb-3">Vabu Payment Instructions</h4>
                        <p class="text-gray-600 mb-4 text-sm">Click the button below to proceed to the Vabu payment portal. After completing the payment, check the confirmation box below.</p>
                        
                        <a href="https://vabu.app/zetech-university-riw25" 
                           target="_blank"
                           class="inline-flex items-center justify-center w-full px-4 py-3 bg-white border border-[#041E42] text-[#041E42] rounded-lg hover:bg-gray-50 transition-colors duration-200"
                        >
                            <img src="{{ asset('images/vabu-logo.png') }}" alt="Vabu" class="h-8 w-auto mr-2">
                            Pay with Vabu
                        </a>
                    </div>

                    <div class="flex items-start space-x-2">
                        <input 
                            type="checkbox" 
                            name="vabu_payment_confirmed" 
                            id="vabu_payment_confirmed" 
                            class="h-4 w-4 mt-1 text-[#041E42] focus:ring-[#041E42] border-gray-300 rounded"
                            required
                        >
                        <label for="vabu_payment_confirmed" class="text-gray-700 text-sm">
                            I confirm that the payment has been completed via Vabu
                        </label>
                    </div>

                    <div>
                        <label for="payment_notes" class="block text-gray-700 font-medium mb-2 text-sm">Additional Notes</label>
                        <textarea 
                            name="payment_notes" 
                            id="payment_notes" 
                            rows="2"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#041E42] text-sm"
                            placeholder="Any additional payment information"
                        ></textarea>
                    </div>

                    <div class="mt-6 flex justify-between">
                        <a href="{{ route('usher.check-in') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Cancel
                        </a>
                        <button type="submit" class="inline-flex items-center px-6 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-[#041E42] hover:bg-[#0A2E5C]">
                            Process Payment
                        </button>
                    </div>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 