@extends('layouts.app')

@section('title', 'Manage Users')

@section('content')
    <div class="container mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-[#041E42]">Manage Users</h1>
            <a href="{{ route('admin.users.create') }}" class="bg-[#041E42] hover:bg-[#0A2E5C] text-white py-2 px-4 rounded-md transition duration-300 border border-[#021530] flex items-center">
                <i data-lucide="user-plus" class="h-5 w-5 mr-1"></i>
                <span>Add New User</span>
            </a>
        </div>
        
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded">
                {{ session('success') }}
            </div>
        @endif
        
        <div class="bg-white rounded-lg shadow overflow-hidden border border-gray-200 mb-8">
            <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-[#041E42]">System Users</h2>
                <div class="relative">
                    <input type="text" id="searchInput" 
                           placeholder="Search users..." 
                           class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-[#041E42] focus:border-[#041E42] text-sm w-64">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i data-lucide="search" class="h-4 w-4 text-gray-400"></i>
                    </div>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-3 bg-gray-50 border-b border-gray-200">Name</th>
                            <th class="px-6 py-3 bg-gray-50 border-b border-gray-200">Email</th>
                            <th class="px-6 py-3 bg-gray-50 border-b border-gray-200">Role</th>
                            <th class="px-6 py-3 bg-gray-50 border-b border-gray-200">Created</th>
                            <th class="px-6 py-3 bg-gray-50 border-b border-gray-200 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200" id="usersTableBody">
                        @forelse($users as $user)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center border border-gray-200 text-[#041E42]">
                                            {{ substr($user->name, 0, 1) }}
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $user->email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800 border border-purple-200' : 'bg-blue-100 text-blue-800 border border-blue-200' }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">{{ $user->created_at->format('M d, Y') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                    <a href="{{ route('admin.users.edit', $user->id) }}" class="text-[#041E42] hover:text-[#0A2E5C]">Edit</a>
                                    <form action="{{ route('admin.users.delete', $user->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Are you sure you want to delete this user?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                    No users found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $users->links() }}
            </div>
        </div>
    </div>
    
    <script>
        // Simple client-side search functionality
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const tableBody = document.getElementById('usersTableBody');
            const rows = tableBody.querySelectorAll('tr');
            
            searchInput.addEventListener('keyup', function() {
                const query = this.value.toLowerCase();
                
                rows.forEach(row => {
                    const nameCell = row.querySelector('td:first-child');
                    const emailCell = row.querySelector('td:nth-child(2)');
                    
                    if (!nameCell || !emailCell) return;
                    
                    const name = nameCell.textContent.toLowerCase();
                    const email = emailCell.textContent.toLowerCase();
                    
                    if (name.includes(query) || email.includes(query)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        });
    </script>
@endsection
