@extends('layouts.app')

@section('title', 'My Registrations')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Flash Messages -->
    @if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm">{{ session('success') }}</p>
            </div>
            <div class="ml-auto pl-3">
                <div class="-mx-1.5 -my-1.5">
                    <button class="inline-flex rounded-md p-1.5 text-green-500 hover:bg-green-200 focus:outline-none" onclick="this.parentElement.parentElement.parentElement.remove()">
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
    
    @if(session('error'))
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm">{{ session('error') }}</p>
            </div>
            <div class="ml-auto pl-3">
                <div class="-mx-1.5 -my-1.5">
                    <button class="inline-flex rounded-md p-1.5 text-red-500 hover:bg-red-200 focus:outline-none" onclick="this.parentElement.parentElement.parentElement.remove()">
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-[#041E42]">My Registrations</h1>
                <p class="text-gray-600 mt-1">Participants you have registered</p>
            </div>
            <div class="mt-4 md:mt-0">
                <a href="{{ route('usher.register') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-[#041E42] hover:bg-[#0A2E5C] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#041E42]">
                    <i data-lucide="user-plus" class="h-4 w-4 mr-2"></i>
                    Register New Participant
                </a>
            </div>
        </div>
    </div>
    
    <!-- Participants Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
        <!-- Action Bar -->
        <div class="p-4 md:p-6 bg-white border-b border-gray-200">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center space-y-3 sm:space-y-0">
                <h2 class="text-lg font-medium text-gray-900">Registered Participants</h2>
                
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            Checked In
                        </span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            Not Checked In
                        </span>
                    </div>
                    
                    <div class="w-full sm:w-64">
                        <div class="relative">
                            <input type="text" id="search-input" placeholder="Search participants..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#041E42]">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i data-lucide="search" class="h-4 w-4 text-gray-400"></i>
                            </div>
                            <button type="button" id="clear-search" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 cursor-pointer hidden">
                                <i data-lucide="x" class="h-4 w-4"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Table Section -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Participant</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Role</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Ticket Number</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Ticket Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check-in Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Registration Date</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="participants-table-body">
                    @forelse($participants as $participant)
                    <tr class="participant-row hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="h-8 w-8 flex-shrink-0 rounded-full bg-[#041E42]/10 flex items-center justify-center">
                                    <span class="text-[#041E42] text-sm font-medium">{{ substr($participant->full_name, 0, 1) }}</span>
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">{{ $participant->full_name }}</div>
                                    <div class="text-sm text-gray-500">{{ $participant->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap hidden md:table-cell">
                            <span class="text-sm text-gray-900">
                                {{ ucfirst($participant->role ?? 'delegate') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap hidden md:table-cell">
                            @if($participant->ticket)
                            <div class="text-sm text-gray-900">{{ $participant->ticket->ticket_number }}</div>
                            @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">No Ticket</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap hidden sm:table-cell">
                            @if($participant->ticket)
                                @php
                                    $textClass = 'text-gray-900';
                                    $statusText = $participant->ticket->getValidityStatus();
                                    
                                    if (!$participant->ticket->active) {
                                        $textClass = 'text-red-600';
                                    } elseif ($participant->ticket->isExpired()) {
                                        $textClass = 'text-red-600';
                                    } elseif (strpos($statusText, 'Valid') !== false) {
                                        $textClass = 'text-green-600';
                                    }
                                @endphp
                                
                                <span class="text-sm {{ $textClass }}">
                                    {{ $statusText }}
                                </span>
                            @else
                            <span class="text-sm text-gray-500">N/A</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
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
                                    
                                    // Also check if ticket is active and not expired
                                    if ($hasValidTicketToday) {
                                        $hasValidTicketToday = $participant->ticket->active && !$participant->ticket->isExpired();
                                    }
                                @endphp
                                
                                @if($isCheckedInToday)
                                    <span class="text-sm text-green-600">
                                        Checked In
                                    </span>
                                @elseif($hasValidTicketToday)
                                    <span class="text-sm text-amber-600">
                                        Not Checked In
                                    </span>
                                @else
                                    <span class="text-sm text-gray-600">
                                        Not Valid Today
                                    </span>
                                @endif
                            @else
                                <span class="text-sm text-gray-600">
                                    No Active Day
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden sm:table-cell">
                            {{ $participant->created_at->format('M j') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <div class="flex space-x-2">
                                @if($participant->ticket)
                                <a href="{{ route('usher.registration.ticket', $participant->ticket->id) }}" 
                                   class="inline-flex items-center px-2 py-1 border border-blue-300 text-xs font-medium rounded shadow-sm text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                   title="View and print participant's ticket details">
                                    <i data-lucide="ticket" class="h-4 w-4"></i>
                                </a>
                                @endif
                                
                                <a href="{{ route('usher.participant.view', $participant->id) }}" 
                                   class="inline-flex items-center px-2 py-1 border border-gray-300 text-xs font-medium rounded shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                                   title="View participant details">
                                    <i data-lucide="eye" class="h-4 w-4"></i>
                                </a>
                                
                                @if($participant->ticket && $today && !$participant->isCheckedInForDay($today->id))
                                    @php
                                        $canCheckIn = false;
                                        if (($today->id == 1 && $participant->ticket->day1_valid) || 
                                     ($today->id == 2 && $participant->ticket->day2_valid) || 
                                            ($today->id == 3 && $participant->ticket->day3_valid)) {
                                            // Check if ticket is active and not expired
                                            $canCheckIn = $participant->ticket->active && !$participant->ticket->isExpired();
                                        }
                                    @endphp
                                    
                                    @if($canCheckIn)
                                    <form action="{{ route('usher.check-in.process') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="participant_id" value="{{ $participant->id }}">
                                        <input type="hidden" name="conference_day_id" value="{{ $today->id }}">
                                        <input type="hidden" name="redirect_to" value="my-registrations">
                                        <button type="submit" 
                                                class="inline-flex items-center px-2 py-1 border border-green-300 text-xs font-medium rounded shadow-sm text-green-700 bg-green-50 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                                                title="Check in this participant for today">
                                            <i data-lucide="check-circle" class="h-4 w-4"></i>
                                        </button>
                                    </form>
                                    @else
                                    <button disabled
                                            class="inline-flex items-center px-2 py-1 border border-gray-200 text-xs font-medium rounded shadow-sm text-gray-400 bg-gray-50 cursor-not-allowed"
                                            title="Cannot check in - ticket not valid for today">
                                        <i data-lucide="x-circle" class="h-4 w-4"></i>
                                    </button>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-sm text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <i data-lucide="users" class="h-8 w-8 text-gray-400 mb-2"></i>
                                <p>No participants registered yet</p>
                                <a href="{{ route('usher.register') }}" class="mt-2 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-[#041E42] hover:bg-[#0A2E5C] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#041E42]">
                                    <i data-lucide="user-plus" class="h-4 w-4 mr-1"></i>
                                    Register Your First Participant
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($participants->hasPages())
        <div class="px-6 py-4 bg-white border-t border-gray-200">
            <div class="flex flex-col sm:flex-row items-center justify-between">
                <div class="text-sm text-gray-600 mb-4 sm:mb-0">
                    Showing {{ $participants->firstItem() ?? 0 }} to {{ $participants->lastItem() ?? 0 }} of {{ $participants->total() }} entries
                </div>
                <div class="pagination-links">
            {{ $participants->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Search functionality
        const searchInput = document.getElementById('search-input');
    const clearSearchBtn = document.getElementById('clear-search');
    const rows = document.querySelectorAll('.participant-row');
    
    // Function to perform search
    function performSearch() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        let visibleCount = 0;
        
        rows.forEach(row => {
            const name = row.querySelector('div.text-sm.font-medium').textContent.toLowerCase();
            const email = row.querySelector('div.text-sm.text-gray-500').textContent.toLowerCase();
            const role = row.querySelector('td:nth-child(2) span')?.textContent.toLowerCase() || '';
            const ticketNumber = row.querySelector('td:nth-child(3) div')?.textContent.toLowerCase() || '';
            
            if (searchTerm === '' || 
                name.includes(searchTerm) || 
                email.includes(searchTerm) || 
                role.includes(searchTerm) || 
                ticketNumber.includes(searchTerm)) {
                    row.style.display = '';
                visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });
        
        // Show/hide the clear button
        if (searchTerm === '') {
            clearSearchBtn.classList.add('hidden');
        } else {
            clearSearchBtn.classList.remove('hidden');
        }
        
        // Update no results message
        const noResultsRow = document.getElementById('no-search-results');
        if (visibleCount === 0 && searchTerm !== '') {
            if (!noResultsRow) {
                const tableBody = document.getElementById('participants-table-body');
                const newRow = document.createElement('tr');
                newRow.id = 'no-search-results';
                newRow.innerHTML = `
                    <td colspan="7" class="px-6 py-10 text-center text-sm text-gray-500">
                        <div class="flex flex-col items-center justify-center">
                            <i data-lucide="search-x" class="h-8 w-8 text-gray-400 mb-2"></i>
                            <p>No matching participants found</p>
                            <button id="reset-search" class="mt-2 inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                <i data-lucide="rotate-ccw" class="h-4 w-4 mr-1"></i>
                                Reset Search
                            </button>
                        </div>
                    </td>
                `;
                tableBody.appendChild(newRow);
                
                // Initialize Lucide icons for the new elements
                if (window.Lucide) {
                    window.Lucide.createIcons();
                }
                
                // Add event listener to reset button
                document.getElementById('reset-search').addEventListener('click', function() {
                    searchInput.value = '';
                    performSearch();
                });
            }
        } else if (noResultsRow) {
            noResultsRow.remove();
        }
    }
    
    // Event listeners
    searchInput.addEventListener('keyup', performSearch);
    
    clearSearchBtn.addEventListener('click', function() {
        searchInput.value = '';
        performSearch();
        searchInput.focus();
        });
    });
</script>
@endsection 