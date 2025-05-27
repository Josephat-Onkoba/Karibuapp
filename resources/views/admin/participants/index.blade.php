@extends('layouts.app')

@section('title', 'Participants Management')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-[#041E42]">Participants Management</h1>
                <p class="text-gray-600 mt-1">Manage attendees for the 3-day research conference</p>
            </div>
            <div class="mt-4 md:mt-0">
                <span class="inline-flex rounded-md shadow">
                    <a href="{{ route('admin.participants.export') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-[#041E42] hover:bg-[#0A2E5C] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#041E42]">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 mr-2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                        Export All Participants
                    </a>
                </span>
            </div>
        </div>
        
        <!-- Conference Statistics -->
        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500 flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Participants</p>
                    <p class="text-xl font-bold">{{ $totalParticipants }}</p>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500 flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Checked In Today</p>
                    <p class="text-xl font-bold">{{ $todayCheckIns }}</p>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-yellow-500 flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Pending Payments</p>
                    <p class="text-xl font-bold">{{ $pendingPayments }}</p>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-purple-500 flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Days Remaining</p>
                    <p class="text-xl font-bold">{{ $daysRemaining }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <h2 class="text-xl font-bold text-[#041E42] mb-4">Participant Categories</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
        <!-- General Participants Card -->
        <div class="h-full">
            <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow overflow-hidden border border-gray-200 h-full flex flex-col">
                <div class="px-6 py-4 bg-gradient-to-r from-[#041E42] to-[#0A2E5C] text-white">
                    <div class="flex items-center">
                        <div class="p-2 rounded-full bg-white/20 mr-3">
                            <i data-lucide="users" class="h-5 w-5"></i>
                        </div>
                        <h2 class="text-base font-bold">General Participants</h2>
                    </div>
                </div>
                <div class="p-6 flex flex-col flex-grow">
                    <div class="description mb-4 min-h-[3rem]">
                        <p class="text-gray-600">Delegates, Exhibitors, Conference Presenters</p>
                    </div>
                    <div class="stats mb-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-2xl font-bold text-[#041E42]">{{ $generalCount }}</span>
                                <span class="text-sm text-gray-500 ml-1">participants</span>
                            </div>
                            <div class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                                {{ $generalPercentage }}% of total
                            </div>
                        </div>
                    </div>
                    <div class="mt-auto flex flex-col space-y-2">
                        <a href="{{ route('admin.participants.category', 'general') }}" class="block w-full text-center bg-white border border-[#041E42] text-[#041E42] hover:bg-gray-50 rounded-md px-4 py-2 transition-colors">
                            <div class="flex justify-center items-center">
                                <span>View Participants</span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 ml-2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                            </div>
                        </a>
                        <a href="{{ route('admin.participants.category.export', 'general') }}" class="block w-full text-center bg-blue-50 border border-blue-200 text-blue-700 hover:bg-blue-100 rounded-md px-4 py-2 transition-colors">
                            <div class="flex justify-center items-center">
                                <span>Export</span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 ml-2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Invited Guests & Speakers Card -->
        <div class="h-full">
            <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow overflow-hidden border border-gray-200 h-full flex flex-col">
                <div class="px-6 py-4 bg-gradient-to-r from-[#041E42] to-[#0A2E5C] text-white">
                    <div class="flex items-center">
                        <div class="p-2 rounded-full bg-white/20 mr-3">
                            <i data-lucide="star" class="h-5 w-5"></i>
                        </div>
                        <h2 class="text-base font-bold">Invited Guests & Speakers</h2>
                    </div>
                </div>
                <div class="p-6 flex flex-col flex-grow">
                    <div class="description mb-4 min-h-[3rem]">
                        <p class="text-gray-600">Chief Guests, Guests, Keynote Speakers, Panelists</p>
                    </div>
                    <div class="stats mb-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-2xl font-bold text-[#041E42]">{{ $invitedCount }}</span>
                                <span class="text-sm text-gray-500 ml-1">participants</span>
                            </div>
                            <div class="px-2 py-1 bg-rose-100 text-rose-800 text-xs rounded-full">
                                {{ $invitedPercentage }}% of total
                            </div>
                        </div>
                    </div>
                    <div class="mt-auto flex flex-col space-y-2">
                        <a href="{{ route('admin.participants.category', 'invited') }}" class="block w-full text-center bg-white border border-[#041E42] text-[#041E42] hover:bg-gray-50 rounded-md px-4 py-2 transition-colors">
                            <div class="flex justify-center items-center">
                                <span>View Participants</span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 ml-2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                            </div>
                        </a>
                        <a href="{{ route('admin.participants.category.export', 'invited') }}" class="block w-full text-center bg-rose-50 border border-rose-200 text-rose-700 hover:bg-rose-100 rounded-md px-4 py-2 transition-colors">
                            <div class="flex justify-center items-center">
                                <span>Export</span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 ml-2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Internal Participants Card -->
        <div class="h-full">
            <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow overflow-hidden border border-gray-200 h-full flex flex-col">
                <div class="px-6 py-4 bg-gradient-to-r from-[#041E42] to-[#0A2E5C] text-white">
                    <div class="flex items-center">
                        <div class="p-2 rounded-full bg-white/20 mr-3">
                            <i data-lucide="building" class="h-5 w-5"></i>
                        </div>
                        <h2 class="text-base font-bold">Internal Participants</h2>
                    </div>
                </div>
                <div class="p-6 flex flex-col flex-grow">
                    <div class="description mb-4 min-h-[3rem]">
                        <p class="text-gray-600">Staff, Students</p>
                    </div>
                    <div class="stats mb-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-2xl font-bold text-[#041E42]">{{ $internalCount }}</span>
                                <span class="text-sm text-gray-500 ml-1">participants</span>
                            </div>
                            <div class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">
                                {{ $internalPercentage }}% of total
                            </div>
                        </div>
                    </div>
                    <div class="mt-auto flex flex-col space-y-2">
                        <a href="{{ route('admin.participants.category', 'internal') }}" class="block w-full text-center bg-white border border-[#041E42] text-[#041E42] hover:bg-gray-50 rounded-md px-4 py-2 transition-colors">
                            <div class="flex justify-center items-center">
                                <span>View Participants</span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 ml-2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                            </div>
                        </a>
                        <a href="{{ route('admin.participants.category.export', 'internal') }}" class="block w-full text-center bg-green-50 border border-green-200 text-green-700 hover:bg-green-100 rounded-md px-4 py-2 transition-colors">
                            <div class="flex justify-center items-center">
                                <span>Export</span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 ml-2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Session Coordinators Card -->
        <div class="h-full">
            <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow overflow-hidden border border-gray-200 h-full flex flex-col">
                <div class="px-6 py-4 bg-gradient-to-r from-[#041E42] to-[#0A2E5C] text-white">
                    <div class="flex items-center">
                        <div class="p-2 rounded-full bg-white/20 mr-3">
                            <i data-lucide="clipboard-list" class="h-5 w-5"></i>
                        </div>
                        <h2 class="text-base font-bold">Session Coordinators</h2>
                    </div>
                </div>
                <div class="p-6 flex flex-col flex-grow">
                    <div class="description mb-4 min-h-[3rem]">
                        <p class="text-gray-600">Secretariat, Moderators, Rapporteurs</p>
                    </div>
                    <div class="stats mb-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-2xl font-bold text-[#041E42]">{{ $coordinatorsCount }}</span>
                                <span class="text-sm text-gray-500 ml-1">participants</span>
                            </div>
                            <div class="px-2 py-1 bg-purple-100 text-purple-800 text-xs rounded-full">
                                {{ $coordinatorsPercentage }}% of total
                            </div>
                        </div>
                    </div>
                    <div class="mt-auto flex flex-col space-y-2">
                        <a href="{{ route('admin.participants.category', 'coordinators') }}" class="block w-full text-center bg-white border border-[#041E42] text-[#041E42] hover:bg-gray-50 rounded-md px-4 py-2 transition-colors">
                            <div class="flex justify-center items-center">
                                <span>View Participants</span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 ml-2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                            </div>
                        </a>
                        <a href="{{ route('admin.participants.category.export', 'coordinators') }}" class="block w-full text-center bg-purple-50 border border-purple-200 text-purple-700 hover:bg-purple-100 rounded-md px-4 py-2 transition-colors">
                            <div class="flex justify-center items-center">
                                <span>Export</span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 ml-2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 