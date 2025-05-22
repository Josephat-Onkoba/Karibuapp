@extends('layouts.app')

@section('title', 'Meal Statistics')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('usher.meals') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900">
            <i data-lucide="arrow-left" class="h-4 w-4 mr-1"></i>
            Back to Meal Service
        </a>
    </div>

    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-[#041E42]">Meal Statistics</h1>
                <p class="text-gray-600 mt-1">
                    @if($day)
                        Statistics for {{ $day->name }} ({{ $day->date->format('F j, Y') }})
                    @else
                        Select a conference day to view statistics
                    @endif
                </p>
            </div>
            
            <!-- Day Selection -->
            <div class="mt-4 md:mt-0">
                <form action="{{ route('usher.meals.stats') }}" method="GET" class="flex">
                    <div class="relative">
                        <select name="day_id" id="day_id" 
                            class="pl-3 pr-10 py-2 border border-gray-300 bg-white rounded-l-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm appearance-none"
                            onchange="this.form.submit()">
                            <option value="">Select a conference day...</option>
                            @foreach($days as $conferenceDay)
                                <option value="{{ $conferenceDay->id }}" {{ ($day && $day->id == $conferenceDay->id) ? 'selected' : '' }}>
                                    {{ $conferenceDay->name }} ({{ $conferenceDay->date->format('M j, Y') }})
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                            <i data-lucide="chevron-down" class="h-4 w-4 text-gray-400"></i>
                        </div>
                    </div>
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-r-md text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i data-lucide="filter" class="h-4 w-4 mr-1"></i>
                        Filter
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    @if(!$day)
        <div class="bg-yellow-50 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded-r-md shadow-sm" role="alert">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i data-lucide="alert-triangle" class="h-5 w-5 text-yellow-500"></i>
                </div>
                <div class="ml-3">
                    <p class="font-medium">No Conference Day Selected</p>
                    <p class="mt-1">Please select a conference day from the dropdown above to view meal statistics.</p>
                </div>
            </div>
        </div>
    @else
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            @foreach($stats as $mealId => $stat)
                <div class="bg-white rounded-lg border {{ $stat['is_current'] ? 'border-green-500' : 'border-gray-200' }} shadow-sm overflow-hidden">
                    <div class="px-6 py-5 flex items-center {{ $stat['is_current'] ? 'bg-green-50' : '' }}">
                        <div class="h-12 w-12 rounded-full bg-primary/10 text-primary flex items-center justify-center">
                            <i data-lucide="utensils" class="h-6 w-6"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="font-semibold text-gray-900">{{ $stat['name'] }}</h3>
                            @if($stat['is_current'])
                                <div class="mt-1 text-xs text-green-600 flex items-center">
                                    <i data-lucide="clock" class="h-3 w-3 mr-1"></i>
                                    <span>Currently being served</span>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="border-t border-gray-200 px-6 py-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Participants served:</span>
                            <span class="text-2xl font-bold text-primary">{{ $stat['count'] }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Recent Servings -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200 mb-6">
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900 flex items-center">
                    <i data-lucide="clock" class="h-5 w-5 mr-2 text-[#041E42]"></i>
                    Recent Meal Servings
                </h2>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Participant
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Meal Type
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ticket Number
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Served At
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Served By
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($recentServings ?? [] as $serving)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-9 w-9 rounded-full bg-[#041E42] text-white flex items-center justify-center text-sm font-medium">
                                            {{ strtoupper(substr($serving->participant->full_name, 0, 1)) }}{{ strtoupper(substr($serving->participant->full_name, strpos($serving->participant->full_name, ' ') + 1, 1)) }}
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900">{{ $serving->participant->full_name }}</div>
                                            <div class="text-xs text-gray-500">{{ $serving->participant->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary/10 text-primary">
                                        {{ $serving->mealType->name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <span class="font-mono">{{ $serving->ticket_number }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $serving->served_at->format('M j, Y g:i A') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $serving->servedBy->name }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                    No meal servings recorded for this day.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection 