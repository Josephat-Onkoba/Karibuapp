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
                    <a href="#" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-[#041E42] hover:bg-[#0A2E5C] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#041E42]">
                        <i data-lucide="printer" class="h-4 w-4 mr-2"></i>
                        Export Report
                    </a>
                </span>
            </div>
        </div>
        
        <!-- Conference Statistics -->
        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500 flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                    <i data-lucide="users" class="h-6 w-6"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Participants</p>
                    <p class="text-xl font-bold">0</p>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500 flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                    <i data-lucide="check-circle" class="h-6 w-6"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Checked In Today</p>
                    <p class="text-xl font-bold">0</p>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-yellow-500 flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                    <i data-lucide="credit-card" class="h-6 w-6"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Pending Payments</p>
                    <p class="text-xl font-bold">0</p>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-purple-500 flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                    <i data-lucide="calendar" class="h-6 w-6"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Days Remaining</p>
                    <p class="text-xl font-bold">3</p>
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
                                <span class="text-2xl font-bold text-[#041E42]">0</span>
                                <span class="text-sm text-gray-500 ml-1">participants</span>
                            </div>
                            <div class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                                0% of total
                            </div>
                        </div>
                    </div>
                    <div class="mt-auto">
                        <a href="{{ route('admin.participants.category', 'general') }}" class="block w-full text-center bg-white border border-[#041E42] text-[#041E42] hover:bg-gray-50 rounded-md px-4 py-2 transition-colors">
                            <div class="flex justify-center items-center">
                                <span>View Participants</span>
                                <i data-lucide="chevron-right" class="h-4 w-4 ml-2"></i>
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
                                <span class="text-2xl font-bold text-[#041E42]">0</span>
                                <span class="text-sm text-gray-500 ml-1">participants</span>
                            </div>
                            <div class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                                0% of total
                            </div>
                        </div>
                    </div>
                    <div class="mt-auto">
                        <a href="{{ route('admin.participants.category', 'invited') }}" class="block w-full text-center bg-white border border-[#041E42] text-[#041E42] hover:bg-gray-50 rounded-md px-4 py-2 transition-colors">
                            <div class="flex justify-center items-center">
                                <span>View Participants</span>
                                <i data-lucide="chevron-right" class="h-4 w-4 ml-2"></i>
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
                                <span class="text-2xl font-bold text-[#041E42]">0</span>
                                <span class="text-sm text-gray-500 ml-1">participants</span>
                            </div>
                            <div class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                                0% of total
                            </div>
                        </div>
                    </div>
                    <div class="mt-auto">
                        <a href="{{ route('admin.participants.category', 'internal') }}" class="block w-full text-center bg-white border border-[#041E42] text-[#041E42] hover:bg-gray-50 rounded-md px-4 py-2 transition-colors">
                            <div class="flex justify-center items-center">
                                <span>View Participants</span>
                                <i data-lucide="chevron-right" class="h-4 w-4 ml-2"></i>
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
                                <span class="text-2xl font-bold text-[#041E42]">0</span>
                                <span class="text-sm text-gray-500 ml-1">participants</span>
                            </div>
                            <div class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                                0% of total
                            </div>
                        </div>
                    </div>
                    <div class="mt-auto">
                        <a href="{{ route('admin.participants.category', 'coordinators') }}" class="block w-full text-center bg-white border border-[#041E42] text-[#041E42] hover:bg-gray-50 rounded-md px-4 py-2 transition-colors">
                            <div class="flex justify-center items-center">
                                <span>View Participants</span>
                                <i data-lucide="chevron-right" class="h-4 w-4 ml-2"></i>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions Section -->
    <div class="mt-8">
        <h2 class="text-xl font-bold text-[#041E42] mb-4">Quick Actions</h2>
        <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="#" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                        <i data-lucide="file-plus" class="h-5 w-5"></i>
                    </div>
                    <div>
                        <p class="font-medium">Import Participants</p>
                        <p class="text-sm text-gray-500">Bulk upload via Excel</p>
                    </div>
                </a>
                <a href="#" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                        <i data-lucide="mail" class="h-5 w-5"></i>
                    </div>
                    <div>
                        <p class="font-medium">Send Invitations</p>
                        <p class="text-sm text-gray-500">Email all participants</p>
                    </div>
                </a>
                <a href="#" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                        <i data-lucide="badge" class="h-5 w-5"></i>
                    </div>
                    <div>
                        <p class="font-medium">Generate Badges</p>
                        <p class="text-sm text-gray-500">Print name badges</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection 