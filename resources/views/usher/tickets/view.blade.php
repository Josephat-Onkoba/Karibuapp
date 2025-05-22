@extends('layouts.app')

@section('title', 'Ticket Details')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('usher.tickets') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900">
            <i data-lucide="arrow-left" class="h-4 w-4 mr-1"></i>
            Back to Ticket Search
        </a>
    </div>

    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-[#041E42]">Ticket Details</h1>
                <p class="text-gray-600 mt-1">Full ticket: <span class="font-medium">{{ $ticket->ticket_number }}</span></p>
            </div>
            <div class="mt-4 md:mt-0 flex space-x-3">
                <a href="{{ route('usher.registration.ticket', $ticket->id) }}" 
                   class="inline-flex items-center px-4 py-2 border border-blue-300 text-sm font-medium rounded-md shadow-sm text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i data-lucide="eye" class="h-4 w-4 mr-1.5"></i>
                    View Full Ticket
                </a>
                <a href="{{ route('usher.ticket.download-pdf', $ticket->id) }}" 
                   class="inline-flex items-center px-4 py-2 border border-green-300 text-sm font-medium rounded-md shadow-sm text-green-700 bg-green-50 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <i data-lucide="download" class="h-4 w-4 mr-1.5"></i>
                    Download PDF
                </a>
            </div>
        </div>
    </div>
    
    <!-- Ticket Status Banner -->
    @php
        $statusClass = 'bg-gray-100 border-gray-400 text-gray-700';
        $statusText = 'Unknown Status';
        $statusIcon = 'help-circle';
        
        if (!$ticket->active) {
            $statusClass = 'bg-red-100 border-red-400 text-red-700';
            $statusText = 'Inactive Ticket';
            $statusIcon = 'x-circle';
        } elseif ($ticket->isExpired()) {
            $statusClass = 'bg-red-100 border-red-400 text-red-700';
            $statusText = 'Expired Ticket';
            $statusIcon = 'clock';
        } else {
            $statusClass = 'bg-green-100 border-green-400 text-green-700';
            $statusText = 'Active Ticket';
            $statusIcon = 'check-circle';
        }
    @endphp
    
    <div class="p-4 rounded-lg border-l-4 {{ $statusClass }} mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <i data-lucide="{{ $statusIcon }}" class="h-5 w-5"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium">
                    {{ $statusText }}: {{ $ticket->getValidityStatus() }}
                </p>
            </div>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Ticket Information -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200 lg:col-span-2">
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900 flex items-center">
                    <i data-lucide="ticket" class="h-5 w-5 mr-2 text-[#041E42]"></i>
                    Ticket Information
                </h2>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Ticket Number</h3>
                        <p class="mt-1 text-lg font-semibold text-gray-900">
                            <span class="text-gray-500">ZU-RIW25-</span>{{ substr($ticket->ticket_number, 9) }}
                        </p>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Creation Date</h3>
                        <p class="mt-1 text-gray-900">{{ $ticket->created_at->format('F j, Y g:i A') }}</p>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Valid For</h3>
                        <div class="mt-2 flex flex-wrap gap-2">
                            @if($ticket->day1_valid)
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Day 1: {{ optional($conferenceDays->where('id', 1)->first())->name ?? 'Day 1' }}
                                </span>
                            @endif
                            
                            @if($ticket->day2_valid)
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Day 2: {{ optional($conferenceDays->where('id', 2)->first())->name ?? 'Day 2' }}
                                </span>
                            @endif
                            
                            @if($ticket->day3_valid)
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Day 3: {{ optional($conferenceDays->where('id', 3)->first())->name ?? 'Day 3' }}
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Expiration</h3>
                        <p class="mt-1 text-gray-900">
                            @if($ticket->expiration_date)
                                {{ $ticket->expiration_date->format('F j, Y g:i A') }}
                                
                                @if($ticket->isExpired())
                                    <span class="text-xs font-medium text-red-600 ml-1">(Expired)</span>
                                @endif
                            @else
                                Not set
                            @endif
                        </p>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Status</h3>
                        <p class="mt-1">
                            @if($ticket->active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i data-lucide="check-circle" class="h-3.5 w-3.5 mr-1"></i>
                                    Active
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <i data-lucide="x-circle" class="h-3.5 w-3.5 mr-1"></i>
                                    Inactive
                                </span>
                            @endif
                        </p>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Registered By</h3>
                        <p class="mt-1 text-gray-900">{{ $ticket->registeredBy->name ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Participant Information -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900 flex items-center">
                    <i data-lucide="user" class="h-5 w-5 mr-2 text-[#041E42]"></i>
                    Participant Information
                </h2>
            </div>
            
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="h-12 w-12 rounded-full bg-[#041E42] text-white flex items-center justify-center text-xl font-bold">
                        {{ strtoupper(substr($ticket->participant->full_name, 0, 1)) }}{{ strtoupper(substr($ticket->participant->full_name, strpos($ticket->participant->full_name, ' ') + 1, 1)) }}
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900">{{ $ticket->participant->full_name }}</h3>
                        <p class="text-sm text-gray-500">{{ $ticket->participant->role }}</p>
                    </div>
                </div>
                
                <div class="mt-6 space-y-4">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Email</h3>
                        <p class="mt-1 text-gray-900">{{ $ticket->participant->email }}</p>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Phone</h3>
                        <p class="mt-1 text-gray-900">{{ $ticket->participant->phone_number }}</p>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Organization</h3>
                        <p class="mt-1 text-gray-900">{{ $ticket->participant->organization ?? 'N/A' }}</p>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Job Title</h3>
                        <p class="mt-1 text-gray-900">{{ $ticket->participant->job_title ?? 'N/A' }}</p>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Category</h3>
                        <p class="mt-1">
                            @php
                                $categoryColors = [
                                    'general' => 'bg-blue-100 text-blue-800',
                                    'invited' => 'bg-purple-100 text-purple-800',
                                    'internal' => 'bg-green-100 text-green-800',
                                    'coordinators' => 'bg-orange-100 text-orange-800'
                                ];
                                
                                $color = $categoryColors[$ticket->participant->category] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $color }}">
                                {{ ucfirst($ticket->participant->category) }}
                            </span>
                        </p>
                    </div>
                </div>
                
                <div class="mt-6">
                    <a href="{{ route('usher.participant.view', $ticket->participant->id) }}" 
                       class="inline-flex w-full justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i data-lucide="user-circle" class="h-4 w-4 mr-1.5"></i>
                        View Full Participant Details
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Check-In History -->
    <div class="mt-6 bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900 flex items-center">
                <i data-lucide="clipboard-check" class="h-5 w-5 mr-2 text-[#041E42]"></i>
                Check-In History
            </h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Conference Day
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Check-In Time
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Checked-In By
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Notes
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($ticket->participant->checkIns as $checkIn)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-medium text-gray-900">
                                    {{ optional($conferenceDays->where('id', $checkIn->conference_day_id)->first())->name ?? 'Day ' . $checkIn->conference_day_id }}
                                </span>
                                <div class="text-xs text-gray-500">
                                    {{ optional($conferenceDays->where('id', $checkIn->conference_day_id)->first())->date->format('M j, Y') ?? 'N/A' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $checkIn->checked_in_at->format('M j, Y g:i A') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ optional($checkIn->checkedBy)->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $checkIn->notes ?? 'No notes' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                No check-in history found for this participant.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection 