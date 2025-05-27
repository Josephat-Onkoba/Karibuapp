<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Karibu Check-in System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Include Lucide icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <style>
        :root {
            --primary-color: #041E42;
            --primary-light: #0A2E5C;
            --primary-dark: #021530;
        }
        .text-primary { color: var(--primary-color); }
        .bg-primary { background-color: var(--primary-color); }
        .border-primary { border-color: var(--primary-color); }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
    <!-- Sticky Navigation Bar -->
    <header class="sticky top-0 bg-white shadow-md z-50">
        <div class="container mx-auto flex justify-between items-center py-3 px-4">
            <div class="flex items-center">
            <img src="{{ asset('images/zetech-logo.png') }}" alt="Karibu Logo" class="h-13 w-auto">
            </div>
            
            @auth
                <div class="relative">
                    <button id="profileDropdownButton" class="flex items-center space-x-2 text-gray-700 hover:text-[#041E42] focus:outline-none p-2">
                        <span>{{ Auth::user()->name }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transform transition-transform duration-200" id="profileArrow" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div id="profileDropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 hidden border border-gray-300 z-50">
                        <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Profile</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            @endauth
        </div>
    </header>

    @auth
        @include('layouts.navigation')
    @endauth

    <main class="flex-1 px-4 py-6">
        @yield('content')
    </main>

    <footer class="bg-[#041E42] text-white py-4">
        <div class="container mx-auto px-4 text-center">
            &copy; {{ date('Y') }} Karibu Check-in System. All rights reserved.
        </div>
    </footer>

    <!-- Alpine.js - for x-data attributes -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <script>
        // Initialize Lucide icons
        lucide.createIcons();
        
        // Profile dropdown functionality
        document.addEventListener('DOMContentLoaded', function() {
            const profileDropdownButton = document.getElementById('profileDropdownButton');
            const profileDropdown = document.getElementById('profileDropdown');
            const profileArrow = document.getElementById('profileArrow');
            
            if (profileDropdownButton && profileDropdown) {
                // Toggle dropdown on click
                profileDropdownButton.addEventListener('click', function(e) {
                    e.stopPropagation();
                    profileDropdown.classList.toggle('hidden');
                    profileArrow.classList.toggle('rotate-180');
                });
                
                // Close dropdown when clicking elsewhere
                document.addEventListener('click', function(e) {
                    if (!profileDropdownButton.contains(e.target) && !profileDropdown.contains(e.target)) {
                        profileDropdown.classList.add('hidden');
                        profileArrow.classList.remove('rotate-180');
                    }
                });
                
                // Handle touch events for mobile
                document.addEventListener('touchstart', function(e) {
                    if (!profileDropdownButton.contains(e.target) && !profileDropdown.contains(e.target)) {
                        profileDropdown.classList.add('hidden');
                        profileArrow.classList.remove('rotate-180');
                    }
                }, { passive: true });
            }
            
            // Re-initialize Lucide icons after DOM updates
            setTimeout(function() {
                lucide.createIcons();
            }, 500);
        });
    </script>
    
    <!-- Additional scripts from child views -->
    @stack('scripts')
</body>
</html>
