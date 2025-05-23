@extends('layouts.app')

@section('title', 'Conference Days')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-[#041E42]">Conference Days</h1>
                <p class="text-gray-600 mt-1">Manage attendance days for the conference</p>
            </div>
            <div class="mt-4 md:mt-0">
                <a href="{{ route('admin.conference-days.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-[#041E42] hover:bg-[#0A2E5C] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#041E42]">
                    <i data-lucide="plus" class="h-4 w-4 mr-2"></i>
                    <span>Add Conference Day</span>
                </a>
            </div>
        </div>
    </div>
    
    <!-- Notification Message -->
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded shadow">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i data-lucide="check-circle" class="h-5 w-5 text-green-500"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700">
                        {{ session('success') }}
                    </p>
                </div>
            </div>
        </div>
    @endif
    
    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded shadow">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i data-lucide="alert-circle" class="h-5 w-5 text-red-500"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700">
                        {{ session('error') }}
                    </p>
                </div>
            </div>
        </div>
    @endif
    
    <!-- Conference Days Table -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden border border-gray-200">
        <div class="p-6 bg-white border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900 flex items-center">
                <i data-lucide="calendar" class="h-5 w-5 text-[#041E42] mr-2"></i>
                <span>Conference Attendance Days</span>
            </h2>
            <p class="mt-1 text-sm text-gray-500">These days will be shown to ushers during check-in</p>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Name
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Date
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Description
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Check-ins
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @if(count($days) > 0)
                        @foreach($days as $day)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-8 w-8 flex-shrink-0 rounded-full bg-[#041E42]/10 flex items-center justify-center">
                                        <i data-lucide="calendar-check" class="h-4 w-4 text-[#041E42]"></i>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $day->name }}</div>
                                        @if($day->date->isToday())
                                            <span class="px-2 py-0.5 text-xs bg-green-100 text-green-800 rounded-full">Today</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500">{{ $day->date->format('M d, Y') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-500 max-w-xs truncate">{{ $day->description }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium">{{ $day->check_ins_count ?? 0 }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2">
                                    <a href="{{ route('admin.conference-days.edit', $day->id) }}" class="text-indigo-600 hover:text-indigo-900 flex items-center">
                                        <i data-lucide="edit" class="h-4 w-4 mr-1"></i>
                                        <span>Edit</span>
                                    </a>
                                    <form action="{{ route('admin.conference-days.destroy', $day->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 flex items-center" onclick="return confirm('Are you sure you want to delete this conference day?')">
                                            <i data-lucide="trash-2" class="h-4 w-4 mr-1"></i>
                                            <span>Delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="rounded-full bg-gray-100 p-3 mb-4">
                                        <i data-lucide="calendar" class="h-8 w-8 text-gray-400"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-1">No conference days found</h3>
                                    <p class="text-gray-500 mb-4">Add days to allow ushers to record attendance</p>
                                    <div>
                                        <a href="{{ route('admin.conference-days.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-[#041E42] hover:bg-[#0A2E5C] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#041E42]">
                                            <i data-lucide="plus" class="h-4 w-4 mr-2"></i>
                                            Add Conference Day
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        
        <!-- Information about usage -->
        <div class="p-6 bg-gray-50 border-t border-gray-200">
            <div class="flex items-start">
                <div class="flex-shrink-0 pt-1">
                    <i data-lucide="info" class="h-5 w-5 text-blue-500"></i>
                </div>
                <div class="ml-3 text-sm text-gray-600">
                    <p>Conference days are used for attendance tracking. Ushers will see these days during the check-in process to mark participant attendance.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 