@extends('layouts.app')

@section('title', 'Usher Dashboard')

@section('content')
<div class="container mx-auto px-4 max-w-full overflow-hidden">
    <!-- Header with welcome message and system status -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 bg-gradient-to-r from-[#041E42] to-[#0A2E5C] p-4 rounded-lg text-white shadow-lg">
        <div>
            <h1 class="text-2xl font-bold">Welcome back, {{ $usherName ?? 'Usher' }}!</h1>
            
        </div>
    </div>
        
    <!-- Summary Stats Section with Improved Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white p-4 rounded-lg shadow hover:shadow-lg transition duration-300">
            <div class="flex items-center mb-3">
                <div class="rounded-full bg-blue-100 p-3 mr-3 border border-blue-200">
                    <i data-lucide="users" class="h-5 w-5 text-blue-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Visitors Today</p>
                    <h3 class="text-2xl font-bold text-[#041E42]">{{ $todayVisitorsCount }}</h3>
                </div>
            </div>
            <div class="flex justify-between items-center text-xs">
                @if($today)
                <span class="text-green-600 bg-green-100 px-2 py-0.5 rounded-full font-medium">
                    {{ $today->name ?? \Carbon\Carbon::parse($today->date)->format('F j') }}
                </span>
                @else
                <span class="text-yellow-600 bg-yellow-100 px-2 py-0.5 rounded-full font-medium flex items-center">
                    <i data-lucide="alert-circle" class="h-3 w-3 mr-1"></i>
                    <span>No event today</span>
                </span>
                @endif
                <a href="#" class="text-blue-500 hover:underline">Details</a>
            </div>
        </div>
        
        <div class="bg-white p-4 rounded-lg shadow hover:shadow-lg transition duration-300">
            <div class="flex items-center mb-3">
                <div class="rounded-full bg-purple-100 p-3 mr-3 border border-purple-200">
                    <i data-lucide="user-plus" class="h-5 w-5 text-purple-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Your Registrations</p>
                    <h3 class="text-2xl font-bold text-[#041E42]">{{ $registeredByYou }}</h3>
                </div>
            </div>
            <div class="flex justify-between items-center text-xs">
                <span class="text-purple-600 bg-purple-100 px-2 py-0.5 rounded-full font-medium">
                    {{ round(($registeredByYou > 0) ? (($registeredByYou - $pendingCheckIns) / $registeredByYou * 100) : 0) }}% checked in
                </span>
                <a href="{{ route('usher.registration.my-registrations') }}" class="text-blue-500 hover:underline">View all</a>
            </div>
        </div>
        
        <div class="bg-white p-4 rounded-lg shadow hover:shadow-lg transition duration-300">
            <div class="flex items-center mb-3">
                <div class="rounded-full bg-{{ $pendingCheckIns > 0 ? 'amber' : 'green' }}-100 p-3 mr-3 border border-{{ $pendingCheckIns > 0 ? 'amber' : 'green' }}-200">
                    <i data-lucide="{{ $pendingCheckIns > 0 ? 'alert-triangle' : 'check-circle' }}" 
                       class="h-5 w-5 text-{{ $pendingCheckIns > 0 ? 'amber-600' : 'green-600' }}"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Pending Check-ins</p>
                    <h3 class="text-2xl font-bold text-{{ $pendingCheckIns > 0 ? 'amber-600' : '[#041E42]' }}">{{ $pendingCheckIns }}</h3>
                </div>
            </div>
            <div class="flex justify-between items-center text-xs">
                <span class="text-{{ $pendingCheckIns > 0 ? 'amber' : 'green' }}-600 bg-{{ $pendingCheckIns > 0 ? 'amber' : 'green' }}-100 px-2 py-0.5 rounded-full font-medium">
                    @if($pendingCheckIns > 0)
                    Needs attention
                    @else
                    All checked in
                    @endif
                </span>
                <a href="{{ route('usher.check-in') }}" class="text-blue-500 hover:underline">Check-in</a>
            </div>
        </div>
        
        <div class="bg-white p-4 rounded-lg shadow hover:shadow-lg transition duration-300">
            <div class="flex items-center mb-3">
                <div class="rounded-full bg-blue-100 p-3 mr-3 border border-blue-200">
                    <i data-lucide="calendar" class="h-5 w-5 text-blue-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Conference Days</p>
                    <h3 class="text-2xl font-bold text-[#041E42]">{{ $conferenceDays->count() }}</h3>
                </div>
            </div>
            <div class="flex justify-between items-center text-xs">
                <div class="flex space-x-1">
                    @foreach($conferenceDays as $day)
                        @php $isToday = $today && $day->id == $today->id; @endphp
                        <span class="h-2 w-2 rounded-full {{ $isToday ? 'bg-green-500' : 'bg-gray-300' }}"></span>
                    @endforeach
                </div>
                <button id="view-schedule" class="text-blue-500 hover:underline">Schedule</button>
            </div>
        </div>
    </div>
    
    <!-- Analytics Section with Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8 overflow-hidden">
        <!-- Registration Trend Chart -->
        <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-[#041E42]">Registration Trend</h2>
                <div class="text-xs text-gray-500">Last 7 days</div>
            </div>
            <div class="h-64">
                <canvas id="registrationTrendChart"></canvas>
            </div>
        </div>
        
        <!-- Category & Payment Distribution -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Role Distribution -->
            <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
                <h2 class="text-lg font-semibold text-[#041E42] mb-4">Participant Roles</h2>
                <div class="h-48">
                    <canvas id="roleDistributionChart"></canvas>
                </div>
                <div class="mt-4 text-xs text-gray-500">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        @foreach($roleDistribution as $role)
                        <div class="flex items-center">
                            <span class="w-3 h-3 rounded-full bg-{{ ['blue', 'green', 'purple', 'orange', 'yellow'][$loop->index % 5] }}-400 mr-1"></span>
                            <span>{{ $role->role ?: 'Unspecified' }}: {{ $role->count }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            
            <!-- Payment Status -->
            <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
                <h2 class="text-lg font-semibold text-[#041E42] mb-4">Payment Status</h2>
                <div class="h-48">
                    <canvas id="paymentStatusChart"></canvas>
                </div>
                <div class="mt-4 text-xs text-gray-500">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        @foreach($paymentStatusOverview as $status)
                        <div class="flex items-center">
                            @php
                                $color = 'gray';
                                if (strpos($status->payment_status, 'Paid') !== false) $color = 'green';
                                if ($status->payment_status == 'Not Paid') $color = 'red';
                                if ($status->payment_status == 'Waived') $color = 'blue';
                            @endphp
                            <span class="w-3 h-3 rounded-full bg-{{ $color }}-400 mr-1"></span>
                            <span>{{ $status->payment_status }}: {{ $status->count }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
        
    <!-- Quick Actions Section with More Options -->
    <div class="mb-8">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-[#041E42]">Quick Actions</h2>
            <button class="text-sm text-blue-500 hover:underline flex items-center">
                <span>Customize</span>
                <i data-lucide="settings" class="h-4 w-4 ml-1"></i>
            </button>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
            <!-- Registration -->
            <a href="{{ route('usher.register') }}" class="bg-white p-4 rounded-lg shadow border border-gray-200 hover:shadow-lg hover:border-blue-300 transition duration-300 flex items-center group">
                <div class="flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-full bg-blue-100 group-hover:bg-blue-200 mr-3 border border-blue-200">
                    <i data-lucide="user-plus" class="h-5 w-5 text-blue-600"></i>
                </div>
                <div class="flex-1">
                    <h3 class="font-semibold mb-0.5 text-[#041E42] group-hover:text-blue-600">Registration</h3>
                    <p class="text-gray-500 text-xs">Add new participants</p>
                </div>
            </a>
            
            <!-- Quick Check-in -->
            <a href="{{ route('usher.check-in') }}" class="bg-white p-4 rounded-lg shadow border border-gray-200 hover:shadow-lg hover:border-green-300 transition duration-300 flex items-center group">
                <div class="flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-full bg-green-100 group-hover:bg-green-200 mr-3 border border-green-200">
                    <i data-lucide="check-circle" class="h-5 w-5 text-green-600"></i>
                </div>
                <div class="flex-1">
                    <h3 class="font-semibold mb-0.5 text-[#041E42] group-hover:text-green-600">Quick Check-in</h3>
                    <p class="text-gray-500 text-xs">Scan tickets or search</p>
                </div>
            </a>
            
            <!-- My Registrations -->
            <a href="{{ route('usher.registration.my-registrations') }}" class="bg-white p-4 rounded-lg shadow border border-gray-200 hover:shadow-lg hover:border-purple-300 transition duration-300 flex items-center group">
                <div class="flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-full bg-purple-100 group-hover:bg-purple-200 mr-3 border border-purple-200 relative">
                    <i data-lucide="clipboard-list" class="h-5 w-5 text-purple-600"></i>
                    @if($pendingCheckIns > 0)
                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                        {{ $pendingCheckIns > 9 ? '9+' : $pendingCheckIns }}
                    </span>
                    @endif
                </div>
                <div class="flex-1">
                    <h3 class="font-semibold mb-0.5 text-[#041E42] group-hover:text-purple-600">My Registrations</h3>
                    <p class="text-gray-500 text-xs">Manage your participants</p>
                </div>
            </a>
            
            <!-- Meals Management -->
            <a href="{{ route('usher.meals') }}" class="bg-white p-4 rounded-lg shadow border border-gray-200 hover:shadow-lg hover:border-amber-300 transition duration-300 flex items-center group">
                <div class="flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-full bg-amber-100 group-hover:bg-amber-200 mr-3 border border-amber-200">
                    <i data-lucide="utensils" class="h-5 w-5 text-amber-600"></i>
                </div>
                <div class="flex-1">
                    <h3 class="font-semibold mb-0.5 text-[#041E42] group-hover:text-amber-600">Meals</h3>
                    <p class="text-gray-500 text-xs">Manage meals service</p>
                </div>
            </a>
        </div>
    </div>
      <!-- Latest Check-ins Table -->
      <div class="mb-8 overflow-x-auto">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-[#041E42]">Latest Check-ins</h2>
            <a href="{{ route('usher.check-in') }}" class="text-sm text-blue-500 hover:underline flex items-center">
                <span>View All</span>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-1"><polyline points="9 18 15 12 9 6"></polyline></svg>
            </a>
        </div>
        
        <div class="bg-white rounded-lg shadow overflow-hidden border border-gray-200">
            <div class="overflow-x-auto -mx-4 sm:mx-0">
                <table class="w-full min-w-[800px]">
                    <thead>
                        <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-3 bg-gray-50 border-b border-gray-200">Participant</th>
                            <th class="px-6 py-3 bg-gray-50 border-b border-gray-200">Conference Day</th>
                            <th class="px-6 py-3 bg-gray-50 border-b border-gray-200">Role</th>
                            <th class="px-6 py-3 bg-gray-50 border-b border-gray-200">Time</th>
                            <th class="px-6 py-3 bg-gray-50 border-b border-gray-200">Checked By</th>
                            <th class="px-6 py-3 bg-gray-50 border-b border-gray-200">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($latestCheckIns as $checkIn)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-semibold">
                                        {{ strtoupper(substr($checkIn->participant->name, 0, 1)) }}
                                    </div>
                                    <div class="ml-2 md:ml-4">
                                        <div class="text-sm font-medium text-gray-900 truncate max-w-[120px] md:max-w-full">{{ $checkIn->participant->name }}</div>
                                        <div class="text-xs md:text-sm text-gray-500 truncate max-w-[120px] md:max-w-full">{{ $checkIn->participant->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    {{ $checkIn->conferenceDay->name ?? 'Day ' . $checkIn->conferenceDay->id }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $checkIn->participant->role ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $checkIn->participant->role ?: 'General' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span class="hidden sm:inline">{{ \Carbon\Carbon::parse($checkIn->checked_in_at)->format('M j, Y g:i A') }}</span>
                                <span class="sm:hidden">{{ \Carbon\Carbon::parse($checkIn->checked_in_at)->format('M j g:i A') }}</span>
                                <div class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($checkIn->checked_in_at)->diffForHumans() }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $checkIn->checkedBy->name ?? 'Unknown' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="#" class="text-indigo-600 hover:text-indigo-900 mr-3">View</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="text-gray-300 mb-4"><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><path d="M12 11h4"></path><path d="M12 16h4"></path><path d="M8 11h.01"></path><path d="M8 16h.01"></path></svg>
                                    <h3 class="text-lg font-medium mb-2 text-gray-700">No check-ins yet</h3>
                                    <p class="mb-4 max-w-xs sm:max-w-sm mx-auto text-sm sm:text-base">Check-ins will appear here once participants have been checked in.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    </div>
@endsection 

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Add responsive chart configuration
    Chart.defaults.maintainAspectRatio = false;
    Chart.defaults.responsive = true;
    Chart.defaults.plugins.legend.display = false;
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // No need for JavaScript date/time updates - using server-side rendering
        
        // Registration Trend Chart
        const registrationTrendCtx = document.getElementById('registrationTrendChart');
        if (registrationTrendCtx) {
            new Chart(registrationTrendCtx, {
                type: 'line',
                data: {
                    labels: @json($dateLabels),
                    datasets: [{
                        label: 'Registrations',
                        data: @json($registrationsByDay),
                        backgroundColor: 'rgba(4, 30, 66, 0.1)',
                        borderColor: 'rgba(4, 30, 66, 0.8)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true,
                        pointBackgroundColor: 'rgba(4, 30, 66, 1)',
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }
        
        // Role Distribution Chart
        const roleCtx = document.getElementById('roleDistributionChart');
        if (roleCtx) {
            const roleData = @json($roleDistribution);
            new Chart(roleCtx, {
                type: 'doughnut',
                data: {
                    labels: roleData.map(item => item.role || 'Unspecified'),
                    datasets: [{
                        data: roleData.map(item => item.count),
                        backgroundColor: [
                            'rgba(59, 130, 246, 0.8)', // blue - Delegate
                            'rgba(16, 185, 129, 0.8)', // green - Exhibitor
                            'rgba(139, 92, 246, 0.8)', // purple - Presenter
                            'rgba(249, 115, 22, 0.8)', // orange - Volunteer
                            'rgba(234, 179, 8, 0.8)',  // yellow - Student
                            'rgba(156, 163, 175, 0.8)' // gray - Other
                        ],
                        borderWidth: 1,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = Math.round((value / total) * 100);
                                    return `${label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    },
                    cutout: '70%'
                }
            });
        }
        
        // Payment Status Chart
        const paymentCtx = document.getElementById('paymentStatusChart');
        if (paymentCtx) {
            const paymentData = @json($paymentStatusOverview);
            const colors = paymentData.map(item => {
                if (item.payment_status.includes('Paid')) return 'rgba(16, 185, 129, 0.8)'; // green
                if (item.payment_status === 'Not Paid') return 'rgba(239, 68, 68, 0.8)'; // red
                if (item.payment_status === 'Waived') return 'rgba(59, 130, 246, 0.8)'; // blue
                return 'rgba(156, 163, 175, 0.8)'; // gray
            });
            
            new Chart(paymentCtx, {
                type: 'doughnut',
                data: {
                    labels: paymentData.map(item => item.payment_status),
                    datasets: [{
                        data: paymentData.map(item => item.count),
                        backgroundColor: colors,
                        borderWidth: 1,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    cutout: '70%'
                }
            });
        }
        
        // Participant Search
        const participantSearch = document.getElementById('participant-search');
        if (participantSearch) {
            participantSearch.addEventListener('keyup', function(e) {
                if (e.key === 'Enter') {
                    const searchTerm = this.value.trim();
                    if (searchTerm.length > 2) {
                        window.location.href = `{{ route('usher.registration.my-registrations') }}?search=${searchTerm}`;
                    }
                }
            });
        }
        
        // Schedule dialog
        const viewScheduleBtn = document.getElementById('view-schedule');
        if (viewScheduleBtn) {
            viewScheduleBtn.addEventListener('click', function() {
                // In a real implementation, this could show a modal with the full schedule
                alert('This would show the full conference schedule!');
            });
        }
    });
</script>
@endpush