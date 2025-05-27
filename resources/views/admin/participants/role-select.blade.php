@extends('layouts.app')

@section('title', 'Select Participant Role')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center mb-2">
            <a href="{{ route('admin.participants.category', $category) }}" class="text-[#041E42] hover:text-[#0A2E5C] mr-3 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                <span class="ml-1">Back</span>
            </a>
        </div>
        <h1 class="text-2xl md:text-3xl font-bold text-[#041E42]">Select Participant Role</h1>
        <p class="text-gray-600 mt-1">Choose the appropriate role for this participant</p>
    </div>

    <!-- Role Selection Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-6">
        @if($category === 'general')
            <!-- Delegate Card -->
            <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow overflow-hidden border border-gray-200">
                <div class="p-6 flex flex-col h-full">
                    <div class="flex items-center mb-4">
                        <div class="p-2 rounded-full bg-blue-100 text-blue-600 mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                        </div>
                        <h2 class="text-lg font-bold text-gray-800">Delegate</h2>
                    </div>
                    <p class="text-gray-600 mb-6 flex-grow">Regular conference participant who pays the standard fee and attends sessions.</p>
                    <a href="{{ route('admin.participants.create', ['category' => $category, 'role' => 'delegate']) }}" class="w-full text-center bg-[#041E42] hover:bg-[#0A2E5C] text-white py-2 px-4 rounded transition-colors">
                        Select
                    </a>
                </div>
            </div>

            <!-- Presenter Card -->
            <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow overflow-hidden border border-gray-200">
                <div class="p-6 flex flex-col h-full">
                    <div class="flex items-center mb-4">
                        <div class="p-2 rounded-full bg-green-100 text-green-600 mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"></path></svg>
                        </div>
                        <h2 class="text-lg font-bold text-gray-800">Presenter</h2>
                    </div>
                    <p class="text-gray-600 mb-6 flex-grow">Participant who will present research, paper, or talk at the conference.</p>
                    <a href="{{ route('admin.participants.create', ['category' => $category, 'role' => 'presenter']) }}" class="w-full text-center bg-[#041E42] hover:bg-[#0A2E5C] text-white py-2 px-4 rounded transition-colors">
                        Select
                    </a>
                </div>
            </div>

            <!-- Exhibitor Card -->
            <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow overflow-hidden border border-gray-200">
                <div class="p-6 flex flex-col h-full">
                    <div class="flex items-center mb-4">
                        <div class="p-2 rounded-full bg-purple-100 text-purple-600 mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="M22 10v3a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V7a4 4 0 0 1 4-4h7"></path><path d="M16 5l3 3-3 3"></path></svg>
                        </div>
                        <h2 class="text-lg font-bold text-gray-800">Exhibitor</h2>
                    </div>
                    <p class="text-gray-600 mb-6 flex-grow">Organization or company representative who will showcase products or services.</p>
                    <a href="{{ route('admin.participants.create', ['category' => $category, 'role' => 'exhibitor']) }}" class="w-full text-center bg-[#041E42] hover:bg-[#0A2E5C] text-white py-2 px-4 rounded transition-colors">
                        Select
                    </a>
                </div>
            </div>
        @elseif($category === 'invited')
            <!-- Guest Card -->
            <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow overflow-hidden border border-gray-200">
                <div class="p-6 flex flex-col h-full">
                    <div class="flex items-center mb-4">
                        <div class="p-2 rounded-full bg-yellow-100 text-yellow-600 mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                        </div>
                        <h2 class="text-lg font-bold text-gray-800">Guest</h2>
                    </div>
                    <p class="text-gray-600 mb-6 flex-grow">Invited participant who attends by special invitation.</p>
                    <a href="{{ route('admin.participants.create', ['category' => $category, 'role' => 'guest']) }}" class="w-full text-center bg-[#041E42] hover:bg-[#0A2E5C] text-white py-2 px-4 rounded transition-colors">
                        Select
                    </a>
                </div>
            </div>

            <!-- VIP Card -->
            <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow overflow-hidden border border-gray-200">
                <div class="p-6 flex flex-col h-full">
                    <div class="flex items-center mb-4">
                        <div class="p-2 rounded-full bg-red-100 text-red-600 mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                        </div>
                        <h2 class="text-lg font-bold text-gray-800">VIP</h2>
                    </div>
                    <p class="text-gray-600 mb-6 flex-grow">Very Important Person with special privileges and seating.</p>
                    <a href="{{ route('admin.participants.create', ['category' => $category, 'role' => 'vip']) }}" class="w-full text-center bg-[#041E42] hover:bg-[#0A2E5C] text-white py-2 px-4 rounded transition-colors">
                        Select
                    </a>
                </div>
            </div>

            <!-- Panelist Card -->
            <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow overflow-hidden border border-gray-200">
                <div class="p-6 flex flex-col h-full">
                    <div class="flex items-center mb-4">
                        <div class="p-2 rounded-full bg-indigo-100 text-indigo-600 mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="M3 3h18v18H3zM8 12h8"></path><path d="M12 8v8"></path></svg>
                        </div>
                        <h2 class="text-lg font-bold text-gray-800">Panelist</h2>
                    </div>
                    <p class="text-gray-600 mb-6 flex-grow">Invited expert who participates in panel discussions and Q&A sessions.</p>
                    <a href="{{ route('admin.participants.create', ['category' => $category, 'role' => 'panelist']) }}" class="w-full text-center bg-[#041E42] hover:bg-[#0A2E5C] text-white py-2 px-4 rounded transition-colors">
                        Select
                    </a>
                </div>
            </div>
        @elseif($category === 'internal')
            <!-- Staff Card -->
            <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow overflow-hidden border border-gray-200">
                <div class="p-6 flex flex-col h-full">
                    <div class="flex items-center mb-4">
                        <div class="p-2 rounded-full bg-blue-100 text-blue-600 mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg>
                        </div>
                        <h2 class="text-lg font-bold text-gray-800">Staff</h2>
                    </div>
                    <p class="text-gray-600 mb-6 flex-grow">Institution staff member attending the conference.</p>
                    <a href="{{ route('admin.participants.create', ['category' => $category, 'role' => 'staff']) }}" class="w-full text-center bg-[#041E42] hover:bg-[#0A2E5C] text-white py-2 px-4 rounded transition-colors">
                        Select
                    </a>
                </div>
            </div>

            <!-- Faculty Card -->
            <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow overflow-hidden border border-gray-200">
                <div class="p-6 flex flex-col h-full">
                    <div class="flex items-center mb-4">
                        <div class="p-2 rounded-full bg-green-100 text-green-600 mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="M22 10v6M2 10l10-5 10 5-10 5z"></path><path d="M6 12v5c3 3 9 3 12 0v-5"></path></svg>
                        </div>
                        <h2 class="text-lg font-bold text-gray-800">Faculty</h2>
                    </div>
                    <p class="text-gray-600 mb-6 flex-grow">Academic faculty member attending the conference.</p>
                    <a href="{{ route('admin.participants.create', ['category' => $category, 'role' => 'faculty']) }}" class="w-full text-center bg-[#041E42] hover:bg-[#0A2E5C] text-white py-2 px-4 rounded transition-colors">
                        Select
                    </a>
                </div>
            </div>

            <!-- Student Card -->
            <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow overflow-hidden border border-gray-200">
                <div class="p-6 flex flex-col h-full">
                    <div class="flex items-center mb-4">
                        <div class="p-2 rounded-full bg-purple-100 text-purple-600 mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>
                        </div>
                        <h2 class="text-lg font-bold text-gray-800">Student</h2>
                    </div>
                    <p class="text-gray-600 mb-6 flex-grow">Student attending the conference with student-specific credentials.</p>
                    <a href="{{ route('admin.participants.create', ['category' => $category, 'role' => 'student']) }}" class="w-full text-center bg-[#041E42] hover:bg-[#0A2E5C] text-white py-2 px-4 rounded transition-colors">
                        Select
                    </a>
                </div>
            </div>
        @elseif($category === 'coordinators')
            <!-- Organizer Card -->
            <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow overflow-hidden border border-gray-200">
                <div class="p-6 flex flex-col h-full">
                    <div class="flex items-center mb-4">
                        <div class="p-2 rounded-full bg-blue-100 text-blue-600 mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                        </div>
                        <h2 class="text-lg font-bold text-gray-800">Organizer</h2>
                    </div>
                    <p class="text-gray-600 mb-6 flex-grow">Main conference organizer with full administrative access.</p>
                    <a href="{{ route('admin.participants.create', ['category' => $category, 'role' => 'organizer']) }}" class="w-full text-center bg-[#041E42] hover:bg-[#0A2E5C] text-white py-2 px-4 rounded transition-colors">
                        Select
                    </a>
                </div>
            </div>

            <!-- Coordinator Card -->
            <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow overflow-hidden border border-gray-200">
                <div class="p-6 flex flex-col h-full">
                    <div class="flex items-center mb-4">
                        <div class="p-2 rounded-full bg-green-100 text-green-600 mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                        </div>
                        <h2 class="text-lg font-bold text-gray-800">Coordinator</h2>
                    </div>
                    <p class="text-gray-600 mb-6 flex-grow">Session coordinator who assists with logistics and participant management.</p>
                    <a href="{{ route('admin.participants.create', ['category' => $category, 'role' => 'coordinator']) }}" class="w-full text-center bg-[#041E42] hover:bg-[#0A2E5C] text-white py-2 px-4 rounded transition-colors">
                        Select
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
