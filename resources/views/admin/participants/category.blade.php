@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex items-center mb-2">
            <a href="{{ route('admin.participants') }}" class="text-[#041E42] hover:text-[#0A2E5C] mr-3 flex items-center">
                <i data-lucide="arrow-left" class="h-5 w-5 mr-1"></i>
                <span>Back</span>
            </a>
        </div>
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-[#041E42]">{{ $title }}</h1>
                <p class="text-gray-600 mt-1">{{ $subtitle }}</p>
            </div>
            <div class="mt-4 md:mt-0 flex space-x-3">
                <button class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#041E42]">
                    <i data-lucide="filter" class="h-4 w-4 mr-2"></i>
                    Filter
                </button>
                
                <button class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#041E42]">
                    <i data-lucide="download" class="h-4 w-4 mr-2"></i>
                    Export
                </button>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
        <!-- Action Bar -->
        <div class="p-4 md:p-6 bg-white border-b border-gray-200">
            <div class="flex flex-col md:flex-row items-start md:items-center md:justify-between space-y-3 md:space-y-0">
                <div class="flex flex-col sm:flex-row gap-2">
                    <a href="{{ route('admin.participants.create', $category) }}" class="inline-flex items-center justify-center px-4 py-2 bg-[#041E42] hover:bg-[#0A2E5C] text-white rounded-md transition-colors">
                        <i data-lucide="plus" class="h-4 w-4 mr-2"></i>
                        <span>Add Participant</span>
                    </a>
                    
                    <a href="{{ route('admin.participants.import', $category) }}" class="inline-flex items-center justify-center px-4 py-2 border border-[#041E42] text-[#041E42] hover:bg-gray-50 rounded-md transition-colors">
                        <i data-lucide="upload" class="h-4 w-4 mr-2"></i>
                        <span>Import Excel</span>
                    </a>
                    
                    <div class="relative inline-block">
                        <button id="more-actions-btn" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-md transition-colors">
                            <i data-lucide="more-horizontal" class="h-4 w-4 mr-2"></i>
                            <span>More</span>
                        </button>
                    </div>
                </div>
                
                <div class="w-full md:w-64">
                    <div class="relative">
                        <input type="text" placeholder="Search participants..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#041E42]">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="search" class="h-4 w-4 text-gray-400"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Filters and Tags (can be expanded in the future) -->
            <div class="mt-4">
                <div class="flex items-center flex-wrap gap-2">
                    <span class="text-sm text-gray-500 mr-2">Active filters:</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        All participants
                        <button type="button" class="flex-shrink-0 ml-1 h-4 w-4 rounded-full inline-flex items-center justify-center text-blue-400 hover:bg-blue-200 hover:text-blue-500 focus:outline-none">
                            <i data-lucide="x" class="h-3 w-3"></i>
                        </button>
                    </span>
                </div>
            </div>
        </div>
        
        <!-- Table Section -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <div class="flex items-center">
                                <input type="checkbox" class="rounded border-gray-300 text-[#041E42] focus:ring-[#041E42]">
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Full Name</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Job Title</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Organization</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @if(count($participants) > 0)
                        @foreach($participants as $participant)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" class="rounded border-gray-300 text-[#041E42] focus:ring-[#041E42]">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-8 w-8 flex-shrink-0 rounded-full bg-[#041E42]/10 flex items-center justify-center">
                                        <span class="text-[#041E42] text-sm font-medium">{{ substr($participant->full_name ?? 'JD', 0, 1) }}</span>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $participant->full_name ?? 'John Doe' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500">{{ $participant->email ?? 'john.doe@example.com' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500">{{ $participant->phone_number ?? '+254 123 456789' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500">{{ $participant->job_title ?? 'Researcher' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500">{{ $participant->organization ?? 'Sample University' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                    {{ $participant->role ?? 'Delegate' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $participant->payment_status ?? 'Not Applicable' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if(isset($participant->payment_confirmed) && $participant->payment_confirmed)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Confirmed
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Pending
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                <div class="flex space-x-2">
                                    <button class="p-1 rounded-full hover:bg-blue-100 text-blue-600" title="View Details">
                                        <i data-lucide="eye" class="h-4 w-4"></i>
                                    </button>
                                    <button class="p-1 rounded-full hover:bg-green-100 text-green-600" title="Check In">
                                        <i data-lucide="check" class="h-4 w-4"></i>
                                    </button>
                                    <button class="p-1 rounded-full hover:bg-indigo-100 text-indigo-600" title="Edit">
                                        <i data-lucide="edit" class="h-4 w-4"></i>
                                    </button>
                                    <button class="p-1 rounded-full hover:bg-red-100 text-red-600" title="Delete">
                                        <i data-lucide="trash-2" class="h-4 w-4"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="10" class="px-6 py-10 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="rounded-full bg-gray-100 p-3 mb-4">
                                        <i data-lucide="users" class="h-8 w-8 text-gray-400"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-1">No participants found</h3>
                                    <p class="text-gray-500 mb-4">Get started by adding your first {{ strtolower($title) }}</p>
                                    <div>
                                        <a href="{{ route('admin.participants.create', $category) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-[#041E42] hover:bg-[#0A2E5C] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#041E42]">
                                            <i data-lucide="plus" class="h-4 w-4 mr-2"></i>
                                            Add Participant
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-6 py-4 bg-white border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Showing <span class="font-medium">1</span> to <span class="font-medium">10</span> of <span class="font-medium">0</span> results
                        </p>
                    </div>
                    <div>
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                            <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                <span class="sr-only">Previous</span>
                                <i data-lucide="chevron-left" class="h-5 w-5"></i>
                            </a>
                            <a href="#" aria-current="page" class="z-10 bg-[#041E42] border-[#041E42] text-white relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                1
                            </a>
                            <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                <span class="sr-only">Next</span>
                                <i data-lucide="chevron-right" class="h-5 w-5"></i>
                            </a>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 