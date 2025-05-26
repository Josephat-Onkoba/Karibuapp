@extends('layouts.app')

@section('title', 'Ticket Details')

@section('content')
<style>
    @media print {
        * {
            print-color-adjust: exact;
            -webkit-print-color-adjust: exact;
        }
    }
</style>
<div class="container mx-auto px-4 py-6">
    <!-- Success Message -->
    @if(session('success'))
    <div class="mb-6 rounded-md bg-green-50 p-4 border border-green-200">
        <div class="flex">
            <div class="flex-shrink-0">
                <i data-lucide="check-circle" class="h-5 w-5 text-green-500"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-green-800">{{ session('success') }}</h3>
            </div>
        </div>
    </div>
    @endif
    
    <!-- Error Message -->
    @if(session('error'))
    <div class="mb-6 rounded-md bg-red-50 p-4 border border-red-200">
        <div class="flex">
            <div class="flex-shrink-0">
                <i data-lucide="alert-circle" class="h-5 w-5 text-red-500"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">{{ session('error') }}</h3>
            </div>
        </div>
    </div>
    @endif
    
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div class="mb-4 md:mb-0">
                <h1 class="text-2xl md:text-3xl font-bold text-[#041E42]">Ticket Details</h1>
                <p class="text-gray-600 mt-1">{{ $ticket->ticket_number }}</p>
            </div>
            <div class="flex flex-wrap gap-2 items-center">
                <a href="{{ route('usher.ticket.print-view', $ticket->id) }}" target="_blank" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-white bg-[#041E42] hover:bg-[#0A2E5C] focus:outline-none">
                    <i data-lucide="printer" class="h-4 w-4 mr-2"></i>
                    Print
                </a>
                <a href="{{ route('usher.ticket.download-pdf', $ticket->id) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-white bg-[#041E42] hover:bg-[#0A2E5C] focus:outline-none">
                    <i data-lucide="file-text" class="h-4 w-4 mr-2"></i>
                    Download PDF
                </a>
                <form action="{{ route('usher.ticket.send-email', $ticket->id) }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-white bg-[#041E42] hover:bg-[#0A2E5C] focus:outline-none">
                        <i data-lucide="mail" class="h-4 w-4 mr-2"></i>
                        Email Ticket
                    </button>
                </form>
                <form action="{{ route('usher.ticket.send-sms', $ticket->id) }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-white bg-[#041E42] hover:bg-[#0A2E5C] focus:outline-none">
                        <i data-lucide="message-square" class="h-4 w-4 mr-2"></i>
                        SMS Ticket
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Ticket Preview -->
    <div id="ticket-content" class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden border border-gray-200">
            <!-- Ticket Header -->
            <div class="bg-[#041E42] px-4 py-2 text-white flex justify-between items-center">
                <div class="flex items-center space-x-2">
                    <div class="text-base font-bold">ZURIW25</div>
                    <span class="text-xs text-gray-300">|</span>
                    <div class="text-xs">Official Ticket</div>
                </div>
                <div class="text-right">
                    <div class="text-xs text-gray-300">{{ date('F j, Y') }}</div>
                </div>
            </div>
            
            <!-- Ticket Body -->
            <div class="p-4">
                <!-- Main Info Columns -->
                <div class="flex justify-between mb-4">
                    <!-- Left Column -->
                    <div class="flex-1">
                        <div class="mb-3">
                            <div class="text-xs text-gray-500">Ticket No.</div>
                            <div class="text-sm font-bold text-[#041E42]">{{ $ticket->ticket_number }}</div>
                </div>
                        <div class="mb-3">
                                <div class="text-xs text-gray-500">Name</div>
                            <div class="text-sm font-medium">{{ $ticket->participant->full_name }}</div>
                            </div>
                        <div class="mb-3">
                            <div class="text-xs text-gray-500">Category</div>
                            <div>
                                @php
                                    $categories = [
                                        'general' => 'General',
                                        'invited' => 'Invited',
                                        'internal' => 'Internal',
                                        'coordinators' => 'Coordinator'
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $categories[$ticket->participant->category] ?? ucfirst($ticket->participant->category) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Column -->
                    <div class="flex-1 text-right">
                        <div class="mb-3">
                            <div class="text-xs text-gray-500">Status</div>
                                <div>
                                    @php
                                    $statusClass = 'bg-green-100 text-green-800';
                                    $statusText = 'VALID';
                                    
                                    if (!$ticket->active) {
                                        $statusClass = 'bg-red-100 text-red-800';
                                        $statusText = 'INACTIVE';
                                    } elseif ($ticket->isExpired()) {
                                        $statusClass = 'bg-red-100 text-red-800';
                                        $statusText = 'EXPIRED';
                                    }
                                    @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                                    {{ $statusText }}
                                    </span>
                                </div>
                            </div>
                        <div class="mb-3">
                            <div class="text-xs text-gray-500">Role</div>
                            <div class="text-sm">{{ $ticket->participant->role }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-xs text-gray-500">Payment</div>
                            <div>
                                @php
                                    $paymentClass = 'bg-green-100 text-green-800';
                                    if ($ticket->participant->payment_status == 'Not Paid') {
                                        $paymentClass = 'bg-red-100 text-red-800';
                                    } elseif ($ticket->participant->payment_status == 'Complimentary') {
                                        $paymentClass = 'bg-purple-100 text-purple-800';
                                    } elseif ($ticket->participant->payment_status == 'Waived' || $ticket->participant->payment_status == 'Not Applicable') {
                                        $paymentClass = 'bg-blue-100 text-blue-800';
                                    }
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $paymentClass }}">
                                    {{ $ticket->participant->payment_status }}
                                </span>
                            </div>
                        </div>
                        </div>
                    </div>
                    
                <!-- Contact Info -->
                <div class="border-t border-gray-100 pt-3 mb-4">
                    <div class="flex justify-between text-xs">
                        <div class="text-gray-500">
                            Email: <span class="text-gray-900">{{ $ticket->participant->email }}</span>
                                        </div>
                        <div class="text-gray-500">
                            Phone: <span class="text-gray-900">{{ $ticket->participant->phone_number }}</span>
                                </div>
                        </div>
                    </div>
                    
                <!-- Attendance Days -->
                <div class="border-t border-gray-100 pt-3">
                    <div class="text-xs text-gray-500 mb-2">Valid for:</div>
                    <div class="grid grid-cols-3 gap-2">
                        @foreach($conferenceDays as $day)
                            @php
                                $isValid = false;
                                if ($day->id == 1 && $ticket->day1_valid) {
                                    $isValid = true;
                                } elseif ($day->id == 2 && $ticket->day2_valid) {
                                    $isValid = true;
                                } elseif ($day->id == 3 && $ticket->day3_valid) {
                                    $isValid = true;
                                }
                            @endphp
                            @if($isValid)
                                <div class="flex items-center text-xs">
                                    <svg class="h-3 w-3 text-green-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="font-medium">{{ $day->name }}</span>
                                    @if($day->date->isToday())
                                        <span class="ml-1 px-1 bg-green-100 text-green-800 rounded text-[10px]">Today</span>
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
            
            <!-- Ticket Footer -->
            <div class="bg-gray-50 px-4 py-2 border-t flex justify-between text-xs text-gray-500">
                <div>Valid only for dates shown</div>
            </div>
        </div>
    </div>
</div>
@endsection