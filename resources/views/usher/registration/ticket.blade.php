@extends('layouts.app')

@section('title', 'Ticket Details')

@section('content')
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
                <h1 class="text-2xl md:text-3xl font-bold text-[#041E42]">Ticket Generated</h1>
                <p class="text-gray-600 mt-1">Registration completed successfully</p>
            </div>
            <div class="flex flex-wrap gap-2 items-center">
                <a href="{{ route('usher.register') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-white bg-[#041E42] hover:bg-[#0A2E5C] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#041E42]">
                    <i data-lucide="user-plus" class="h-4 w-4 mr-2"></i>
                    New Registration
                </a>
                <form action="{{ route('usher.ticket.send-email', $ticket->id) }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-white bg-[#041E42] hover:bg-[#0A2E5C] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#041E42]">
                        <i data-lucide="mail" class="h-4 w-4 mr-2"></i>
                        Email Ticket
                    </button>
                </form>
                <form action="{{ route('usher.ticket.send-sms', $ticket->id) }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-white bg-[#041E42] hover:bg-[#0A2E5C] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#041E42]">
                        <i data-lucide="message-square" class="h-4 w-4 mr-2"></i>
                        SMS Ticket
                    </button>
                </form>
                <div class="dropdown inline-block">
                    <button id="print-ticket" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-white bg-[#041E42] hover:bg-[#0A2E5C] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#041E42]" aria-haspopup="true" aria-expanded="false">
                        <i data-lucide="printer" class="h-4 w-4 mr-2"></i>
                        Print/Save
                    </button>
                    <div class="dropdown-content" aria-labelledby="print-ticket">
                        <a href="{{ route('usher.ticket.print-view', $ticket->id) }}" target="_blank" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i data-lucide="printer" class="h-4 w-4 inline mr-2"></i> Print
                        </a>
                        <a href="{{ route('usher.ticket.download-pdf', $ticket->id) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i data-lucide="file-text" class="h-4 w-4 inline mr-2"></i> Save as PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Ticket Card - Compact design -->
    <div id="ticket-content" class="max-w-3xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden border border-gray-200 print:shadow-none print:border-0">
            <!-- Ticket Header -->
            <div class="bg-[#041E42] px-4 py-3 text-white relative">
                @php
                    $statusClass = 'bg-green-500';
                    $statusText = 'VALID';
                    
                    if (!$ticket->active) {
                        $statusClass = 'bg-red-500';
                        $statusText = 'INACTIVE';
                    } elseif ($ticket->isExpired()) {
                        $statusClass = 'bg-red-500';
                        $statusText = 'EXPIRED';
                    }
                @endphp
                <div class="absolute top-2 right-2 {{ $statusClass }} text-white text-xs font-bold py-0.5 px-2 rounded-full">
                    {{ $statusText }}
                </div>
                <h2 class="text-xl font-bold text-center">ZURIW25 CONFERENCE</h2>
                <p class="text-xs text-gray-200 text-center">Official Attendee Ticket</p>
            </div>
            
            <!-- Ticket Body -->
            <div class="p-4">
                <!-- Ticket Number -->
                <div class="bg-gray-50 p-3 mb-4 rounded-lg border text-center">
                    <div class="text-gray-500 text-xs">TICKET NUMBER</div>
                    <div class="text-xl font-bold text-[#041E42]">{{ $ticket->ticket_number }}</div>
                    @if($ticket->expiration_date)
                    <div class="text-xs text-gray-500 mt-1">
                        Expires: {{ $ticket->expiration_date->format('M j, Y g:i A') }}
                        @if(!$ticket->isExpired())
                        <span class="text-xs text-gray-500">({{ $ticket->expiration_date->diffForHumans() }})</span>
                        @endif
                    </div>
                    @endif
                </div>
                
                <div class="space-y-4">
                    <!-- Attendee Info -->
                    <div>
                        <h3 class="text-sm font-semibold text-[#041E42] mb-2 border-b pb-1">ATTENDEE INFORMATION</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
                            <div>
                                <div class="text-xs text-gray-500">Name</div>
                                <div class="font-medium">{{ $ticket->participant->full_name }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500">Role</div>
                                <div class="font-medium">{{ $ticket->participant->role }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500">Category</div>
                                <div>
                                    @php
                                        $categories = [
                                            'general' => 'General Participant',
                                            'invited' => 'Invited Guest',
                                            'internal' => 'Internal Participant',
                                            'coordinators' => 'Session Coordinator'
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mt-1">
                                        {{ $categories[$ticket->participant->category] ?? $ticket->participant->category }}
                                    </span>
                                </div>
                            </div>
                            @if($ticket->participant->organization)
                            <div>
                                <div class="text-xs text-gray-500">Organization</div>
                                <div class="font-medium">{{ $ticket->participant->organization }}</div>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Ticket Status -->
                    <div>
                        <h3 class="text-sm font-semibold text-[#041E42] mb-2 border-b pb-1">TICKET STATUS</h3>
                        <div class="flex items-center">
                            <div class="bg-gray-50 p-2 rounded-lg border w-full">
                                @php
                                    $statusClass = 'text-gray-800';
                                    $badgeClass = 'bg-gray-100 text-gray-800';
                                    $statusText = $ticket->getValidityStatus();
                                    $iconName = 'alert-circle';
                                    
                                    if (!$ticket->active) {
                                        $statusClass = 'text-red-700';
                                        $badgeClass = 'bg-red-100 text-red-800';
                                        $iconName = 'x-circle';
                                    } elseif ($ticket->isExpired()) {
                                        $statusClass = 'text-red-700';
                                        $badgeClass = 'bg-red-100 text-red-800';
                                        $iconName = 'clock';
                                    } elseif (strpos($statusText, 'Valid') !== false) {
                                        $statusClass = 'text-green-700';
                                        $badgeClass = 'bg-green-100 text-green-800';
                                        $iconName = 'check-circle';
                                    }
                                @endphp
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <i data-lucide="{{ $iconName }}" class="h-4 w-4 {{ $statusClass }} mr-2"></i>
                                        <span class="{{ $statusClass }} font-medium">Status:</span>
                                    </div>
                                    <span class="px-2 py-0.5 text-xs font-medium rounded-full {{ $badgeClass }}">
                                        {{ $statusText }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Valid Days -->
                    <div>
                        <h3 class="text-sm font-semibold text-[#041E42] mb-2 border-b pb-1">VALID ATTENDANCE DAYS</h3>
                        <div class="space-y-1.5">
                            @foreach($conferenceDays as $day)
                                <div class="flex items-center">
                                    @if(($day->id == 1 && $ticket->day1_valid) || 
                                        ($day->id == 2 && $ticket->day2_valid) || 
                                        ($day->id == 3 && $ticket->day3_valid))
                                        <div class="flex-shrink-0 h-4 w-4 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                                            <i data-lucide="check" class="h-2.5 w-2.5"></i>
                                        </div>
                                        <div class="ml-2 flex items-center flex-wrap">
                                            <span class="text-xs font-medium">{{ $day->name }}</span>
                                            <span class="text-xs text-gray-500 ml-1">{{ $day->date->format('M j') }}</span>
                                            @if($day->date->isToday())
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 ml-1">Today</span>
                                            @elseif($day->date->isPast())
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 ml-1">Past</span>
                                            @else
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 ml-1">Upcoming</span>
                                            @endif
                                        </div>
                                    @else
                                        <div class="flex-shrink-0 h-4 w-4 rounded-full bg-gray-100 flex items-center justify-center text-gray-400">
                                            <i data-lucide="x" class="h-2.5 w-2.5"></i>
                                        </div>
                                        <div class="ml-2 text-gray-500 flex items-center flex-wrap">
                                            <span class="text-xs">{{ $day->name }}</span>
                                            <span class="text-xs ml-1">{{ $day->date->format('M j') }}</span>
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 ml-1">Not Valid</span>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Payment Info -->
                    <div>
                        <h3 class="text-sm font-semibold text-[#041E42] mb-2 border-b pb-1">PAYMENT DETAILS</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
                            <div>
                                <div class="text-xs text-gray-500">Payment Status</div>
                                <div class="font-medium">
                                    @if($ticket->participant->payment_status == 'Not Paid')
                                        <span class="text-red-600">{{ $ticket->participant->payment_status }}</span>
                                    @elseif($ticket->participant->payment_status == 'Waived' || $ticket->participant->payment_status == 'Not Applicable')
                                        <span class="text-blue-600">{{ $ticket->participant->payment_status }}</span>
                                    @elseif($ticket->participant->payment_status == 'Complimentary')
                                        <span class="text-purple-600">{{ $ticket->participant->payment_status }}</span>
                                    @else
                                        <span class="text-green-600">{{ $ticket->participant->payment_status }}</span>
                                    @endif
                                </div>
                            </div>
                            
                            <div>
                                <div class="text-xs text-gray-500">Issued By</div>
                                <div class="font-medium">{{ $ticket->registeredBy->name }}</div>
                            </div>
                            
                            <div>
                                <div class="text-xs text-gray-500">Issue Date</div>
                                <div class="font-medium">{{ $ticket->created_at->format('M j, Y') }}</div>
                            </div>
                            
                            @if($ticket->expiration_date)
                            <div>
                                <div class="text-xs text-gray-500">Expiration Date</div>
                                <div class="font-medium {{ $ticket->isExpired() ? 'text-red-600' : '' }}">
                                    {{ $ticket->expiration_date->format('M j, Y g:i A') }}
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Ticket Footer -->
            <div class="bg-gray-50 px-3 py-2 border-t">
                <div class="text-xs text-gray-600 text-center">
                    This ticket must be presented for entry. Valid only for dates indicated above. Not transferable.
                    @if($ticket->expiration_date)
                    <br>Expires on {{ $ticket->expiration_date->format('F j, Y') }} at {{ $ticket->expiration_date->format('g:i A') }} East African Time.
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Lucide icons again after loading
        if(typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
        
        // Print dropdown functionality
        const printButton = document.getElementById('print-ticket');
        const dropdown = document.querySelector('.dropdown-content');
        
        if (printButton && dropdown) {
            printButton.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                dropdown.classList.toggle('hidden');
                
                // Focus trap for accessibility
                if (!dropdown.classList.contains('hidden')) {
                    setTimeout(() => {
                        dropdown.querySelector('a')?.focus();
                    }, 100);
                }
            });
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!printButton.contains(e.target) && !dropdown.contains(e.target)) {
                    dropdown.classList.add('hidden');
                }
            });
            
            // Close dropdown when pressing escape
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && !dropdown.classList.contains('hidden')) {
                    dropdown.classList.add('hidden');
                    printButton.focus();
                }
            });
        }
        
        // Email and SMS functionality now handled via form submissions
        // The AJAX implementation has been removed as we're using traditional form submissions
    });
</script>

<style>
    .dropdown {
        position: relative;
        display: inline-block;
    }
    
    .dropdown-content {
        position: absolute;
        right: 0;
        background-color: white;
        min-width: 160px;
        box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        z-index: 1;
        border-radius: 0.375rem;
        transition: all 0.2s ease-in-out;
        max-height: 0;
        overflow: hidden;
    }
    
    .dropdown-content:not(.hidden) {
        max-height: 200px;
        margin-top: 0.5rem;
        border: 1px solid #e5e7eb;
    }
    
    .dropdown-content a {
        color: #374151;
        padding: 12px 16px;
        text-decoration: none;
        display: block;
    }
    
    .dropdown-content a:hover {
        background-color: #f3f4f6;
    }
</style>
@endpush