<nav class="bg-[#041E42] shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <div class="flex-shrink-0 flex items-center">
                    <span class="text-white text-xl font-bold">Karibu</span>
                </div>
                
                <!-- Desktop Navigation -->
                <div class="hidden md:ml-10 md:flex md:space-x-6">
                    @if(Auth::user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}" 
                           class="flex items-center px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.dashboard') ? 'text-white border-b-2 border-white' : 'text-gray-300 hover:text-white hover:border-b-2 hover:border-gray-300' }}">
                            <i data-lucide="layout-dashboard" class="h-5 w-5 mr-1"></i>
                            <span>Dashboard</span>
                        </a>
                        
                        <a href="{{ route('admin.participants') }}" 
                           class="flex items-center px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.participants*') ? 'text-white border-b-2 border-white' : 'text-gray-300 hover:text-white hover:border-b-2 hover:border-gray-300' }}">
                            <i data-lucide="users-2" class="h-5 w-5 mr-1"></i>
                            <span>Participants</span>
                        </a>
                        
                        <a href="{{ route('admin.conference-days.index') }}" 
                           class="flex items-center px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.conference-days*') ? 'text-white border-b-2 border-white' : 'text-gray-300 hover:text-white hover:border-b-2 hover:border-gray-300' }}">
                            <i data-lucide="calendar" class="h-5 w-5 mr-1"></i>
                            <span>Conference Days</span>
                        </a>
                        
                        <a href="{{ route('admin.users') }}" 
                           class="flex items-center px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.users') ? 'text-white border-b-2 border-white' : 'text-gray-300 hover:text-white hover:border-b-2 hover:border-gray-300' }}">
                            <i data-lucide="users" class="h-5 w-5 mr-1"></i>
                            <span>Manage Users</span>
                        </a>
                        
                        <a href="#" 
                           class="flex items-center px-3 py-2 text-sm font-medium text-gray-300 hover:text-white hover:border-b-2 hover:border-gray-300">
                            <i data-lucide="bar-chart" class="h-5 w-5 mr-1"></i>
                            <span>Reports</span>
                        </a>
                        
                        <a href="#" 
                           class="flex items-center px-3 py-2 text-sm font-medium text-gray-300 hover:text-white hover:border-b-2 hover:border-gray-300">
                            <i data-lucide="settings" class="h-5 w-5 mr-1"></i>
                            <span>Settings</span>
                        </a>
                    @elseif(Auth::user()->role === 'usher')
                        <a href="{{ route('usher.dashboard') }}" 
                           class="flex items-center px-3 py-2 text-sm font-medium {{ request()->routeIs('usher.dashboard') ? 'text-white border-b-2 border-white' : 'text-gray-300 hover:text-white hover:border-b-2 hover:border-gray-300' }}">
                            <i data-lucide="layout-dashboard" class="h-5 w-5 mr-1"></i>
                            <span>Dashboard</span>
                        </a>
                        
                        <a href="{{ route('usher.register') }}" 
                           class="flex items-center px-3 py-2 text-sm font-medium {{ request()->routeIs('usher.register*') ? 'text-white border-b-2 border-white' : 'text-gray-300 hover:text-white hover:border-b-2 hover:border-gray-300' }}">
                            <i data-lucide="user-plus" class="h-5 w-5 mr-1"></i>
                            <span>Registration</span>
                        </a>
                        
                        <a href="{{ route('usher.check-in') }}" 
                           class="flex items-center px-3 py-2 text-sm font-medium {{ request()->routeIs('usher.check-in*') ? 'text-white border-b-2 border-white' : 'text-gray-300 hover:text-white hover:border-b-2 hover:border-gray-300' }}">
                            <i data-lucide="check-square" class="h-5 w-5 mr-1"></i>
                            <span>Check-In</span>
                        </a>
                        
                        <a href="{{ route('usher.my-registrations') }}" 
                           class="flex items-center px-3 py-2 text-sm font-medium {{ request()->routeIs('usher.my-registrations') ? 'text-white border-b-2 border-white' : 'text-gray-300 hover:text-white hover:border-b-2 hover:border-gray-300' }} relative">
                            <i data-lucide="list" class="h-5 w-5 mr-1"></i>
                            <span>My Registrations</span>
                            @if(isset($globalPendingCheckIns) && $globalPendingCheckIns > 0)
                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-4 w-4 flex items-center justify-center">
                                {{ $globalPendingCheckIns > 9 ? '9+' : $globalPendingCheckIns }}
                            </span>
                            @endif
                        </a>
                        
                        <a href="{{ route('usher.tickets') }}" 
                           class="flex items-center px-3 py-2 text-sm font-medium {{ request()->routeIs('usher.tickets*') ? 'text-white border-b-2 border-white' : 'text-gray-300 hover:text-white hover:border-b-2 hover:border-gray-300' }}">
                            <i data-lucide="ticket" class="h-5 w-5 mr-1"></i>
                            <span>Tickets</span>
                        </a>
                        
                        <a href="{{ route('usher.meals') }}" 
                           class="flex items-center px-3 py-2 text-sm font-medium {{ request()->routeIs('usher.meals*') ? 'text-white border-b-2 border-white' : 'text-gray-300 hover:text-white hover:border-b-2 hover:border-gray-300' }}">
                            <i data-lucide="utensils" class="h-5 w-5 mr-1"></i>
                            <span>Meals</span>
                        </a>
                    
                    @endif
                </div>
            </div>
            
            <!-- Mobile menu button -->
            <div class="flex md:hidden">
                <button type="button" class="mobile-menu-button bg-[#041E42] inline-flex items-center justify-center p-2 rounded-md text-gray-300 hover:text-white focus:outline-none">
                    <i data-lucide="menu" class="h-6 w-6"></i>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Mobile menu -->
    <div class="mobile-menu hidden md:hidden border-t border-gray-600">
        <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
            @if(Auth::user()->role === 'admin')
                <a href="{{ route('admin.dashboard') }}" 
                   class="flex items-center px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('admin.dashboard') ? 'bg-[#0A2E5C] text-white' : 'text-gray-300 hover:bg-[#0A2E5C] hover:text-white' }}">
                    <i data-lucide="layout-dashboard" class="h-5 w-5 mr-2"></i>
                    <span>Dashboard</span>
                </a>
                
                <a href="{{ route('admin.participants') }}" 
                   class="flex items-center px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('admin.participants*') ? 'bg-[#0A2E5C] text-white' : 'text-gray-300 hover:bg-[#0A2E5C] hover:text-white' }}">
                    <i data-lucide="users-2" class="h-5 w-5 mr-2"></i>
                    <span>Participants</span>
                </a>
                
                <a href="{{ route('admin.conference-days.index') }}" 
                   class="flex items-center px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('admin.conference-days*') ? 'bg-[#0A2E5C] text-white' : 'text-gray-300 hover:bg-[#0A2E5C] hover:text-white' }}">
                    <i data-lucide="calendar" class="h-5 w-5 mr-2"></i>
                    <span>Conference Days</span>
                </a>
                
                <a href="{{ route('admin.users') }}" 
                   class="flex items-center px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('admin.users') ? 'bg-[#0A2E5C] text-white' : 'text-gray-300 hover:bg-[#0A2E5C] hover:text-white' }}">
                    <i data-lucide="users" class="h-5 w-5 mr-2"></i>
                    <span>Manage Users</span>
                </a>
                
                <a href="#" 
                   class="flex items-center px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:bg-[#0A2E5C] hover:text-white">
                    <i data-lucide="bar-chart" class="h-5 w-5 mr-2"></i>
                    <span>Reports</span>
                </a>
                
                <a href="#" 
                   class="flex items-center px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:bg-[#0A2E5C] hover:text-white">
                    <i data-lucide="settings" class="h-5 w-5 mr-2"></i>
                    <span>Settings</span>
                </a>
            @elseif(Auth::user()->role === 'usher')
                <a href="{{ route('usher.dashboard') }}" 
                   class="flex items-center px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('usher.dashboard') ? 'bg-[#0A2E5C] text-white' : 'text-gray-300 hover:bg-[#0A2E5C] hover:text-white' }}">
                    <i data-lucide="layout-dashboard" class="h-5 w-5 mr-2"></i>
                    <span>Dashboard</span>
                </a>
                
                <a href="{{ route('usher.register') }}" 
                   class="flex items-center px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('usher.register*') ? 'bg-[#0A2E5C] text-white' : 'text-gray-300 hover:bg-[#0A2E5C] hover:text-white' }}">
                    <i data-lucide="user-plus" class="h-5 w-5 mr-2"></i>
                    <span>Registration</span>
                </a>
                
                <a href="{{ route('usher.check-in') }}" 
                   class="flex items-center px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('usher.check-in*') ? 'bg-[#0A2E5C] text-white' : 'text-gray-300 hover:bg-[#0A2E5C] hover:text-white' }}">
                    <i data-lucide="check-square" class="h-5 w-5 mr-2"></i>
                    <span>Check-In</span>
                </a>
                
                <a href="{{ route('usher.my-registrations') }}" 
                   class="flex items-center px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('usher.my-registrations') ? 'bg-[#0A2E5C] text-white' : 'text-gray-300 hover:bg-[#0A2E5C] hover:text-white' }} relative">
                    <i data-lucide="list" class="h-5 w-5 mr-2"></i>
                    <span>My Registrations</span>
                    @if(isset($globalPendingCheckIns) && $globalPendingCheckIns > 0)
                    <span class="absolute top-2 left-6 bg-red-500 text-white text-xs rounded-full h-4 w-4 flex items-center justify-center">
                        {{ $globalPendingCheckIns > 9 ? '9+' : $globalPendingCheckIns }}
                    </span>
                    @endif
                </a>
                
                <a href="{{ route('usher.tickets') }}" 
                   class="flex items-center px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('usher.tickets*') ? 'bg-[#0A2E5C] text-white' : 'text-gray-300 hover:bg-[#0A2E5C] hover:text-white' }}">
                    <i data-lucide="ticket" class="h-5 w-5 mr-2"></i>
                    <span>Tickets</span>
                </a>
                
                <a href="{{ route('usher.meals') }}" 
                   class="flex items-center px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('usher.meals*') ? 'bg-[#0A2E5C] text-white' : 'text-gray-300 hover:bg-[#0A2E5C] hover:text-white' }}">
                    <i data-lucide="utensils" class="h-5 w-5 mr-2"></i>
                    <span>Meals</span>
                </a>
        
            @endif
        </div>
    </div>
</nav>

<script>
    // Mobile menu toggle
    document.addEventListener('DOMContentLoaded', function() {
        const btn = document.querySelector('.mobile-menu-button');
        const menu = document.querySelector('.mobile-menu');
        
        btn.addEventListener('click', function() {
            menu.classList.toggle('hidden');
        });
    });
</script>
