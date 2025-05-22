<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
        <div class="bg-gray-50 p-4 rounded-lg mb-4">
            <h4 class="text-lg font-medium text-[#041E42] mb-3">Personal Information</h4>
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-medium text-gray-500">Full Name</label>
                    <div class="mt-1 text-sm font-medium">{{ $participant->full_name }}</div>
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-gray-500">Email Address</label>
                    <div class="mt-1 text-sm">{{ $participant->email }}</div>
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-gray-500">Phone Number</label>
                    <div class="mt-1 text-sm">{{ $participant->phone_number }}</div>
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-gray-500">Organization</label>
                    <div class="mt-1 text-sm">{{ $participant->organization ?? 'Not specified' }}</div>
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-gray-500">Job Title</label>
                    <div class="mt-1 text-sm">{{ $participant->job_title ?? 'Not specified' }}</div>
                </div>
            </div>
        </div>
        
        <div class="bg-gray-50 p-4 rounded-lg">
            <h4 class="text-lg font-medium text-[#041E42] mb-3">Registration Info</h4>
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-medium text-gray-500">Category</label>
                    <div class="mt-1 text-sm">
                        <span class="px-2 py-0.5 text-xs font-medium rounded-full 
                            @if($participant->category == 'general') bg-blue-100 text-blue-800 
                            @elseif($participant->category == 'invited') bg-purple-100 text-purple-800 
                            @elseif($participant->category == 'internal') bg-green-100 text-green-800 
                            @else bg-yellow-100 text-yellow-800 @endif">
                            {{ ucfirst($participant->category) }}
                        </span>
                    </div>
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-gray-500">Role</label>
                    <div class="mt-1 text-sm">{{ $participant->role }}</div>
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-gray-500">Registration Date</label>
                    <div class="mt-1 text-sm">{{ $participant->created_at->format('F j, Y g:i A') }}</div>
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-gray-500">Registered By</label>
                    <div class="mt-1 text-sm">{{ $participant->registeredBy->name ?? 'Unknown' }}</div>
                </div>
                
                @if($participant->category === 'general')
                <div class="mt-3 pt-3 border-t border-gray-100">
                    <label class="block text-xs font-medium text-gray-500">Payment Status</label>
                    <div class="mt-1">
                        <span class="px-2 py-0.5 text-xs font-medium rounded-full 
                            @if($participant->payment_status === 'Paid via M-Pesa' || $participant->payment_status === 'Paid via Vabu') 
                                bg-green-100 text-green-800
                            @elseif($participant->payment_status === 'Not Paid')
                                bg-red-100 text-red-800
                            @else
                                bg-blue-100 text-blue-800
                            @endif">
                            {{ $participant->payment_status }}
                        </span>
                        
                        @if($participant->payment_confirmed)
                        <span class="ml-2 px-2 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-800">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 inline mr-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            Verified
                        </span>
                        @endif
                    </div>
                </div>
                @endif
                
                @if($participant->notes)
                <div>
                    <label class="block text-xs font-medium text-gray-500">Notes</label>
                    <div class="mt-1 text-sm bg-white p-2 rounded border border-gray-100">{{ $participant->notes }}</div>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <div>
        <div class="bg-gray-50 p-4 rounded-lg mb-4">
            <h4 class="text-lg font-medium text-[#041E42] mb-3">Ticket Information</h4>
            @if($participant->ticket)
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-medium text-gray-500">Ticket Number</label>
                    <div class="mt-1 text-sm font-medium">{{ $participant->ticket->ticket_number }}</div>
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-gray-500">Valid Days</label>
                    <div class="mt-1 flex space-x-1">
                        @if($participant->ticket->day1_valid)
                        <span class="px-1.5 py-0.5 text-xs font-medium rounded bg-green-100 text-green-800">Day 1</span>
                        @endif
                        @if($participant->ticket->day2_valid)
                        <span class="px-1.5 py-0.5 text-xs font-medium rounded bg-green-100 text-green-800">Day 2</span>
                        @endif
                        @if($participant->ticket->day3_valid)
                        <span class="px-1.5 py-0.5 text-xs font-medium rounded bg-green-100 text-green-800">Day 3</span>
                        @endif
                    </div>
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-gray-500">Ticket Status</label>
                    <div class="mt-1 text-sm">
                        @php
                            $statusClass = 'bg-gray-100 text-gray-800';
                            $statusText = $participant->ticket->getValidityStatus();
                            
                            if (!$participant->ticket->active) {
                                $statusClass = 'bg-red-100 text-red-800';
                            } elseif ($participant->ticket->isExpired()) {
                                $statusClass = 'bg-red-100 text-red-800';
                            } elseif (strpos($statusText, 'Valid') !== false) {
                                $statusClass = 'bg-green-100 text-green-800';
                            }
                        @endphp
                        
                        <span class="px-2 py-0.5 text-xs font-medium rounded-full {{ $statusClass }}">
                            {{ $statusText }}
                        </span>
                    </div>
                </div>
                
                @if($participant->ticket->expiration_date)
                <div>
                    <label class="block text-xs font-medium text-gray-500">Expires</label>
                    <div class="mt-1 text-sm">
                        {{ $participant->ticket->expiration_date->format('M j, Y g:i A') }} 
                        <span class="text-xs text-gray-500">({{ $participant->ticket->expiration_date->diffForHumans() }})</span>
                    </div>
                </div>
                @endif
                
                <div>
                    <label class="block text-xs font-medium text-gray-500">Created</label>
                    <div class="mt-1 text-sm">{{ $participant->ticket->created_at->format('M j, Y g:i A') }}</div>
                </div>
                
                @if(!$participant->ticket->active)
                <div class="mt-3 pt-3 border-t border-gray-100">
                    <div class="bg-yellow-50 p-2 rounded-md border border-yellow-200">
                        <p class="text-xs text-yellow-700">
                            <i data-lucide="alert-triangle" class="h-3 w-3 inline-block mr-1"></i>
                            This ticket is inactive. A new ticket may be generated when checking in the participant.
                        </p>
                    </div>
                </div>
                @elseif($participant->ticket->isExpired())
                <div class="mt-3 pt-3 border-t border-gray-100">
                    <div class="bg-red-50 p-2 rounded-md border border-red-200">
                        <p class="text-xs text-red-700">
                            <i data-lucide="clock" class="h-3 w-3 inline-block mr-1"></i>
                            This ticket has expired. A new ticket will be generated when checking in the participant.
                        </p>
                    </div>
                </div>
                @endif
            </div>
            @else
            <div class="py-4 text-center">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">No Ticket Assigned</span>
            </div>
            @endif
        </div>
        
        <div class="bg-gray-50 p-4 rounded-lg">
            <h4 class="text-lg font-medium text-[#041E42] mb-3">Check-in Status</h4>
            <div class="space-y-2">
                @foreach($checkInInfo as $dayId => $dayInfo)
                <div class="flex items-center justify-between p-2 {{ $loop->index % 2 == 0 ? 'bg-white' : 'bg-gray-50' }} rounded border border-gray-100">
                    <div>
                        <p class="text-sm font-medium">{{ $dayInfo['day_name'] }}</p>
                        <p class="text-xs text-gray-500">{{ $dayInfo['day_date'] }}</p>
                    </div>
                    <div class="flex flex-col items-end">
                        @if(!$dayInfo['is_valid'])
                        <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-gray-100 text-gray-800">Not Valid</span>
                        @elseif($dayInfo['is_checked_in'])
                        <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-800">Checked In</span>
                        <span class="text-xs text-gray-500 mt-0.5">{{ $dayInfo['checked_in_at'] }}</span>
                        @else
                        <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">Not Checked In</span>
                        @if(isset($today) && $today->id == $dayId)
                        <form action="{{ route('usher.check-in.process') }}" method="POST" class="mt-1">
                            @csrf
                            <input type="hidden" name="participant_id" value="{{ $participant->id }}">
                            <input type="hidden" name="conference_day_id" value="{{ $dayId }}">
                            <input type="hidden" name="redirect_to" value="participant_view">
                            <input type="hidden" name="participant_id_redirect" value="{{ $participant->id }}">
                            <button type="submit" class="text-xs text-green-600 hover:text-green-800">
                                Check In Now
                            </button>
                        </form>
                        @endif
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<div class="mt-6 flex justify-end">
    <a href="{{ $participant->ticket ? route('usher.registration.ticket', $participant->ticket->id) : '#' }}" 
       class="inline-flex items-center px-3 py-2 border border-blue-300 text-sm font-medium rounded shadow-sm text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 {{ !$participant->ticket ? 'opacity-50 cursor-not-allowed' : '' }}">
        <i data-lucide="ticket" class="h-4 w-4 mr-1.5"></i>
        View Ticket
    </a>
</div> 