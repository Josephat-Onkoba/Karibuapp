@extends('layouts.app')

@section('title', 'View Check-Ins')

@section('content')
<div class="container mx-auto px-4">
    <div class="mb-4 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-primary">Check-In History</h1>
        <a href="{{ route('usher.check-in') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded inline-flex items-center">
            <i data-lucide="user-plus" class="h-4 w-4 mr-1"></i>
            Check-In Participant
        </a>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="mb-6">
            <h3 class="font-medium text-gray-700 mb-2">Filter by Conference Day</h3>
            <form method="GET" action="{{ route('usher.check-ins') }}" class="flex space-x-2">
                <select name="day_id" class="flex-grow px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Days</option>
                    @foreach($days as $dayOption)
                        <option value="{{ $dayOption->id }}" {{ request('day_id') == $dayOption->id || ($day && $day->id == $dayOption->id) ? 'selected' : '' }}>
                            {{ $dayOption->name }} ({{ $dayOption->date->format('M d, Y') }})
                            @if($dayOption->isToday()) - TODAY @endif
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="bg-primary hover:bg-primary-dark text-white font-bold py-2 px-6 rounded-lg">
                    Filter
                </button>
            </form>
        </div>

        @if (!$day)
            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4" role="alert">
                <p>Please select a conference day from the dropdown above.</p>
            </div>
        @elseif ($checkIns && $checkIns->count() > 0)
            <div class="mt-4">
                <h2 class="text-lg font-semibold mb-4">Check-Ins for {{ $day->name }} ({{ $day->date->format('M d, Y') }})</h2>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-200">
                        <thead>
                            <tr>
                                <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Participant</th>
                                <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Email</th>
                                <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Organization</th>
                                <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Check-In Time</th>
                                <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($checkIns as $checkIn)
                                <tr>
                                    <td class="py-3 px-4 border-b border-gray-200">{{ $checkIn->participant->full_name }}</td>
                                    <td class="py-3 px-4 border-b border-gray-200">{{ $checkIn->participant->email }}</td>
                                    <td class="py-3 px-4 border-b border-gray-200">{{ $checkIn->participant->organization }}</td>
                                    <td class="py-3 px-4 border-b border-gray-200">{{ $checkIn->checked_in_at->format('M d, Y - h:i A') }}</td>
                                    <td class="py-3 px-4 border-b border-gray-200">
                                        <a href="{{ route('usher.participant.view', $checkIn->participant_id) }}" class="text-blue-600 hover:text-blue-900">
                                            View Participant
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-4">
                    {{ $checkIns->links() }}
                </div>
            </div>
        @else
            <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4" role="alert">
                <p>No check-ins found for {{ $day->name }} ({{ $day->date->format('M d, Y') }}).</p>
            </div>
        @endif
    </div>
</div>
@endsection 