@extends('layouts.app')

@section('title', 'Change Password')

@section('content')
<div class="min-h-screen flex flex-col items-center pt-6 px-4 sm:pt-12 bg-gray-50">
    <div class="w-full sm:max-w-md mt-6 bg-white shadow-md overflow-hidden rounded-lg">
        <div class="px-6 py-4 bg-[#041E42] text-white">
            <h2 class="text-center text-lg font-bold">Change Your Password</h2>
        </div>

        <div class="p-6">
            <div class="mb-4">
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i data-lucide="alert-triangle" class="h-5 w-5 text-yellow-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                Please change your default password to continue. You must create a new secure password for your account.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ auth()->user()->role === 'admin' ? route('admin.password.update') : route('usher.password.update') }}">
                @csrf

                <div class="mb-4">
                    <label for="current_password" class="block text-gray-700 font-medium mb-1">Current Password</label>
                    <input id="current_password" type="password" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#041E42]" name="current_password" required>
                    @error('current_password')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password" class="block text-gray-700 font-medium mb-1">New Password</label>
                    <input id="password" type="password" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#041E42]" name="password" required>
                    @error('password')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password_confirmation" class="block text-gray-700 font-medium mb-1">Confirm New Password</label>
                    <input id="password_confirmation" type="password" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#041E42]" name="password_confirmation" required>
                </div>

                <div class="mt-6">
                    <button type="submit" class="w-full bg-[#041E42] hover:bg-[#0A2E5C] text-white py-2 px-4 rounded-md transition duration-300 border border-[#021530]">
                        Change Password & Continue
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 