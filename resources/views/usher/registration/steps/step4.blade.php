@extends('layouts.app')

@section('title', 'Registration - Step 4: Ticket Preview')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Sticky Header -->
    <div class="sticky top-0 z-10 bg-white pb-4 mb-6 text-center">
        <h1 class="text-2xl md:text-4xl font-bold text-[#041E42]">Registration</h1>
        <p class="text-gray-600 mt-2 md:text-lg">Step 4: Preview Ticket</p>
    </div>
    
    <!-- Stepper Progress -->
    <div class="mb-10">
        <div class="max-w-4xl mx-auto">
            <div class="flex justify-between text-sm md:text-base" role="list" aria-label="Registration steps">
                @php
                    $steps = ['Category', 'Details', 'Attendance', 'Ticket', 'Check-in'];
                @endphp
                @foreach($steps as $index => $step)
                    <div role="listitem" class="flex flex-col items-center {{ $index === 3 ? 'text-[#041E42] font-bold' : 'text-gray-400' }}" aria-current="{{ $index === 3 ? 'step' : 'false' }}">
                        <div class="w-7 h-7 md:w-8 md:h-8 rounded-full flex items-center justify-center 
                                    {{ $index === 3 ? 'bg-[#041E42] text-white' : ($index < 3 ? 'bg-[#041E42] text-white' : 'bg-gray-300 text-white') }}">
                            {{ $index + 1 }}
                        </div>
                        <span class="mt-1 text-xs md:text-sm">{{ $step }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Form Content -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
        <div class="p-6">
            <form action="{{ route('usher.registration.process_step4') }}" method="POST">
                @csrf
                
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-bold text-gray-700 mb-4">Ticket Preview</h3>
                        <p class="text-gray-600 mb-6">The ticket will be valid for the selected days.</p>
                    </div>
                    
                    <!-- Ticket Preview -->
                    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">
                        <!-- Ticket Header -->
                        <div class="bg-[#041E42] px-4 py-2 text-white flex justify-between items-center">
                            <div class="flex items-center space-x-2">
                                <h2 class="text-base font-bold">ZURIW25</h2>
                                <span class="text-xs text-gray-300">|</span>
                                <p class="text-xs">Official Ticket</p>
                            </div>
                            <div class="text-right">
                                <div class="text-xs text-gray-300">{{ now()->format('F j, Y') }}</div>
                            </div>
                        </div>
                        
                        <!-- Ticket Body -->
                        <div class="p-4">
                            <div class="flex justify-between items-start mb-3">
                                <!-- Left Column -->
                                <div class="space-y-2">
                                    <div>
                                        <div class="text-xs text-gray-500">Ticket No.</div>
                                        <div class="text-sm font-bold text-[#041E42]">ZU-RIW25-{{ mt_rand(1000, 9999) }}</div>
                                    </div>
                                    <div>
                                        <div class="text-xs text-gray-500">Name</div>
                                        <div class="text-sm font-medium">{{ $data['full_name'] }}</div>
                                    </div>
                                    <div>
                                        <div class="text-xs text-gray-500">Category</div>
                                        <div class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            @php
                                                $categories = [
                                                    'general' => 'General',
                                                    'invited' => 'Invited',
                                                    'internal' => 'Internal',
                                                    'coordinators' => 'Coordinator'
                                                ];
                                            @endphp
                                            {{ $categories[$data['category']] ?? ucfirst($data['category']) }}
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Right Column -->
                                <div class="space-y-2 text-right">
                                    <div>
                                        <div class="text-xs text-gray-500">Status</div>
                                        <div class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            VALID
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-xs text-gray-500">Role</div>
                                        <div class="text-sm">{{ $data['role'] }}</div>
                                    </div>
                                    <div>
                                        <div class="text-xs text-gray-500">Payment</div>
                                        <div class="text-sm">
                                            @if($data['category'] === 'general')
                                                @if(isset($data['payment_status']) && $data['payment_status'] == 'Not Paid')
                                                    <span class="text-red-600">Not Paid</span>
                                                @elseif(isset($data['payment_status']) && $data['payment_status'] == 'Complimentary')
                                                    <span class="text-purple-600">Complimentary</span>
                                                @else
                                                    <span class="text-green-600">{{ $data['payment_status'] ?? 'Paid' }}</span>
                                                @endif
                                            @elseif($data['category'] === 'internal')
                                                <span class="text-blue-600">Waived</span>
                                            @else
                                                <span class="text-blue-600">N/A</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                           <!-- Contact Info -->
                            <div class="flex flex-col md:flex-row justify-between text-xs border-t border-gray-100 pt-2 mb-3 space-y-1 md:space-y-0 md:space-x-4">
                                <div>
                                    <span class="text-gray-500">Email:</span>
                                    <span class="ml-1 break-all">{{ $data['email'] }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-500">Phone:</span>
                                    <span class="ml-1">{{ $data['phone_number'] }}</span>
                                </div>
                            </div>

                            
                            <!-- Attendance Days -->
                            <div class="border-t border-gray-100 pt-2">
                                <div class="text-xs text-gray-500 mb-1">Valid for:</div>
                                <div class="grid grid-cols-3 gap-1">
                                    @foreach($conferenceDays as $day)
                                        <div class="flex items-center text-xs" role="listitem">
                                            <svg class="h-3 w-3 text-green-500 mr-1" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                            <span class="font-medium">{{ $day->name }}</span>
                                            @if($day->date->isToday())
                                                <span class="ml-1 px-1 bg-green-100 text-green-800 rounded text-[10px]">Today</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        
                        <!-- Ticket Footer -->
                        <div class="bg-gray-50 px-4 py-2 border-t text-[10px] text-gray-500 flex justify-between items-center">
                            <div>Present with ID</div>
                            <div>Valid only for dates shown</div>
                        </div>
                    </div>
                    
                    <div class="mt-4 bg-blue-50 border-l-4 border-blue-400 p-4" role="alert" aria-live="polite">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-700">
                                    After registration is complete, this ticket will be sent to the participant via email and text message.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-8 pt-5 border-t border-gray-200">
                    <div class="flex flex-col sm:flex-row justify-between gap-4">
                        <a href="{{ route('usher.registration.step3') }}" class="inline-flex items-center justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 -ml-1" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            Back to Attendance Days
                        </a>
                        <button type="submit" class="inline-flex items-center justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-[#041E42] hover:bg-[#02305e] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#041E42]">
                            Continue to Check-in
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2 -mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
