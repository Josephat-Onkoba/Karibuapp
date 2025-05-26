@extends('layouts.app')

@section('title', 'Registration - Payment')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header Section -->
    <div class="mb-6">
                <h1 class="text-2xl md:text-3xl font-bold text-[#041E42]">Registration</h1>
        <p class="text-gray-600 mt-1">Payment Required</p>
    </div>

    <!-- Payment Information -->
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
            <div class="p-6">
                <!-- Payment Amount Information -->
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 mb-6">
                    <h4 class="font-medium text-gray-700 mb-3">Payment Details for {{ $data['full_name'] }}</h4>
                    
                    @php
                        $amount = 0;
                        $description = '';
                        
                        switch($data['category']) {
                            case 'exhibitor':
                                $amount = 30000;
                                $description = 'Exhibition Fee (Includes full conference period)';
                                break;
                            case 'presenter':
                                switch($data['presenter_type']) {
                                    case 'non_student':
                                        $amount = 6000;
                                        $description = 'Presenter Fee (Non-Student)';
                                        break;
                                    case 'student':
                                        $amount = 4000;
                                        $description = 'Presenter Fee (Student)';
                                        break;
                                    case 'international':
                                        $amount = 100;
                                        $description = 'Presenter Fee (International) in USD';
                                        break;
                                }
                                break;
                            case 'general':
                                $amount = $data['payment_amount'] ?? 0;
                                $days = $data['eligible_days'] ?? 1;
                                $description = "General Participant Fee ({$days} " . Str::plural('day', $days) . ")";
                                break;
                        }
                    @endphp
                    
                    <div class="space-y-2">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-600">Category:</span>
                            <span class="font-medium">{{ ucfirst($data['category']) }}</span>
                </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-600">Description:</span>
                            <span class="font-medium">{{ $description }}</span>
                </div>
                        <div class="flex justify-between items-center text-lg pt-2 border-t border-gray-200 mt-2">
                            <span class="text-gray-600">Amount Due:</span>
                            <span class="font-bold text-[#041E42]">
                                @if(isset($data['presenter_type']) && $data['presenter_type'] === 'international')
                                    <span class="flex items-center">
                                        <span class="text-sm mr-1">USD</span>
                                        <span>{{ number_format($amount, 2) }}</span>
                                    </span>
                                @else
                                    <span class="flex items-center">
                                        <span class="text-sm mr-1">KES</span>
                                        <span>{{ number_format($amount, 2) }}</span>
                                    </span>
                                @endif
                            </span>
            </div>
        </div>
    </div>

                <!-- Payment Method Tabs -->
                <div class="border-b border-gray-200 mb-6">
                    <div class="flex space-x-8">
                        <form action="{{ route('usher.registration.payment') }}" method="GET" class="flex-1">
                            <input type="hidden" name="method" value="mpesa">
                            <button type="submit" 
                                class="w-full inline-flex items-center justify-center py-3 px-1 border-b-2 font-medium text-sm focus:outline-none {{ request('method', 'mpesa') === 'mpesa' ? 'border-[#041E42] text-[#041E42]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                            >
                                <img src="{{ asset('images/mpesa-logo.png') }}" alt="M-Pesa" class="h-8 w-auto mr-2">
                                M-Pesa Payment
                            </button>
                        </form>
                        <form action="{{ route('usher.registration.payment') }}" method="GET" class="flex-1">
                            <input type="hidden" name="method" value="vabu">
                            <button type="submit" 
                                class="w-full inline-flex items-center justify-center py-3 px-1 border-b-2 font-medium text-sm focus:outline-none {{ request('method') === 'vabu' ? 'border-[#041E42] text-[#041E42]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                            >
                                <img 
                                    src="{{ asset('images/vabu-logo.png') }}" 
                                    alt="Vabu" 
                                    class="h-8 w-auto mr-2 {{ request('method') === 'vabu' ? 'brightness-0 invert-[0.13] sepia-[0.82] saturate-[2078] hue-rotate-[201deg] brightness-95 contrast-[1.06]' : 'brightness-0' }}"
                                >
                                Vabu Payment
                            </button>
                        </form>
                    </div>
                </div>

                @if(request('method', 'mpesa') === 'mpesa')
                <!-- M-Pesa Payment Form -->
                <form action="{{ route('usher.registration.process_payment') }}" method="POST" class="space-y-4">
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
                                    @if(isset($data['presenter_type']) && $data['presenter_type'] === 'international')
                                        USD {{ number_format($amount, 2) }}
                                        <span class="text-sm text-gray-500">(Please convert to KES at current exchange rate)</span>
                                    @else
                                        KES {{ number_format($amount, 2) }}
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

                    <div class="mt-8 pt-5 border-t border-gray-200">
                        <div class="flex justify-between">
                            <a href="{{ route('usher.registration.step2') }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 -ml-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                Back
                            </a>
                            <button type="submit" class="inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-[#041E42] hover:bg-[#0A2E5C] focus:outline-none">
                                Continue
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2 -mr-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </form>

                @else
                <!-- Vabu Payment Form -->
                <form action="{{ route('usher.registration.process_payment') }}" method="POST" class="space-y-4">
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
                
                <div class="mt-8 pt-5 border-t border-gray-200">
                    <div class="flex justify-between">
                        <a href="{{ route('usher.registration.step2') }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 -ml-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                                Back
                        </a>
                            <button type="submit" class="inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-[#041E42] hover:bg-[#0A2E5C] focus:outline-none">
                                Continue
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2 -mr-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection