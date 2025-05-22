@extends('layouts.app')

@section('title', 'Participant Details')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('usher.my-registrations') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900">
            <i data-lucide="arrow-left" class="h-4 w-4 mr-1"></i>
            Back to My Registrations
        </a>
    </div>

    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-[#041E42]">Participant Details</h1>
                <p class="text-gray-600 mt-1">Detailed information about the participant</p>
            </div>
            <div class="mt-4 md:mt-0 flex space-x-3">
                @if($participant->ticket)
                <a href="{{ route('usher.registration.ticket', $participant->ticket->id) }}" 
                   class="inline-flex items-center px-4 py-2 border border-blue-300 text-sm font-medium rounded-md shadow-sm text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i data-lucide="ticket" class="h-4 w-4 mr-1.5"></i>
                    View Ticket
                </a>
                @endif
                
                @if($participant->ticket && $today)
                    @php
                        $isCheckedInToday = $participant->isCheckedInForDay($today->id);
                        $hasValidTicketToday = false;
                        
                        if ($today->id == 1 && $participant->ticket->day1_valid) {
                            $hasValidTicketToday = true;
                        } elseif ($today->id == 2 && $participant->ticket->day2_valid) {
                            $hasValidTicketToday = true;
                        } elseif ($today->id == 3 && $participant->ticket->day3_valid) {
                            $hasValidTicketToday = true;
                        }
                        
                        // Check if ticket is active and not expired
                        if ($hasValidTicketToday) {
                            $hasValidTicketToday = $participant->ticket->active && !$participant->ticket->isExpired();
                        }
                    @endphp
                
                    @if($isCheckedInToday)
                        <div class="inline-flex items-center px-4 py-2 border border-green-300 text-sm font-medium rounded-md shadow-sm text-green-700 bg-green-50">
                            <i data-lucide="check-circle" class="h-4 w-4 mr-1.5"></i>
                            Checked In Today
                        </div>
                    @elseif($hasValidTicketToday)
                        <form action="{{ route('usher.check-in.process') }}" method="POST" class="inline">
                            @csrf
                            <input type="hidden" name="participant_id" value="{{ $participant->id }}">
                            <input type="hidden" name="conference_day_id" value="{{ $today->id }}">
                            <input type="hidden" name="redirect_to" value="participant_view">
                            <input type="hidden" name="participant_id_redirect" value="{{ $participant->id }}">
                            <button type="submit" 
                                class="inline-flex items-center px-4 py-2 border border-green-300 text-sm font-medium rounded-md shadow-sm text-green-700 bg-green-50 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                <i data-lucide="check-circle" class="h-4 w-4 mr-1.5"></i>
                                Check In Today
                            </button>
                        </form>
                    @else
                        <div class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-500 bg-gray-50 cursor-not-allowed">
                            <i data-lucide="x-circle" class="h-4 w-4 mr-1.5"></i>
                            Not Valid Today
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200 p-6 mb-6">
        @include('usher.registration.partials.participant-details', [
            'participant' => $participant,
            'checkInInfo' => $checkInInfo,
            'conferenceDays' => $conferenceDays,
            'today' => $today
        ])
    </div>
    
    <!-- Check-in for Any Day Section -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200 mb-6">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900 flex items-center">
                <i data-lucide="check-square" class="h-5 w-5 mr-2 text-[#041E42]"></i>
                Check-in for Any Conference Day
            </h2>
            <p class="text-sm text-gray-600 mt-1">Generate tickets and check in this participant for any active conference day</p>
        </div>
        
        <div class="p-6">
            <form action="{{ route('usher.check-in.process') }}" method="POST" class="max-w-lg">
                @csrf
                <input type="hidden" name="participant_id" value="{{ $participant->id }}">
                <input type="hidden" name="redirect_to" value="participant_view">
                <input type="hidden" name="participant_id_redirect" value="{{ $participant->id }}">
                
                <div class="mb-4">
                    <label for="conference_day_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Select Conference Day
                    </label>
                    <div class="relative">
                        <select name="conference_day_id" id="conference_day_id" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md" required>
                            <option value="">Select a conference day...</option>
                            @foreach($conferenceDays as $day)
                                @php
                                    $isCheckedIn = $participant->isCheckedInForDay($day->id);
                                    $isToday = $today && $today->id === $day->id;
                                @endphp
                                <option value="{{ $day->id }}" {{ $isToday ? 'selected' : '' }} {{ $isCheckedIn ? 'disabled' : '' }}>
                                    {{ $day->name }} ({{ $day->date->format('M j, Y') }})
                                    @if($isToday) - TODAY @endif
                                    @if($isCheckedIn) - ALREADY CHECKED IN @endif
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                            <i data-lucide="chevron-down" class="h-4 w-4 text-gray-400"></i>
                        </div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">
                        Notes (Optional)
                    </label>
                    <textarea id="notes" name="notes" rows="2" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Add any notes about this check-in..."></textarea>
                </div>
                
                <div class="bg-yellow-50 p-3 rounded-md border border-yellow-100 text-sm text-yellow-700 mb-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i data-lucide="alert-triangle" class="h-5 w-5 text-yellow-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="font-medium">Important Note</p>
                            <ul class="mt-1 list-disc list-inside">
                                <li>Checking in for a day will generate a new ticket if needed</li>
                                <li>If the participant has no valid ticket for the selected day, a new ticket will be created</li>
                                <li>Existing tickets that are inactive or expired will remain in the system</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div>
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-[#041E42] hover:bg-[#0A2E5C] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Generate Ticket & Check-in
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Ticket History Section -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900 flex items-center">
                <i data-lucide="ticket" class="h-5 w-5 mr-2 text-[#041E42]"></i>
                Ticket History
            </h2>
            <p class="text-sm text-gray-600 mt-1">All tickets generated for this participant</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ticket Number</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valid Days</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expires</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($ticketHistory as $historyTicket)
                    <tr class="hover:bg-gray-50 {{ $historyTicket->active ? 'bg-green-50' : '' }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $historyTicket->ticket_number }}</div>
                            <div class="text-xs text-gray-500">
                                @if($historyTicket->active)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                    <i data-lucide="check-circle" class="h-3 w-3 mr-1"></i>
                                    Active
                                </span>
                                @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                    <i data-lucide="x-circle" class="h-3 w-3 mr-1"></i>
                                    Inactive
                                </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex space-x-1">
                                @if($historyTicket->day1_valid)
                                <span class="px-1.5 py-0.5 text-xs font-medium rounded bg-green-100 text-green-800">Day 1</span>
                                @endif
                                @if($historyTicket->day2_valid)
                                <span class="px-1.5 py-0.5 text-xs font-medium rounded bg-green-100 text-green-800">Day 2</span>
                                @endif
                                @if($historyTicket->day3_valid)
                                <span class="px-1.5 py-0.5 text-xs font-medium rounded bg-green-100 text-green-800">Day 3</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusClass = 'bg-gray-100 text-gray-800';
                                $statusText = $historyTicket->getValidityStatus();
                                
                                if (!$historyTicket->active) {
                                    $statusClass = 'bg-red-100 text-red-800';
                                } elseif ($historyTicket->isExpired()) {
                                    $statusClass = 'bg-red-100 text-red-800';
                                } elseif (strpos($statusText, 'Valid') !== false) {
                                    $statusClass = 'bg-green-100 text-green-800';
                                }
                            @endphp
                            
                            <span class="px-2 py-0.5 text-xs font-medium rounded-full {{ $statusClass }}">
                                {{ $statusText }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $historyTicket->created_at->format('M j, Y g:i A') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($historyTicket->expiration_date)
                                {{ $historyTicket->expiration_date->format('M j, Y g:i A') }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <a href="{{ route('usher.registration.ticket', $historyTicket->id) }}" 
                               class="text-blue-600 hover:text-blue-900 font-medium">
                                View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                            No ticket history found for this participant.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection 