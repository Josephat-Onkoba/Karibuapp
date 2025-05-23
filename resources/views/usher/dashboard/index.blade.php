@extends('layouts.app')

@section('title', 'Usher Dashboard')

@section('content')
    <div class="container mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-[#041E42]">Usher Dashboard</h1>
            <div class="flex space-x-2">
                <button class="bg-white p-2 rounded-lg shadow hover:shadow-md border border-gray-300 relative">
                    <i data-lucide="bell" class="h-5 w-5 text-gray-500"></i>
                    @if($pendingCheckIns > 0)
                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                        {{ $pendingCheckIns > 9 ? '9+' : $pendingCheckIns }}
                    </span>
                    @endif
                </button>
                <button class="bg-white p-2 rounded-lg shadow hover:shadow-md border border-gray-300">
                    <i data-lucide="refresh-cw" class="h-5 w-5 text-gray-500"></i>
                </button>
            </div>
        </div>
        
        <!-- Summary Stats Section -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-lg shadow border border-gray-200 hover:shadow-lg transition duration-300 flex items-center">
                <div class="rounded-full bg-blue-100 p-3 mr-4 border border-gray-200">
                    <i data-lucide="users" class="h-6 w-6 text-[#041E42]"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Visitors Today</p>
                    <h3 class="text-2xl font-bold text-[#041E42]">{{ $todayVisitorsCount }}</h3>
                    @if($today)
                    <p class="text-xs text-gray-600 flex items-center">
                        <span>{{ $today->name ?? \Carbon\Carbon::parse($today->date)->format('F j, Y') }}</span>
                    </p>
                    @else
                    <p class="text-xs text-yellow-600 flex items-center">
                        <i data-lucide="alert-circle" class="h-3 w-3 mr-1"></i>
                        <span>No conference today</span>
                    </p>
                    @endif
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow border border-gray-200 hover:shadow-lg transition duration-300 flex items-center">
                <div class="rounded-full bg-blue-100 p-3 mr-4 border border-gray-200">
                    <i data-lucide="user-plus" class="h-6 w-6 text-[#041E42]"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Your Registrations</p>
                    <h3 class="text-2xl font-bold text-[#041E42]">{{ $registeredByYou }}</h3>
                    <p class="text-xs text-gray-600 flex items-center">
                        <span>Total participants registered</span>
                    </p>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow border border-gray-200 hover:shadow-lg transition duration-300 flex items-center">
                <div class="rounded-full bg-{{ $pendingCheckIns > 0 ? 'yellow' : 'blue' }}-100 p-3 mr-4 border border-gray-200">
                    <i data-lucide="{{ $pendingCheckIns > 0 ? 'alert-triangle' : 'clock' }}" class="h-6 w-6 text-{{ $pendingCheckIns > 0 ? 'yellow-600' : '[#041E42]' }}"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Pending Check-ins</p>
                    <h3 class="text-2xl font-bold text-{{ $pendingCheckIns > 0 ? 'yellow-600' : '[#041E42]' }}">{{ $pendingCheckIns }}</h3>
                    <p class="text-xs text-{{ $pendingCheckIns > 0 ? 'yellow-600' : 'gray-600' }} flex items-center">
                        @if($pendingCheckIns > 0)
                        <i data-lucide="alert-circle" class="h-3 w-3 mr-1"></i>
                        <span>Participants need check-in</span>
                        @else
                        <span>All participants checked in</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions Grid -->
        <h2 class="text-xl font-semibold text-[#041E42] mb-4">Quick Actions</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
            <a href="{{ route('usher.register') }}" class="bg-white p-6 rounded-lg shadow border border-gray-200 hover:shadow-lg transition duration-300 flex items-center group">
                <div class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 group-hover:bg-blue-200 mr-4 border border-gray-200">
                    <i data-lucide="user-plus" class="h-6 w-6 text-[#041E42]"></i>
                </div>
                <div class="flex-1">
                    <h3 class="font-semibold text-lg mb-1 text-[#041E42]">Registration</h3>
                    <p class="text-gray-500 text-sm">Register and check-in participants</p>
                </div>
                <i data-lucide="chevron-right" class="h-5 w-5 text-gray-400 group-hover:text-[#041E42]"></i>
            </a>
            
            <a href="{{ route('usher.my-registrations') }}" class="bg-white p-6 rounded-lg shadow border border-gray-200 hover:shadow-lg transition duration-300 flex items-center group">
                <div class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-{{ $pendingCheckIns > 0 ? 'yellow' : 'blue' }}-100 group-hover:bg-{{ $pendingCheckIns > 0 ? 'yellow' : 'blue' }}-200 mr-4 border border-gray-200 relative">
                    <i data-lucide="clipboard-list" class="h-6 w-6 text-{{ $pendingCheckIns > 0 ? 'yellow-600' : '[#041E42]' }}"></i>
                    @if($pendingCheckIns > 0)
                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                        {{ $pendingCheckIns > 9 ? '9+' : $pendingCheckIns }}
                    </span>
                    @endif
                </div>
                <div class="flex-1">
                    <h3 class="font-semibold text-lg mb-1 text-[#041E42]">My Registrations</h3>
                    <p class="text-gray-500 text-sm">{{ $pendingCheckIns > 0 ? 'You have participants waiting to be checked in' : 'View and manage your registrations' }}</p>
                </div>
                <i data-lucide="chevron-right" class="h-5 w-5 text-gray-400 group-hover:text-[#041E42]"></i>
            </a>
        </div>
        
        <!-- Recent Visitors -->
        <h2 class="text-xl font-semibold text-[#041E42] mb-4">Recent Visitors</h2>
        <div class="bg-white rounded-lg shadow overflow-hidden mb-8 border border-gray-200">
            <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                <h3 class="font-medium text-[#041E42]">Latest Check-ins</h3>
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <input type="text" 
                               placeholder="Search visitors" 
                               class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-[#041E42] focus:border-[#041E42] text-sm w-full" />
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="search" class="h-4 w-4 text-gray-400"></i>
                        </div>
                    </div>
                    <button class="text-[#041E42] hover:underline text-sm flex items-center">
                        <span>View All</span>
                        <i data-lucide="chevron-right" class="h-4 w-4 ml-1"></i>
                    </button>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-3 bg-gray-50 border-b border-gray-200">Visitor</th>
                            <th class="px-6 py-3 bg-gray-50 border-b border-gray-200">Time</th>
                            <th class="px-6 py-3 bg-gray-50 border-b border-gray-200">Purpose</th>
                            <th class="px-6 py-3 bg-gray-50 border-b border-gray-200">Status</th>
                            <th class="px-6 py-3 bg-gray-50 border-b border-gray-200">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <!-- Placeholder loading state -->
                        <tr class="animate-pulse">
                            <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <i data-lucide="loader" class="h-8 w-8 mb-2"></i>
                                    <div class="h-4 w-48 bg-gray-200 rounded"></div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Empty state -->
            <div class="p-10 text-center text-gray-500">
                <div class="flex flex-col items-center">
                    <i data-lucide="clipboard" class="h-12 w-12 mb-4 text-gray-300"></i>
                    <h3 class="text-lg font-medium mb-2 text-gray-700">No visitors yet</h3>
                    <p class="mb-4 max-w-sm mx-auto">When visitors check in, they'll appear here. Start by registering a new visitor.</p>
                    <a href="{{ route('usher.register') }}" class="bg-[#041E42] hover:bg-[#0A2E5C] text-white px-4 py-2 rounded-lg transition duration-300 border border-[#021530]">
                        Registration
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection 