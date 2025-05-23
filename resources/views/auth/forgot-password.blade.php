<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Karibu App</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Include Lucide icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
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
                <a href="/" class="text-[#041E42] text-2xl font-bold mr-6">
                    <div class="flex items-center">
                        <img src="{{ asset('images/zetech-logo.png') }}" alt="Karibu Logo" class="h-13 w-auto">
                    </div>
                </a>
            </div>
        </div>
    </header>
    
    <div class="flex-1 flex items-center justify-center px-4 py-12">
        <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md border border-gray-200">
            <div class="text-center mb-6">
                <div class="flex justify-center mb-3">
                    <img src="{{ asset('images/zetech-logo.png') }}" alt="Karibu Logo" class="h-20 w-auto">
                </div>
                <h1 class="text-2xl font-bold text-[#041E42]">Reset Password</h1>
                <p class="text-gray-600 mt-1">Enter your email to receive a reset link</p>
            </div>
            
            @if(session('status'))
                <div class="bg-green-100 text-green-700 p-4 rounded mb-4">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="email" class="block text-gray-700 font-medium mb-1">Email</label>
                    <input type="email" name="email" id="email" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#041E42]"
                        required autofocus>
                    @error('email')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>
                
                <button type="submit" class="w-full bg-[#041E42] hover:bg-[#0A2E5C] text-white py-2 px-4 rounded-md transition duration-300 border border-[#021530]">
                    Send Password Reset Link
                </button>

                <div class="text-center mt-4">
                    <a href="{{ route('login') }}" class="text-[#041E42] hover:underline">
                        Back to Login
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <footer class="bg-[#041E42] text-white py-4 mt-auto">
        <div class="container mx-auto px-4 text-center">
            &copy; {{ date('Y') }} Karibu Check-in System. All rights reserved.
        </div>
    </footer>

    <script>
        // Initialize Lucide icons
        lucide.createIcons();
    </script>
</body>
</html>
