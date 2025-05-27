@extends('layouts.app')

@section('title', 'Participant Details')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex items-center mb-2">
            <a href="{{ route('admin.participants.category', $category) }}" class="text-[#041E42] hover:text-[#0A2E5C] mr-3 flex items-center">
                <i data-lucide="arrow-left" class="h-5 w-5 mr-1"></i>
                <span>Back</span>
            </a>
        </div>
        <div class="flex flex-wrap items-center justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-[#041E42]">Participant Details</h1>
                <p class="text-gray-600 mt-1">View information for {{ $participant->full_name }}</p>
            </div>
            <div class="mt-4 md:mt-0 flex space-x-3">
                <a href="{{ route('admin.participants.edit', ['category' => $category, 'id' => $participant->id]) }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 flex items-center">
                    <i data-lucide="edit" class="h-4 w-4 mr-2"></i>
                    Edit
                </a>
                <form action="{{ route('admin.participants.destroy', ['category' => $category, 'id' => $participant->id]) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this participant?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 flex items-center">
                        <i data-lucide="trash-2" class="h-4 w-4 mr-2"></i>
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Participant Information Card -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200 mb-6">
        <div class="px-6 py-4 bg-[#041E42] text-white flex items-center justify-between">
            <h2 class="font-bold text-xl">Personal Information</h2>
            <div class="flex items-center">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $participant->payment_status == 'paid' ? 'bg-green-100 text-green-800' : ($participant->payment_status == 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                    {{ ucfirst($participant->payment_status) }}
                </span>
                @if($participant->checked_in)
                    <span class="ml-2 inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        Checked In
                    </span>
                @endif
            </div>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Full Name</h3>
                    <p class="mt-1 text-lg font-medium text-gray-900">{{ $participant->full_name }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Email Address</h3>
                    <p class="mt-1 text-lg font-medium text-gray-900">{{ $participant->email }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Phone Number</h3>
                    <p class="mt-1 text-lg font-medium text-gray-900">{{ $participant->phone_number }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Organization</h3>
                    <p class="mt-1 text-lg font-medium text-gray-900">{{ $participant->organization ?: 'Not specified' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Role Information Card -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200 mb-6">
        <div class="px-6 py-4 bg-[#041E42] text-white">
            <h2 class="font-bold text-xl">Role Information</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Role</h3>
                    <p class="mt-1 text-lg font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $participant->role)) }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Category</h3>
                    <p class="mt-1 text-lg font-medium text-gray-900">{{ ucfirst($participant->category) }}</p>
                </div>

                <!-- Conditional fields based on role -->
                @if($participant->role == 'student')
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Student Admission Number</h3>
                    <p class="mt-1 text-lg font-medium text-gray-900">{{ $participant->student_admission_number }}</p>
                </div>
                @endif

                @if($participant->role == 'staff')
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Staff Number</h3>
                    <p class="mt-1 text-lg font-medium text-gray-900">{{ $participant->staff_number }}</p>
                </div>
                @endif

                @if($participant->role == 'presenter')
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Presenter Type</h3>
                    <p class="mt-1 text-lg font-medium text-gray-900">{{ ucfirst($participant->presenter_type) }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Payment Information Card -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200 mb-6">
        <div class="px-6 py-4 bg-[#041E42] text-white">
            <h2 class="font-bold text-xl">Payment Information</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Payment Status</h3>
                    <p class="mt-1 text-lg font-medium {{ $participant->payment_status == 'paid' ? 'text-green-600' : ($participant->payment_status == 'pending' ? 'text-yellow-600' : 'text-gray-600') }}">
                        {{ ucfirst($participant->payment_status) }}
                    </p>
                </div>
                @if($participant->payment_status != 'exempted')
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Payment Amount</h3>
                    <p class="mt-1 text-lg font-medium text-gray-900">KES {{ number_format($participant->payment_amount, 2) }}</p>
                </div>
                @endif
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Payment Confirmation</h3>
                    <p class="mt-1 text-lg font-medium {{ $participant->payment_confirmed ? 'text-green-600' : 'text-red-600' }}">
                        {{ $participant->payment_confirmed ? 'Confirmed' : 'Not Confirmed' }}
                    </p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Registration Date</h3>
                    <p class="mt-1 text-lg font-medium text-gray-900">{{ $participant->created_at->format('F j, Y, g:i a') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Eligibility Information Card -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
        <div class="px-6 py-4 bg-[#041E42] text-white">
            <h2 class="font-bold text-xl">Conference Eligibility</h2>
        </div>
        <div class="p-6">
            <h3 class="text-sm font-medium text-gray-500 mb-3">Eligible Days</h3>
            <div class="space-y-2">
                @php
                    $eligibleDays = explode(',', $participant->eligible_days);
                    $conferenceDays = DB::table('conference_days')->whereIn('id', $eligibleDays)->orderBy('date')->get();
                @endphp
                
                @if(count($conferenceDays) > 0)
                    @foreach($conferenceDays as $day)
                        <div class="flex items-center py-2 px-3 bg-gray-50 rounded-md border border-gray-200">
                            <i data-lucide="calendar" class="h-5 w-5 text-[#041E42] mr-2"></i>
                            <span class="text-gray-700">{{ \Carbon\Carbon::parse($day->date)->format('l, F j, Y') }}</span>
                        </div>
                    @endforeach
                @else
                    <p class="text-gray-500 italic">No eligible days specified</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
