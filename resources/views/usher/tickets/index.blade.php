@extends('layouts.app')

@section('title', 'Ticket Search')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-primary">Ticket Search</h1>
        <p class="text-gray-600 mt-1">Find ticket details by entering the 4-digit ticket number</p>
    </div>
    
    @if (session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-r-md shadow-sm" role="alert">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i data-lucide="check-circle" class="h-5 w-5 text-green-500"></i>
                </div>
                <div class="ml-3">
                    <p class="font-medium">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r-md shadow-sm" role="alert">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i data-lucide="alert-circle" class="h-5 w-5 text-red-500"></i>
                </div>
                <div class="ml-3">
                    <p class="font-medium">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif
    
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <div class="max-w-md mx-auto">
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center h-16 w-16 rounded-full bg-primary/10 text-primary mb-4">
                    <i data-lucide="ticket" class="h-8 w-8"></i>
                </div>
                <h2 class="text-xl font-semibold text-gray-800">Search for Tickets</h2>
                <p class="text-gray-600 text-sm mt-1">Enter only the 4-digit portion of the ticket number</p>
            </div>
            
            <form action="{{ route('usher.tickets.search') }}" method="POST">
                @csrf
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2" for="ticket_number">
                        4-Digit Ticket Number
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 font-medium">ZU-RIW25-</span>
                        </div>
                        <input type="text" name="ticket_number" id="ticket_number" 
                            class="w-full pl-24 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                            placeholder="1234" required maxlength="4" pattern="[0-9]{4}" inputmode="numeric">
                    </div>
                    <p class="text-sm text-gray-500 mt-1">Enter only the last 4 digits of the ticket number (e.g., if ticket is ZU-RIW25-1234, enter only 1234)</p>
                </div>
                
                <div class="flex justify-center">
                    <button type="submit" 
                        class="bg-primary hover:bg-primary-dark text-white font-bold py-2 px-6 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors flex items-center">
                        <i data-lucide="search" class="h-5 w-5 mr-2"></i>
                        Search Ticket
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="bg-gray-50 border border-gray-200 rounded-lg p-5">
        <h3 class="font-semibold text-gray-800 mb-3 flex items-center">
            <i data-lucide="info" class="h-5 w-5 mr-2 text-primary"></i>
            Tips for Finding Tickets
        </h3>
        <ul class="list-disc list-inside text-gray-600 space-y-2">
            <li>Enter only the last 4 digits of the ticket number</li>
            <li>The complete ticket number format is: ZU-RIW25-XXXX where XXXX is what you enter</li>
            <li>All conference tickets have the same prefix "ZU-RIW25-"</li>
            <li>If you can't find a ticket, check if the 4 digits were entered correctly</li>
        </ul>
    </div>
</div>
@endsection 