@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="container mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-[#041E42]">Admin Dashboard</h1>
            <div class="flex space-x-2">
                <button class="bg-white p-2 rounded-lg shadow hover:shadow-md border border-gray-300">
                    <i data-lucide="bell" class="h-5 w-5 text-gray-500"></i>
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
                    <h3 class="text-2xl font-bold text-[#041E42]">{{ $todayVisitorsCount ?? 0 }}</h3>
                    <p class="text-xs text-green-600 flex items-center">
                        <i data-lucide="trending-up" class="h-3 w-3 mr-1"></i>
                        <span>12% increase</span>
                    </p>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow border border-gray-200 hover:shadow-lg transition duration-300 flex items-center">
                <div class="rounded-full bg-blue-100 p-3 mr-4 border border-gray-200">
                    <i data-lucide="user-check" class="h-6 w-6 text-[#041E42]"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Ushers</p>
                    <h3 class="text-2xl font-bold text-[#041E42]">{{ $usherCount ?? 0 }}</h3>
                    <p class="text-xs text-red-600 flex items-center">
                        <i data-lucide="trending-down" class="h-3 w-3 mr-1"></i>
                        <span>2% decrease</span>
                    </p>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow border border-gray-200 hover:shadow-lg transition duration-300 flex items-center">
                <div class="rounded-full bg-blue-100 p-3 mr-4 border border-gray-200">
                    <i data-lucide="bar-chart-2" class="h-6 w-6 text-[#041E42]"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Visitors</p>
                    <h3 class="text-2xl font-bold text-[#041E42]">{{ $totalVisitorsCount ?? 0 }}</h3>
                    <p class="text-xs text-green-600 flex items-center">
                        <i data-lucide="trending-up" class="h-3 w-3 mr-1"></i>
                        <span>8% increase</span>
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions Grid -->
        <h2 class="text-xl font-semibold text-[#041E42] mb-4">Quick Actions</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <a href="{{ route('admin.users') }}" class="bg-white p-6 rounded-lg shadow border border-gray-200 hover:shadow-lg transition duration-300 group">
                <div class="flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 mb-4 group-hover:bg-blue-200 border border-gray-200">
                    <i data-lucide="user-plus" class="h-6 w-6 text-[#041E42]"></i>
                </div>
                <h3 class="font-semibold text-lg mb-1 text-[#041E42]">Manage Users</h3>
                <p class="text-gray-500 text-sm">Add, edit, or remove system users</p>
            </a>
            
            <a href="{{ route('admin.conference-days.index') }}" class="bg-white p-6 rounded-lg shadow border border-gray-200 hover:shadow-lg transition duration-300 group">
                <div class="flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 mb-4 group-hover:bg-blue-200 border border-gray-200">
                    <i data-lucide="calendar" class="h-6 w-6 text-[#041E42]"></i>
                </div>
                <h3 class="font-semibold text-lg mb-1 text-[#041E42]">Conference Days</h3>
                <p class="text-gray-500 text-sm">Set attendance days for the event</p>
            </a>
            
            <a href="#" class="bg-white p-6 rounded-lg shadow border border-gray-200 hover:shadow-lg transition duration-300 group">
                <div class="flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 mb-4 group-hover:bg-blue-200 border border-gray-200">
                    <i data-lucide="file-text" class="h-6 w-6 text-[#041E42]"></i>
                </div>
                <h3 class="font-semibold text-lg mb-1 text-[#041E42]">Generate Report</h3>
                <p class="text-gray-500 text-sm">Create reports and export data</p>
            </a>
            
            <a href="#" class="bg-white p-6 rounded-lg shadow border border-gray-200 hover:shadow-lg transition duration-300 group">
                <div class="flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 mb-4 group-hover:bg-blue-200 border border-gray-200">
                    <i data-lucide="settings" class="h-6 w-6 text-[#041E42]"></i>
                </div>
                <h3 class="font-semibold text-lg mb-1 text-[#041E42]">System Setup</h3>
                <p class="text-gray-500 text-sm">Configure system parameters</p>
            </a>
        </div>
        
        <!-- Recent Activity -->
        <h2 class="text-xl font-semibold text-[#041E42] mb-4">Recent Activity</h2>
        <div class="bg-white rounded-lg shadow overflow-hidden mb-8 border border-gray-200">
            <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                <h3 class="font-medium text-[#041E42]">Latest System Events</h3>
                <button class="text-[#041E42] hover:underline text-sm flex items-center">
                    <span>View All</span>
                    <i data-lucide="chevron-right" class="h-4 w-4 ml-1"></i>
                </button>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-3 bg-gray-50 border-b border-gray-200">User</th>
                            <th class="px-6 py-3 bg-gray-50 border-b border-gray-200">Action</th>
                            <th class="px-6 py-3 bg-gray-50 border-b border-gray-200">Time</th>
                            <th class="px-6 py-3 bg-gray-50 border-b border-gray-200">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <!-- Placeholder row with loading state -->
                        <tr class="animate-pulse">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="h-8 w-8 rounded-full bg-gray-200"></div>
                                    <div class="ml-4 h-4 w-24 bg-gray-200 rounded"></div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="h-4 w-32 bg-gray-200 rounded"></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="h-4 w-24 bg-gray-200 rounded"></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="h-6 w-16 bg-gray-200 rounded-full"></div>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center border border-gray-200">
                                        <i data-lucide="user" class="h-5 w-5 text-gray-500"></i>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">Admin User</div>
                                        <div class="text-sm text-gray-500">admin@karibu.com</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">System Login</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500">1 minute ago</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 border border-green-200">
                                    Success
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection 