@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="container mx-auto px-4">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-[#041E42]">Admin Dashboard</h1>
                <p class="text-gray-600">Welcome to the Zetech University Research and Innovation Week admin panel</p>
            </div>
            <div class="flex flex-wrap mt-4 lg:mt-0 gap-2">
                <button id="refreshDashboard" class="bg-white p-2 rounded-lg shadow hover:shadow-md border border-gray-300 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-500 mr-1"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38"/></svg>
                    <span>Refresh</span>
                </button>
                <button id="toggleFilters" class="bg-white p-2 rounded-lg shadow hover:shadow-md border border-gray-300 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-500 mr-1"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                    <span>Filters</span>
                </button>
                <a href="{{ route('admin.password.change') }}" class="bg-white p-2 rounded-lg shadow hover:shadow-md border border-gray-300 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-500 mr-1"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    <span>Security</span>
                </a>
            </div>
        </div>
        
        <!-- Filter Panel (hidden by default) -->
        <div id="filterPanel" class="bg-white p-4 rounded-lg shadow mb-6 hidden">
            <h3 class="text-lg font-semibold mb-4">Dashboard Filters</h3>
            <form id="dashboardFilterForm" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="form-group">
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                    <input type="date" id="start_date" name="start_date" class="w-full p-2 border border-gray-300 rounded-md">
                </div>
                <div class="form-group">
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                    <input type="date" id="end_date" name="end_date" class="w-full p-2 border border-gray-300 rounded-md">
                </div>
                <div class="form-group">
                    <label for="usher_id" class="block text-sm font-medium text-gray-700 mb-1">Filter by Usher</label>
                    <select id="usher_id" name="usher_id" class="w-full p-2 border border-gray-300 rounded-md">
                        <option value="">All Ushers</option>
                        <!-- Populated via JavaScript -->
                    </select>
                </div>
                <div class="form-group">
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Filter by Role</label>
                    <select id="role" name="role" class="w-full p-2 border border-gray-300 rounded-md">
                        <option value="">All Roles</option>
                        <!-- Populated via JavaScript -->
                    </select>
                </div>
                <div class="col-span-1 md:col-span-4 flex justify-end gap-2">
                    <button type="button" id="resetFilters" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-200">Reset</button>
                    <button type="submit" class="bg-[#041E42] text-white px-4 py-2 rounded-md hover:bg-[#0A2E5C]">Apply Filters</button>
                </div>
            </form>
        </div>
        
        <!-- Live Stats Section -->
        <div class="flex items-center mb-4">
            <h2 class="text-xl font-semibold text-[#041E42]">Live Statistics</h2>
            <span class="ml-2 bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full border border-green-200 flex items-center">
                <span class="w-2 h-2 bg-green-500 rounded-full mr-1 animate-pulse"></span> Live
            </span>
        </div>

        <!-- Primary Stats Section -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <!-- Today's Registrations -->
            <div class="bg-white p-4 rounded-lg shadow hover:shadow-lg transition duration-300">
                <div class="flex items-center mb-3">
                    <div class="rounded-full bg-blue-100 p-3 mr-3 border border-blue-200">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-600"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Today's Registrations</p>
                        <h3 class="text-2xl font-bold text-[#041E42]">{{ $todayRegistrations }}</h3>
                    </div>
                </div>
                <div class="flex justify-between items-center text-xs">
                    <span class="text-blue-600 bg-blue-100 px-2 py-0.5 rounded-full font-medium">
                        All ushers
                    </span>
                    <a href="{{ route('admin.participants') }}" class="text-blue-500 hover:underline">View all</a>
                </div>
            </div>

            <!-- Today's Check-ins -->
            <div class="bg-white p-4 rounded-lg shadow hover:shadow-lg transition duration-300">
                <div class="flex items-center mb-3">
                    <div class="rounded-full bg-green-100 p-3 mr-3 border border-green-200">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-green-600"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Today's Check-ins</p>
                        <h3 class="text-2xl font-bold text-[#041E42]">{{ $todayCheckIns }}</h3>
                    </div>
                </div>
                <div class="flex justify-between items-center text-xs">
                    <span class="text-green-600 bg-green-100 px-2 py-0.5 rounded-full font-medium">
                        {{ $totalCheckIns }} total
                    </span>
                    <a href="#" id="view-check-ins" class="text-blue-500 hover:underline">View all</a>
                </div>
            </div>

            <!-- Total Participants -->
            <div class="bg-white p-4 rounded-lg shadow hover:shadow-lg transition duration-300">
                <div class="flex items-center mb-3">
                    <div class="rounded-full bg-purple-100 p-3 mr-3 border border-purple-200">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-purple-600"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Total Participants</p>
                        <h3 class="text-2xl font-bold text-[#041E42]">{{ $totalParticipants }}</h3>
                    </div>
                </div>
                <div class="flex justify-between items-center text-xs">
                    <span class="text-purple-600 bg-purple-100 px-2 py-0.5 rounded-full font-medium">
                        {{ $pendingCheckIns }} pending check-in
                    </span>
                    <a href="{{ route('admin.participants') }}" class="text-blue-500 hover:underline">View details</a>
                </div>
            </div>

            <!-- Payment Statistics -->
            <div class="bg-white p-4 rounded-lg shadow hover:shadow-lg transition duration-300">
                <div class="flex items-center mb-3">
                    <div class="rounded-full bg-amber-100 p-3 mr-3 border border-amber-200">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-amber-600"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Payment Rate</p>
                        <h3 class="text-2xl font-bold text-[#041E42]">{{ $paymentRate }}%</h3>
                    </div>
                </div>
                <div class="flex justify-between items-center text-xs">
                    <span class="text-amber-600 bg-amber-100 px-2 py-0.5 rounded-full font-medium">
                        {{ $paidParticipants }} paid participants
                    </span>
                    <a href="#" class="text-blue-500 hover:underline">View payments</a>
                </div>
            </div>
        </div>

        <!-- Secondary Stats Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-8">
            <!-- Usher Count -->
            <div class="bg-white p-4 rounded-lg shadow hover:shadow-lg transition duration-300 flex items-center">
                <div class="rounded-full bg-blue-100 p-3 mr-4 border border-blue-200">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-600"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">System Users</p>
                    <h3 class="text-xl font-bold text-[#041E42]">{{ $usherCount }} Ushers & {{ $adminCount }} Admins</h3>
                </div>
            </div>

            <!-- Conference Status -->
            <div class="bg-white p-4 rounded-lg shadow hover:shadow-lg transition duration-300 flex items-center">
                <div class="rounded-full bg-green-100 p-3 mr-4 border border-green-200">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-green-600"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Conference Status</p>
                    <h3 class="text-xl font-bold text-[#041E42]">
                        @if($today)
                            {{ $today->name ?? \Carbon\Carbon::parse($today->date)->format('F j, Y') }}
                        @else
                            No conference today
                        @endif
                    </h3>
                </div>
            </div>

            <!-- Current Time -->
            <div class="bg-white p-4 rounded-lg shadow hover:shadow-lg transition duration-300 flex items-center">
                <div class="rounded-full bg-indigo-100 p-3 mr-4 border border-indigo-200">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-indigo-600"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Current Time</p>
                    <h3 class="text-xl font-bold text-[#041E42]" id="currentDateTime">{{ date('l, F j, Y g:i A') }}</h3>
                </div>
            </div>
        </div>
        
        <!-- Analytics Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Participant Role Distribution -->
            <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
                <h3 class="font-semibold text-lg mb-4 text-[#041E42]">Participant Role Distribution</h3>
                <div class="h-64">
                    <canvas id="roleDistributionChart"></canvas>
                </div>
            </div>
            
            <!-- Registration & Check-in Trends -->
            <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
                <h3 class="font-semibold text-lg mb-4 text-[#041E42]">7-Day Activity Trend</h3>
                <div class="h-64">
                    <canvas id="activityTrendChart"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Admin & Usher Features -->
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-[#041E42]">Admin & Usher Features</h2>
            <span class="text-sm text-blue-600 bg-blue-50 px-2 py-1 rounded-md border border-blue-100">Unified Access</span>
        </div>
        
        <!-- Quick Actions Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <!-- Admin Features -->
            <a href="{{ route('admin.users') }}" class="bg-white p-5 rounded-lg shadow hover:shadow-lg transition duration-300 group border-l-4 border-[#041E42]">
                <div class="flex items-center mb-3">
                    <div class="rounded-full bg-blue-100 p-2 mr-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-[#041E42]"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    </div>
                    <h3 class="font-semibold text-[#041E42]">User Management</h3>
                </div>
                <p class="text-gray-500 text-xs pl-10">Add, edit, or remove system users</p>
            </a>
            
            <a href="{{ route('admin.conference-days.index') }}" class="bg-white p-5 rounded-lg shadow hover:shadow-lg transition duration-300 group border-l-4 border-[#041E42]">
                <div class="flex items-center mb-3">
                    <div class="rounded-full bg-blue-100 p-2 mr-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-[#041E42]"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                    </div>
                    <h3 class="font-semibold text-[#041E42]">Conference Days</h3>
                </div>
                <p class="text-gray-500 text-xs pl-10">Set attendance days for the event</p>
            </a>
            
            <a href="#" class="bg-white p-5 rounded-lg shadow hover:shadow-lg transition duration-300 group border-l-4 border-[#041E42]">
                <div class="flex items-center mb-3">
                    <div class="rounded-full bg-blue-100 p-2 mr-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-[#041E42]"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                    </div>
                    <h3 class="font-semibold text-[#041E42]">Export Data</h3>
                </div>
                <p class="text-gray-500 text-xs pl-10">Generate and download reports</p>
            </a>
            
            <a href="#" class="bg-white p-5 rounded-lg shadow hover:shadow-lg transition duration-300 group border-l-4 border-[#041E42]">
                <div class="flex items-center mb-3">
                    <div class="rounded-full bg-blue-100 p-2 mr-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-[#041E42]"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                    </div>
                    <h3 class="font-semibold text-[#041E42]">System Settings</h3>
                </div>
                <p class="text-gray-500 text-xs pl-10">Configure system parameters</p>
            </a>
            
            <!-- Usher Features -->
            <a href="{{ route('admin.check-in') }}" class="bg-white p-5 rounded-lg shadow hover:shadow-lg transition duration-300 group border-l-4 border-green-500">
                <div class="flex items-center mb-3">
                    <div class="rounded-full bg-green-100 p-2 mr-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-green-600"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    </div>
                    <h3 class="font-semibold text-[#041E42]">Check-in Console</h3>
                </div>
                <p class="text-gray-500 text-xs pl-10">Process participant check-ins</p>
            </a>
            
            <a href="{{ route('admin.registration') }}" class="bg-white p-5 rounded-lg shadow hover:shadow-lg transition duration-300 group border-l-4 border-green-500">
                <div class="flex items-center mb-3">
                    <div class="rounded-full bg-green-100 p-2 mr-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-green-600"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    </div>
                    <h3 class="font-semibold text-[#041E42]">Register Participant</h3>
                </div>
                <p class="text-gray-500 text-xs pl-10">Create new participant registrations</p>
            </a>
            
            <a href="{{ route('admin.meals') }}" class="bg-white p-5 rounded-lg shadow hover:shadow-lg transition duration-300 group border-l-4 border-green-500">
                <div class="flex items-center mb-3">
                    <div class="rounded-full bg-green-100 p-2 mr-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-green-600"><path d="M18 8h1a4 4 0 0 1 0 8h-1"></path><path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"></path><line x1="6" y1="1" x2="6" y2="4"></line><line x1="10" y1="1" x2="10" y2="4"></line><line x1="14" y1="1" x2="14" y2="4"></line></svg>
                    </div>
                    <h3 class="font-semibold text-[#041E42]">Meal Management</h3>
                </div>
                <p class="text-gray-500 text-xs pl-10">Track meal service for participants</p>
            </a>
            
            <a href="{{ route('admin.tickets') }}" class="bg-white p-5 rounded-lg shadow hover:shadow-lg transition duration-300 group border-l-4 border-green-500">
                <div class="flex items-center mb-3">
                    <div class="rounded-full bg-green-100 p-2 mr-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-green-600"><polyline points="4 7 4 4 20 4 20 7"></polyline><line x1="9" y1="20" x2="15" y2="20"></line><line x1="12" y1="4" x2="12" y2="20"></line><path d="M4.99 12.97A7.48 7.48 0 0 0 4 16.5c0 .88.33 4.5 4 4.5s4-3.62 4-4.5c0-1.37-.37-2.69-1.08-3.83"></path><path d="M19.01 12.97A7.48 7.48 0 0 1 20 16.5c0 .88-.33 4.5-4 4.5s-4-3.62-4-4.5c0-1.37.37-2.69 1.08-3.83"></path></svg>
                    </div>
                    <h3 class="font-semibold text-[#041E42]">Ticket Management</h3>
                </div>
                <p class="text-gray-500 text-xs pl-10">Search and resend participant tickets</p>
            </a>
        </div>
        
        <!-- Latest Check-ins Section -->
        <h2 class="text-xl font-semibold text-[#041E42] mb-4">Latest Check-ins</h2>
        <div class="bg-white rounded-lg shadow overflow-hidden mb-8 border border-gray-200">
            <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="font-medium text-[#041E42]">Most Recent Participant Check-ins</h3>
                <a href="#" id="view-all-check-ins" class="text-[#041E42] hover:underline text-sm flex items-center">
                    <span>View All</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-1"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </a>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="px-4 py-3 bg-gray-50 border-b border-gray-200">Participant</th>
                            <th class="px-4 py-3 bg-gray-50 border-b border-gray-200">Role</th>
                            <th class="px-4 py-3 bg-gray-50 border-b border-gray-200">Checked By</th>
                            <th class="px-4 py-3 bg-gray-50 border-b border-gray-200">Time</th>
                            <th class="px-4 py-3 bg-gray-50 border-b border-gray-200">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($latestCheckIns as $checkIn)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center border border-blue-200 text-blue-500 font-bold">
                                            {{ strtoupper(substr($checkIn->participant->name ?? 'U', 0, 1)) }}
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900">{{ $checkIn->participant->name ?? 'Unknown' }}</div>
                                            <div class="text-xs text-gray-500">{{ $checkIn->participant->email ?? 'No email' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs rounded-full
                                        @if($checkIn->participant->role == 'delegate') bg-blue-100 text-blue-800
                                        @elseif($checkIn->participant->role == 'exhibitor') bg-purple-100 text-purple-800
                                        @elseif($checkIn->participant->role == 'presenter') bg-amber-100 text-amber-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst($checkIn->participant->role ?? 'Unknown') }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $checkIn->checkedBy->name ?? 'System' }}</div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($checkIn->created_at)->diffForHumans() }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="#" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                                    <a href="#" class="text-green-600 hover:text-green-900">Send Ticket</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-3 text-center text-gray-500">
                                    No check-ins found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Usher Performance Section -->
        <h2 class="text-xl font-semibold text-[#041E42] mb-4">Usher Performance</h2>
        <div class="bg-white rounded-lg shadow overflow-hidden mb-8 border border-gray-200">
            <div class="p-4 border-b border-gray-200">
                <h3 class="font-medium text-[#041E42]">Check-ins by Usher</h3>
            </div>
            
            <div class="p-4">
                <div class="overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Usher
                                </th>
                                <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Check-ins
                                </th>
                                <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Progress
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($usherPerformance as $performance)
                                <tr>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-500 font-bold">
                                                {{ strtoupper(substr($performance->checkedBy->name ?? 'U', 0, 1)) }}
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900">{{ $performance->checkedBy->name ?? 'Unknown' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $performance->count }}</div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                                            <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ min(100, ($performance->count / max(1, $totalCheckIns) * 100)) }}%"></div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-3 text-center text-gray-500">
                                        No usher performance data available
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js Script -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle Filter Panel
            const toggleFilters = document.getElementById('toggleFilters');
            const filterPanel = document.getElementById('filterPanel');
            
            if (toggleFilters && filterPanel) {
                toggleFilters.addEventListener('click', function() {
                    filterPanel.classList.toggle('hidden');
                });
            }
            
            // Reset Filters
            const resetFilters = document.getElementById('resetFilters');
            const filterForm = document.getElementById('dashboardFilterForm');
            
            if (resetFilters && filterForm) {
                resetFilters.addEventListener('click', function() {
                    filterForm.reset();
                });
            }
            
            // Refresh Dashboard
            const refreshDashboard = document.getElementById('refreshDashboard');
            
            if (refreshDashboard) {
                refreshDashboard.addEventListener('click', function() {
                    location.reload();
                });
            }
            
            // Real-time date and time update
            function updateDateTime() {
                const now = new Date();
                const options = { weekday: 'long', year: 'numeric', month: 'short', day: 'numeric', hour: 'numeric', minute: '2-digit', second: '2-digit', hour12: true };
                const dateTimeElement = document.getElementById('currentDateTime');
                
                if (dateTimeElement) {
                    dateTimeElement.textContent = now.toLocaleString('en-US', options);
                }
            }
            
            updateDateTime();
            setInterval(updateDateTime, 1000);
            
            // Role Distribution Chart
            const roleDistributionCtx = document.getElementById('roleDistributionChart');
            
            if (roleDistributionCtx) {
                const roleData = @json($roleDistribution);
                
                const roles = roleData.map(item => item.role);
                const counts = roleData.map(item => item.count);
                
                // Generate a vibrant color palette with enough distinct colors
                const colorPalette = [
                    '#3b82f6', // blue
                    '#8b5cf6', // purple
                    '#f59e0b', // amber
                    '#10b981', // emerald
                    '#ef4444', // red
                    '#6366f1', // indigo
                    '#ec4899', // pink
                    '#14b8a6', // teal
                    '#f97316', // orange
                    '#84cc16', // lime
                    '#06b6d4', // cyan
                    '#a855f7', // violet
                    '#f43f5e', // rose
                    '#22c55e', // green
                    '#eab308', // yellow
                    '#6b21a8', // purple-800
                    '#0891b2', // cyan-600
                    '#be123c', // rose-700
                    '#4338ca', // indigo-700
                    '#0d9488'  // teal-600
                ];
                
                // Fixed color map for common roles
                const commonRoleColors = {
                    'delegate': '#3b82f6',   // blue
                    'exhibitor': '#8b5cf6', // purple
                    'presenter': '#f59e0b', // amber
                    'guest': '#10b981',     // emerald
                    'organizer': '#ef4444', // red
                    'staff': '#6366f1',     // indigo
                    'vip': '#ec4899',       // pink
                    'student': '#14b8a6',    // teal
                    'faculty': '#f97316',    // orange
                    'panelist': '#84cc16',   // lime
                    'panelsit': '#06b6d4',   // cyan (in case of typo)
                };
                
                // Generate colors for each role, ensuring no gray colors
                const getColorForRole = (role, index) => {
                    // First check if it's a common role with a predefined color
                    if (commonRoleColors[role.toLowerCase()]) {
                        return commonRoleColors[role.toLowerCase()];
                    }
                    
                    // Otherwise use the color palette and rotate through it
                    return colorPalette[index % colorPalette.length];
                };
                
                const colors = roles.map((role, index) => getColorForRole(role, index));
                
                window.roleDistributionChart = new Chart(roleDistributionCtx, {
                    type: 'doughnut',
                    data: {
                        labels: roles.map(role => role.charAt(0).toUpperCase() + role.slice(1)),
                        datasets: [{
                            data: counts,
                            backgroundColor: colors,
                            borderColor: 'white',
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'right',
                                labels: {
                                    font: {
                                        size: 12
                                    }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = context.raw || 0;
                                        const total = context.dataset.data.reduce((acc, data) => acc + data, 0);
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
            
            // Activity Trend Chart
            const activityTrendCtx = document.getElementById('activityTrendChart');
            
            if (activityTrendCtx) {
                const registrationTrend = @json($registrationTrend);
                const checkInTrend = @json($checkInTrend);
                
                // Generate the last 7 days for X-axis
                const last7Days = [];
                for (let i = 6; i >= 0; i--) {
                    const date = new Date();
                    date.setDate(date.getDate() - i);
                    last7Days.push(date.toISOString().split('T')[0]); // Format: YYYY-MM-DD
                }
                
                // Map the data to the days
                const registrationData = last7Days.map(day => registrationTrend[day] || 0);
                const checkInData = last7Days.map(day => checkInTrend[day] || 0);
                
                // Format dates for display
                const formattedDates = last7Days.map(day => {
                    const date = new Date(day);
                    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                });
                
                window.activityTrendChart = new Chart(activityTrendCtx, {
                    type: 'line',
                    data: {
                        labels: formattedDates,
                        datasets: [
                            {
                                label: 'Registrations',
                                data: registrationData,
                                borderColor: '#3b82f6',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                borderWidth: 2,
                                tension: 0.3,
                                fill: true
                            },
                            {
                                label: 'Check-ins',
                                data: checkInData,
                                borderColor: '#10b981',
                                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                borderWidth: 2,
                                tension: 0.3,
                                fill: true
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    drawBorder: false
                                },
                                ticks: {
                                    precision: 0
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        },
                        interaction: {
                            mode: 'nearest',
                            axis: 'x',
                            intersect: false
                        }
                    }
                });
            }
            
            // Populate role select options
            const roleSelect = document.getElementById('role');
            if (roleSelect) {
                const roleData = @json($roleDistribution);
                roleData.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.role;
                    option.textContent = item.role.charAt(0).toUpperCase() + item.role.slice(1);
                    roleSelect.appendChild(option);
                });
            }
            
            // Dashboard filter form submission
            const dashboardFilterForm = document.getElementById('dashboardFilterForm');
            if (dashboardFilterForm) {
                dashboardFilterForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    // Show loading state
                    const submitBtn = this.querySelector('button[type="submit"]');
                    const originalBtnText = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Filtering...';
                    submitBtn.disabled = true;
                    
                    const formData = new FormData(dashboardFilterForm);
                    const params = new URLSearchParams(formData);
                    
                    fetch(`{{ route('admin.dashboard.filter') }}?${params.toString()}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        // Update dashboard stats with filtered data
                        console.log('Filtered data:', data);
                        
                        // Update participant count
                        const totalParticipantsElement = document.querySelector('.text-2xl.font-bold:contains("Total Participants")');
                        if (totalParticipantsElement) {
                            totalParticipantsElement.textContent = data.participantCount;
                        }
                        
                        // Update check-in count
                        const checkInCountElement = document.querySelector('.text-green-600.bg-green-100 span');
                        if (checkInCountElement) {
                            checkInCountElement.textContent = `${data.checkInCount} total`;
                        }
                        
                        // Update role distribution chart if it exists
                        if (window.roleDistributionChart && data.roleDistribution) {
                            const roles = data.roleDistribution.map(item => item.role);
                            const counts = data.roleDistribution.map(item => item.count);
                            
                            window.roleDistributionChart.data.labels = roles.map(role => role.charAt(0).toUpperCase() + role.slice(1));
                            window.roleDistributionChart.data.datasets[0].data = counts;
                            window.roleDistributionChart.update();
                        }
                        
                        // Show filter notification
                        const filterPanel = document.getElementById('filterPanel');
                        const filterNotice = document.createElement('div');
                        filterNotice.className = 'mt-2 p-2 bg-blue-50 text-blue-700 rounded-md text-sm';
                        filterNotice.innerHTML = `<div class="flex items-center"><svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" /></svg> Showing filtered data from ${data.dateRange.start} to ${data.dateRange.end}</div>`;
                        
                        // Remove any existing filter notice
                        const existingNotice = filterPanel.querySelector('.bg-blue-50');
                        if (existingNotice) {
                            existingNotice.remove();
                        }
                        
                        filterPanel.appendChild(filterNotice);
                    })
                    .catch(error => {
                        console.error('Error fetching filtered data:', error);
                        
                        // Show error notification
                        const filterPanel = document.getElementById('filterPanel');
                        const errorNotice = document.createElement('div');
                        errorNotice.className = 'mt-2 p-2 bg-red-50 text-red-700 rounded-md text-sm';
                        errorNotice.innerHTML = `<div class="flex items-center"><svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg> Error loading filtered data. Please try again.</div>`;
                        
                        // Remove any existing error notice
                        const existingNotice = filterPanel.querySelector('.bg-red-50');
                        if (existingNotice) {
                            existingNotice.remove();
                        }
                        
                        filterPanel.appendChild(errorNotice);
                    })
                    .finally(() => {
                        // Restore button state
                        submitBtn.innerHTML = originalBtnText;
                        submitBtn.disabled = false;
                    });
                });
            }
        });
    </script>
@endsection 