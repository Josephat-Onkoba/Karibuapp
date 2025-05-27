@extends('layouts.app')

@section('title', 'Check-In Participants')

@section('content')
<style>
    /* Error message styling */
    .alert-message {
        z-index: 1000;
        min-width: 300px;
        max-width: 90%;
        animation: slideIn 0.3s ease-out;
    }
    
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    
    /* Form validation styling */
    .is-invalid {
        border-color: #ef4444 !important;
    }
    
    .invalid-feedback {
        color: #ef4444;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }
</style>

<div class="container mx-auto px-4 py-6">
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-3xl font-bold text-primary">Check-In Participants</h1>
        <a href="{{ route('usher.check-ins') }}" class="flex items-center bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-4 rounded-lg transition-colors">
            <i data-lucide="list" class="h-4 w-4 mr-2"></i>
            <span>View All Check-ins</span>
        </a>
    </div>
    
    @if (session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-r-md shadow-sm" role="alert">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i data-lucide="check-circle" class="h-5 w-5 text-green-500"></i>
                </div>
                <div class="ml-3">
                    <p class="font-medium">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r-md shadow-sm" role="alert">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i data-lucide="alert-circle" class="h-5 w-5 text-red-500"></i>
                </div>
                <div class="ml-3">
                    <p class="font-medium">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Search Panel -->
        <div class="md:col-span-1">
            <div class="bg-white shadow-md rounded-lg p-6 h-full">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                    <i data-lucide="search" class="h-5 w-5 mr-2 text-primary"></i>
                    Find Participant
                </h2>
                
                @if (!$today)
                    <div class="bg-yellow-50 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4 rounded-r-md" role="alert">
                        <p class="font-bold">No Conference Today</p>
                        <p>There is no active conference day set for today. Please select a different day.</p>
                    </div>
                @endif
                
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i data-lucide="user-search" class="h-5 w-5 text-gray-400"></i>
                    </div>
                    <input type="text" id="search-input" placeholder="Search by name, email or phone..."
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                        autocomplete="off">
                    <div id="search-results" class="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden hidden max-h-72 overflow-y-auto">
                        <!-- Search results will appear here -->
                    </div>
                </div>
                
                <div class="mt-6">
                    <p id="search-instructions" class="text-sm text-gray-600 italic">
                        Type at least 3 characters to search for participants
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Form Panel -->
        <div class="md:col-span-2">
            <div class="bg-white shadow-md rounded-lg p-6">
                <form id="check-in-form" method="POST" action="{{ route('usher.check-in.process') }}">
                    @csrf
                    <input type="hidden" name="participant_id" id="participant_id">
                    
                    <div class="mb-6 hidden" id="participant-details-container">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                            <i data-lucide="user" class="h-5 w-5 mr-2 text-primary"></i>
                            Participant Details
                        </h2>
                        
                        <div class="bg-gray-50 border border-gray-100 rounded-lg p-5">
                            <div class="flex items-center mb-4">
                                <div class="h-12 w-12 rounded-full bg-primary text-white flex items-center justify-center text-xl font-bold" id="participant-initials">JS</div>
                                <div class="ml-4">
                                    <h3 class="text-xl font-semibold text-gray-800" id="participant-name"></h3>
                                    <p class="text-sm text-gray-500" id="participant-role"></p>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="flex items-center">
                                    <i data-lucide="mail" class="h-4 w-4 text-gray-500 mr-2"></i>
                                    <span class="text-gray-800" id="participant-email"></span>
                                </div>
                                <div class="flex items-center">
                                    <i data-lucide="phone" class="h-4 w-4 text-gray-500 mr-2"></i>
                                    <span class="text-gray-800" id="participant-phone"></span>
                                </div>
                                <div class="flex items-center">
                                    <i data-lucide="briefcase" class="h-4 w-4 text-gray-500 mr-2"></i>
                                    <span class="text-gray-800" id="participant-organization"></span>
                                </div>
                            </div>

                            <!-- Ticket Information Section -->
                            <div class="mt-5 pt-5 border-t border-gray-200">
                                <h4 class="font-medium text-gray-800 mb-3 flex items-center">
                                    <i data-lucide="ticket" class="h-4 w-4 mr-2 text-primary"></i>
                                    Ticket Information
                                </h4>
                                
                                <div id="no-ticket-info" class="hidden">
                                    <p class="text-gray-600 italic">No ticket information available</p>
                                </div>
                                
                                <div id="ticket-info" class="hidden">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-sm text-gray-600">Ticket Number</p>
                                            <p class="font-medium" id="ticket-number"></p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600">Status</p>
                                            <p class="font-medium">
                                                <span id="ticket-status-badge" class="px-2 py-1 rounded-full text-xs font-semibold"></span>
                                            </p>
                                        </div>
                                        <div id="ticket-expiration-container">
                                            <p class="text-sm text-gray-600">Expires</p>
                                            <p class="font-medium" id="ticket-expiration"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Conference Day Selection Card -->
                    <div class="mb-6">
                        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm transition-all hover:shadow-md">
                            <div class="p-5">
                                <div class="flex items-center mb-4">
                                    <div class="p-2 rounded-full bg-blue-50 text-blue-600 mr-3">
                                        <i data-lucide="calendar-days" class="h-5 w-5"></i>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-800">Select Conference Day</h3>
                                </div>
                                
                                <div class="relative">
                                    <select 
                                        name="conference_day_id" 
                                        id="conference_day_id" 
                                        class="w-full pl-12 pr-10 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all appearance-none bg-white"
                                        style="background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%239ca3af' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 0.75rem center; background-size: 1.5rem;"
                                    >
                                        <option value="" disabled {{ !old('conference_day_id', $today ? $today->id : '') ? 'selected' : '' }}>Choose a conference day</option>
                                        @foreach($days as $day)
                                            <option 
                                                value="{{ $day->id }}" 
                                                {{ (old('conference_day_id', $today ? $today->id : '') == $day->id) ? 'selected' : '' }}
                                                data-date="{{ $day->date->format('Y-m-d') }}"
                                                class="flex items-center"
                                            >
                                                <span class="font-medium">{{ $day->name }}</span>
                                                <span class="text-gray-500 ml-2">• {{ $day->date->format('D, M j, Y') }}</span>
                                                @if($day->date->isToday())
                                                    <span class="ml-2 px-2 py-0.5 bg-green-100 text-green-800 text-xs font-medium rounded-full">Today</span>
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i data-lucide="calendar-days" class="h-5 w-5 text-gray-400"></i>
                                    </div>
                                    
                                    @error('conference_day_id')
                                        <div class="mt-2 flex items-center text-red-600">
                                            <i data-lucide="alert-circle" class="h-4 w-4 mr-1"></i>
                                            <span class="text-sm">{{ $message }}</span>
                                        </div>
                                    @enderror
                                </div>
                                
                                <div id="day-info" class="mt-3 p-3 bg-blue-50 rounded-lg hidden">
                                    <div class="flex items-start">
                                        <i data-lucide="info" class="h-4 w-4 text-blue-600 mt-0.5 mr-2 flex-shrink-0"></i>
                                        <p class="text-sm text-blue-700" id="selected-day-info"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 font-semibold mb-2" for="notes">
                            Notes (Optional)
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 pt-2 pointer-events-none">
                                <i data-lucide="file-text" class="h-5 w-5 text-gray-400"></i>
                            </div>
                            <textarea name="notes" id="notes" rows="3" 
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                                placeholder="Add any notes about this check-in..."></textarea>
                        </div>
                    </div>

                    <div class="flex items-center justify-between mt-8">
                        <p id="check-in-message" class="text-gray-600 italic">Please select a participant to check in</p>
                        <button type="submit" id="check-in-button" disabled
                            class="bg-primary hover:bg-primary-dark text-white font-bold py-3 px-6 rounded-lg disabled:bg-gray-300 disabled:cursor-not-allowed transition-colors flex items-center">
                            <i data-lucide="check-circle" class="h-5 w-5 mr-2"></i>
                            Check In Participant
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-input');
    const searchResults = document.getElementById('search-results');
    const searchInstructions = document.getElementById('search-instructions');
    const participantIdField = document.getElementById('participant_id');
    const participantDetailsContainer = document.getElementById('participant-details-container');
    const participantName = document.getElementById('participant-name');
    const participantEmail = document.getElementById('participant-email');
    const participantPhone = document.getElementById('participant-phone');
    const participantOrganization = document.getElementById('participant-organization');
    const participantRole = document.getElementById('participant-role');
    const participantInitials = document.getElementById('participant-initials');
    
    // Ticket info elements
    const ticketInfo = document.getElementById('ticket-info');
    const noTicketInfo = document.getElementById('no-ticket-info');
    const ticketNumber = document.getElementById('ticket-number');
    const ticketStatusBadge = document.getElementById('ticket-status-badge');
    const ticketExpirationContainer = document.getElementById('ticket-expiration-container');
    const ticketExpiration = document.getElementById('ticket-expiration');
    
    const checkInButton = document.getElementById('check-in-button');
    const checkInMessage = document.getElementById('check-in-message');

    let searchTimeout;

    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        
        searchTimeout = setTimeout(function() {
            const query = searchInput.value.trim();
            
            if (query.length < 3) {
                searchResults.innerHTML = '';
                searchResults.classList.add('hidden');
                return;
            }

            fetch(`{{ route('usher.check-in.search') }}?query=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    searchResults.innerHTML = '';
                    
                    if (data.participants.length === 0) {
                        const noResults = document.createElement('div');
                        noResults.className = 'px-4 py-3 text-gray-600';
                        noResults.textContent = 'No participants found';
                        searchResults.appendChild(noResults);
                        searchInstructions.textContent = 'No matching participants found';
                    } else {
                        searchInstructions.textContent = `Found ${data.participants.length} matching participants`;
                        data.participants.forEach(participant => {
                            const item = document.createElement('div');
                            item.className = 'px-4 py-3 hover:bg-gray-50 cursor-pointer border-b last:border-b-0';
                            
                            const name = document.createElement('div');
                            name.className = 'font-medium';
                            name.textContent = participant.full_name;
                            
                            const email = document.createElement('div');
                            email.className = 'text-sm text-gray-600';
                            email.textContent = participant.email;
                            
                            // Add ticket information
                            const ticketInfoRow = document.createElement('div');
                            ticketInfoRow.className = 'flex justify-between items-center mt-1';
                            
                            // Set ticket status badge
                            let leftContent = '';
                            let badgeClass = 'bg-gray-200 text-gray-800'; // Default
                            
                            if (participant.ticket_status === 'Expired' || participant.ticket_status === 'Inactive') {
                                badgeClass = 'bg-red-100 text-red-800';
                            } else if (participant.ticket_status.includes('Valid')) {
                                badgeClass = 'bg-green-100 text-green-800';
                            }
                            
                            leftContent = `<span class="inline-block px-2 py-0.5 rounded-full text-xs ${badgeClass}">${participant.ticket_status}</span>`;
                            
                            // Add ticket count if multiple tickets exist
                            let rightContent = '';
                            if (participant.has_multiple_tickets) {
                                rightContent = `
                                    <a href="${participant.view_tickets_url}" class="text-xs text-blue-600 hover:text-blue-800">
                                        View ${participant.ticket_count} tickets
                                    </a>
                                `;
                            }
                            
                            ticketInfoRow.innerHTML = `
                                <div>${leftContent}</div>
                                <div>${rightContent}</div>
                            `;
                            
                            item.appendChild(name);
                            item.appendChild(email);
                            item.appendChild(ticketInfoRow);
                            
                            item.addEventListener('click', function() {
                                selectParticipant(participant);
                            });
                            
                            searchResults.appendChild(item);
                        });
                    }
                    
                    searchResults.classList.remove('hidden');
                })
                .catch(error => console.error('Error searching participants:', error));
        }, 300);
    });
    
    // Hide search results when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.classList.add('hidden');
        }
    });
    
    // Handle form submission
    document.getElementById('check-in-form').addEventListener('submit', function(e) {
        const participantId = participantIdField.value;
        const conferenceDay = document.getElementById('conference_day_id').value;
        
        if (!participantId) {
            e.preventDefault();
            showAlert('Please select a participant first.', 'error');
            return false;
        }
        
        if (!conferenceDay) {
            e.preventDefault();
            showAlert('Please select a conference day.', 'error');
            return false;
        }
    });
    
    // Function to show alert messages
    function showAlert(message, type = 'error') {
        // Remove any existing alerts
        const existingAlert = document.querySelector('.alert-message');
        if (existingAlert) {
            existingAlert.remove();
        }
        
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert-message fixed top-4 right-4 p-4 rounded-lg shadow-lg ${type === 'error' ? 'bg-red-100 border-l-4 border-red-500 text-red-700' : 'bg-green-100 border-l-4 border-green-500 text-green-700'}`;
        alertDiv.role = 'alert';
        
        const icon = type === 'error' ? 'alert-triangle' : 'check-circle';
        alertDiv.innerHTML = `
            <div class="flex items-center">
                <i data-lucide="${icon}" class="h-6 w-6 mr-2"></i>
                <span>${message}</span>
            </div>
        `;
        
        document.body.appendChild(alertDiv);
        lucide.createIcons();
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }
    
    // Handle conference day selection
    document.addEventListener('DOMContentLoaded', function() {
        const conferenceDaySelect = document.getElementById('conference_day_id');
        const dayInfo = document.getElementById('day-info');
        const selectedDayInfo = document.getElementById('selected-day-info');
        
        if (conferenceDaySelect) {
            // Show info for initially selected day if not the placeholder
            if (conferenceDaySelect.value) {
                updateDayInfo(conferenceDaySelect);
            }
            
            // Update info when selection changes
            conferenceDaySelect.addEventListener('change', function() {
                updateDayInfo(this);
            });
        }
        
        function updateDayInfo(selectElement) {
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            const date = selectedOption.dataset.date;
            
            if (date) {
                const dayName = new Date(date).toLocaleDateString('en-US', { weekday: 'long' });
                const formattedDate = new Date(date).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
                
                dayInfo.classList.remove('hidden');
                selectedDayInfo.innerHTML = `
                    <strong>${selectedOption.textContent.split('•')[0].trim()}</strong> on 
                    <span class="font-medium">${dayName}, ${formattedDate}</span>.
                    ${selectedOption.text.includes('Today') ? 'This is today\'s session.' : ''}
                `;
            } else {
                dayInfo.classList.add('hidden');
            }
        }
    });
    
    // Function to get initials from name
    function getInitials(name) {
        return name
            .split(' ')
            .map(word => word[0])
            .join('')
            .substring(0, 2)
            .toUpperCase();
    }
    
    // Function to select a participant
    function selectParticipant(participant) {
        participantIdField.value = participant.id;
        searchInput.value = participant.full_name;
        searchResults.classList.add('hidden');
        searchInstructions.textContent = 'Participant selected';
        
        // Show participant details
        participantName.textContent = participant.full_name;
        participantEmail.textContent = participant.email || 'N/A';
        participantPhone.textContent = participant.phone_number || 'N/A';
        participantOrganization.textContent = participant.organization || 'N/A';
        participantRole.textContent = participant.role || 'Attendee';
        participantInitials.textContent = getInitials(participant.full_name);
        participantDetailsContainer.classList.remove('hidden');
        
        // Handle ticket information
        if (participant.ticket_number) {
            ticketInfo.classList.remove('hidden');
            noTicketInfo.classList.add('hidden');
            
            ticketNumber.textContent = participant.ticket_number;
            
            // Set status badge with appropriate color
            let badgeClass = 'bg-gray-200 text-gray-800'; // Default
            
            if (participant.ticket_status === 'Expired' || participant.ticket_status === 'Inactive') {
                badgeClass = 'bg-red-100 text-red-800';
            } else if (participant.ticket_status.includes('Valid')) {
                badgeClass = 'bg-green-100 text-green-800';
            }
            
            ticketStatusBadge.className = `px-2 py-1 rounded-full text-xs font-semibold ${badgeClass}`;
            ticketStatusBadge.textContent = participant.ticket_status;
            
            if (participant.has_multiple_tickets) {
                ticketStatusBadge.innerHTML += ` <a href="${participant.view_tickets_url}" class="ml-2 text-xs text-blue-600 hover:underline">(${participant.ticket_count} tickets)</a>`;
            }
            
            // Show expiration date if available
            if (participant.ticket_expiration_date) {
                ticketExpirationContainer.classList.remove('hidden');
                ticketExpiration.textContent = participant.ticket_expiration_date;
            } else {
                ticketExpirationContainer.classList.add('hidden');
            }
        } else {
            ticketInfo.classList.add('hidden');
            noTicketInfo.classList.remove('hidden');
        }
        
        // Enable check-in button
        checkInButton.disabled = false;
        checkInMessage.textContent = 'Ready to check in this participant';
    }
});
</script>
@endsection 