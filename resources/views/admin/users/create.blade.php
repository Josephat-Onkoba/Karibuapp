@extends('layouts.app')

@section('title', 'Add New User')

@section('content')
    <div class="container mx-auto">
        <div class="mb-6">
            <div class="flex items-center mb-2">
                <a href="{{ route('admin.users') }}" class="text-[#041E42] hover:text-[#0A2E5C] mr-2">
                    <i data-lucide="arrow-left" class="h-5 w-5"></i>
                </a>
                <h1 class="text-2xl font-bold text-[#041E42]">Add New User</h1>
            </div>
            <p class="text-gray-600">Create a new user account with either admin or usher privileges.</p>
        </div>
        
        @if($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded">
                <ul class="list-disc pl-4">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <div class="bg-white rounded-lg shadow overflow-hidden border border-gray-200 max-w-2xl">
            <div class="p-6">
                <form action="{{ route('admin.users.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="name" class="block text-gray-700 font-medium mb-1">Full Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#041E42]"
                               placeholder="Enter full name" required>
                        @error('name')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="email" class="block text-gray-700 font-medium mb-1">Email Address</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#041E42]"
                               placeholder="Enter email address" required>
                        @error('email')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div class="mb-6">
                        <label for="role" class="block text-gray-700 font-medium mb-1">User Role</label>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="border rounded-md p-4 cursor-pointer flex items-start" id="role-usher">
                                <div>
                                    <input type="radio" name="role" id="role-option-usher" value="usher" class="mr-2" 
                                           {{ old('role') == 'usher' ? 'checked' : 'checked' }}>
                                </div>
                                <div class="ml-2">
                                    <label for="role-option-usher" class="font-medium text-gray-900 block">Usher</label>
                                    <p class="text-sm text-gray-500">Can register attendees and manage check-ins.</p>
                                </div>
                            </div>
                            <div class="border rounded-md p-4 cursor-pointer flex items-start" id="role-admin">
                                <div>
                                    <input type="radio" name="role" id="role-option-admin" value="admin" class="mr-2" 
                                           {{ old('role') == 'admin' ? 'checked' : '' }}>
                                </div>
                                <div class="ml-2">
                                    <label for="role-option-admin" class="font-medium text-gray-900 block">Admin</label>
                                    <p class="text-sm text-gray-500">Full system access, including user management.</p>
                                </div>
                            </div>
                        </div>
                        @error('role')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div class="border-t border-gray-200 pt-5">
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('admin.users') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#041E42]">
                                Cancel
                            </a>
                            <button type="submit" class="w-full bg-[#041E42] hover:bg-[#0A2E5C] text-white py-2 px-4 rounded-md transition duration-300 border border-[#021530]">
                                Create User
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const usherBox = document.getElementById('role-usher');
            const adminBox = document.getElementById('role-admin');
            const usherRadio = document.getElementById('role-option-usher');
            const adminRadio = document.getElementById('role-option-admin');
            
            usherBox.addEventListener('click', function() {
                usherRadio.checked = true;
                updateSelection();
            });
            
            adminBox.addEventListener('click', function() {
                adminRadio.checked = true;
                updateSelection();
            });
            
            function updateSelection() {
                if (usherRadio.checked) {
                    usherBox.classList.add('ring-2', 'ring-[#041E42]');
                    adminBox.classList.remove('ring-2', 'ring-[#041E42]');
                } else {
                    adminBox.classList.add('ring-2', 'ring-[#041E42]');
                    usherBox.classList.remove('ring-2', 'ring-[#041E42]');
                }
            }
            
            // Initial state
            updateSelection();
        });
    </script>
@endsection 