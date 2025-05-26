@extends('layouts.app')

@section('title', 'Meal Service')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-primary">Meal Service</h1>
            <p class="text-gray-600 mt-1">Record participant meals for today's conference</p>
        </div>
        <a href="{{ route('usher.meals.stats') }}" class="flex items-center bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-4 rounded-lg transition-colors">
            <i data-lucide="bar-chart-2" class="h-4 w-4 mr-2"></i>
            <span>View Meal Statistics</span>
        </a>
    </div>
    
    @if(!$today)
        <div class="bg-yellow-50 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6 rounded-r-md shadow-sm" role="alert">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i data-lucide="alert-triangle" class="h-5 w-5 text-yellow-500"></i>
                </div>
                <div class="ml-3">
                    <p class="font-medium">No Conference Today</p>
                    <p class="mt-1">There is no active conference day set for today. Meal service is unavailable.</p>
                </div>
            </div>
        </div>
    @endif
    
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Meal Selection Panel -->
        <div class="md:col-span-1">
            <div class="bg-white shadow-md rounded-lg p-6 h-full">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                    <i data-lucide="utensils" class="h-5 w-5 mr-2 text-primary"></i>
                    Select Meal Type
                </h2>
                
                <div class="space-y-4">
                    @forelse($mealTypes as $mealType)
                        <form action="{{ route('usher.meals.select') }}" method="POST">
                            @csrf
                            <input type="hidden" name="meal_type_id" value="{{ $mealType->id }}">
                            <button type="submit" class="w-full text-left">
                                <div class="border rounded-lg p-4 hover:bg-gray-50 cursor-pointer
                                    {{ isset($stats[$mealType->id]) && $stats[$mealType->id]['is_current'] ? 'border-green-500 bg-green-50' : 'border-gray-200' }} 
                                    {{ $selectedMealTypeId == $mealType->id ? 'border-primary bg-primary/5' : '' }}">
                                    <div class="flex justify-between items-center">
                                        <div class="flex items-center">
                                            <div class="h-10 w-10 rounded-full bg-primary/10 text-primary flex items-center justify-center">
                                                <i data-lucide="utensils" class="h-5 w-5"></i>
                                            </div>
                                            <div class="ml-3">
                                                <h3 class="font-medium text-gray-900">{{ $mealType->name }}</h3>
                                                <p class="text-sm text-gray-500">{{ $mealType->start_time }} - {{ $mealType->end_time }}</p>
                                            </div>
                                        </div>
                                        <div class="h-6 w-6 rounded-full border border-gray-300 flex items-center justify-center">
                                            @if($selectedMealTypeId == $mealType->id)
                                                <i data-lucide="check" class="h-4 w-4 text-primary"></i>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    @if(isset($stats[$mealType->id]))
                                        <div class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between">
                                            <span class="text-sm text-gray-500">Served today:</span>
                                            <span class="font-medium text-primary">{{ $stats[$mealType->id]['count'] }}</span>
                                        </div>
                                        
                                        @if($stats[$mealType->id]['is_current'])
                                            <div class="mt-2 text-xs text-green-600 flex items-center">
                                                <i data-lucide="clock" class="h-3 w-3 mr-1"></i>
                                                <span>Currently being served</span>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </button>
                        </form>
                    @empty
                        <div class="text-center text-gray-500 py-4">
                            <p>No meal types available</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
        
        <!-- Meal Serving Form -->
        <div class="md:col-span-2">
            <div class="bg-white shadow-md rounded-lg p-6">
                @if($selectedMealType)
                    <form id="meal-serving-form" method="POST" action="{{ route('usher.meals.serve') }}">
                        @csrf
                        <input type="hidden" name="meal_type_id" value="{{ $selectedMealTypeId }}">
                        
                        <div class="mb-6">
                            <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                                <i data-lucide="clipboard-check" class="h-5 w-5 mr-2 text-primary"></i>
                                Serve {{ $selectedMealType->name }}
                            </h2>
                            
                            <div class="bg-primary/5 border border-primary/10 rounded-lg p-4 mb-6">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 rounded-full bg-primary/20 text-primary flex items-center justify-center">
                                        <i data-lucide="utensils" class="h-5 w-5"></i>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="font-medium text-gray-900">{{ $selectedMealType->name }}</h3>
                                        <p class="text-sm text-gray-500">{{ $selectedMealType->start_time }} - {{ $selectedMealType->end_time }}</p>
                                    </div>
                                </div>
                            </div>
                            
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
                                        placeholder="1234" required maxlength="4" pattern="[0-9]{4}" inputmode="numeric" autofocus>
                                </div>
                                <p class="text-sm text-gray-500 mt-1">Enter only the last 4 digits of the ticket number</p>
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
                            <div class="mb-6">
                                <label class="block text-gray-700 font-semibold mb-2" for="notes">
                                    Notes (Optional)
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 pt-2 pointer-events-none">
                                        <i data-lucide="file-text" class="h-5 w-5 text-gray-400"></i>
                                    </div>
                                    <textarea name="notes" id="notes" rows="2" 
                                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                                        placeholder="Add any notes about this meal service..."></textarea>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-end">
                                <button type="submit" id="serve-button" 
                                    class="bg-primary hover:bg-primary-dark text-white font-bold py-3 px-6 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors flex items-center">
                                    <i data-lucide="utensils" class="h-5 w-5 mr-2"></i>
                                    Serve {{ $selectedMealType->name }}
                                </button>
                            </div>
                        </div>
                    </form>
                @else
                    <div class="text-center py-8">
                        <div class="inline-flex items-center justify-center h-16 w-16 rounded-full bg-gray-100 text-gray-400 mb-4">
                            <i data-lucide="utensils" class="h-8 w-8"></i>
                        </div>
                        <p class="text-gray-500">No meal type available for serving</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
// Minimal JavaScript - just for submitting forms using AJAX and focusing on ticket input after success
document.addEventListener('DOMContentLoaded', function() {
    // Focus on ticket number input if it exists
    const ticketNumberInput = document.getElementById('ticket_number');
    if (ticketNumberInput) {
        ticketNumberInput.focus();
    }
    
    // If there was a success message, clear the ticket number field
    const successMessage = document.querySelector('.bg-green-50');
    if (successMessage && ticketNumberInput) {
        ticketNumberInput.value = '';
        ticketNumberInput.focus();

        setTimeout(() => {
            successMessage.style.display = 'none';
        }, 4000);
    }

    const errorMessage = document.querySelector('.bg-red-50');
    if (errorMessage && ticketNumberInput) {
        ticketNumberInput.value = '';
        ticketNumberInput.focus();

        setTimeout(() => {
            errorMessage.style.display = 'none';
        }, 4000);
    }
});
</script>
@endsection 