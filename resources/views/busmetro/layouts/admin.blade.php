@extends('busmetro.layouts.app')

@section('body')
<div class="flex min-h-screen">
    <!-- Sidebar Desktop -->
    <aside :class="sidebarOpen ? 'w-64' : 'w-[72px]'" class="hidden lg:flex flex-col bg-white border-r border-gray-100 fixed h-full z-40 transition-all duration-200">
        <div class="flex items-center justify-between h-16 px-4 border-b border-gray-100">
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 rounded-xl bg-bm-600 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-landmark text-white text-sm"></i>
                </div>
                <span x-show="sidebarOpen" x-cloak class="font-semibold text-gray-900 text-[15px] tracking-tight">Business Room</span>
            </div>
            <button @click="sidebarOpen = !sidebarOpen" class="text-gray-400 hover:text-bm-600 transition-colors">
                <i class="fas" :class="sidebarOpen ? 'fa-chevron-left' : 'fa-chevron-right'" class="text-xs"></i>
            </button>
        </div>

        <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">
            <a href="{{ route('busmetro.admin.dashboard') }}" class="sidebar-link flex items-center px-3 py-2 rounded-lg text-gray-600 text-[13px] font-medium {{ request()->routeIs('busmetro.admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-chart-pie w-5 text-center"></i>
                <span x-show="sidebarOpen" x-cloak class="ml-3">Tableau de bord</span>
            </a>
            <a href="{{ route('busmetro.admin.adherents.index') }}" class="sidebar-link flex items-center px-3 py-2 rounded-lg text-gray-600 text-[13px] font-medium {{ request()->routeIs('busmetro.admin.adherents.*') ? 'active' : '' }}">
                <i class="fas fa-users w-5 text-center"></i>
                <span x-show="sidebarOpen" x-cloak class="ml-3">Adhérents</span>
            </a>
            <a href="{{ route('busmetro.admin.users.index') }}" class="sidebar-link flex items-center px-3 py-2 rounded-lg text-gray-600 text-[13px] font-medium {{ request()->routeIs('busmetro.admin.users.*') ? 'active' : '' }}">
                <i class="fas fa-user-shield w-5 text-center"></i>
                <span x-show="sidebarOpen" x-cloak class="ml-3">Staff</span>
            </a>

            <div x-show="sidebarOpen" x-cloak class="pt-4 pb-1"><span class="px-3 text-[10px] font-semibold uppercase tracking-wider text-gray-400">Finance</span></div>
            <div x-show="!sidebarOpen" x-cloak class="pt-4 border-t border-gray-100"></div>

            <a href="{{ route('busmetro.admin.cotisations.index') }}" class="sidebar-link flex items-center px-3 py-2 rounded-lg text-gray-600 text-[13px] font-medium {{ request()->routeIs('busmetro.admin.cotisations.*') ? 'active' : '' }}">
                <i class="fas fa-coins w-5 text-center"></i>
                <span x-show="sidebarOpen" x-cloak class="ml-3">Cotisations</span>
            </a>
            <a href="{{ route('busmetro.admin.sessions.index') }}" class="sidebar-link flex items-center px-3 py-2 rounded-lg text-gray-600 text-[13px] font-medium {{ request()->routeIs('busmetro.admin.sessions.*') ? 'active' : '' }}">
                <i class="fas fa-calendar-check w-5 text-center"></i>
                <span x-show="sidebarOpen" x-cloak class="ml-3">Sessions</span>
            </a>
            <a href="{{ route('busmetro.admin.financements.index') }}" class="sidebar-link flex items-center px-3 py-2 rounded-lg text-gray-600 text-[13px] font-medium {{ request()->routeIs('busmetro.admin.financements.*') ? 'active' : '' }}">
                <i class="fas fa-hand-holding-usd w-5 text-center"></i>
                <span x-show="sidebarOpen" x-cloak class="ml-3">Financements</span>
            </a>
            <a href="{{ route('busmetro.admin.soutiens.index') }}" class="sidebar-link flex items-center px-3 py-2 rounded-lg text-gray-600 text-[13px] font-medium {{ request()->routeIs('busmetro.admin.soutiens.*') ? 'active' : '' }}">
                <i class="fas fa-heart w-5 text-center"></i>
                <span x-show="sidebarOpen" x-cloak class="ml-3">Soutiens NKH</span>
            </a>
            <a href="{{ route('busmetro.admin.transactions.index') }}" class="sidebar-link flex items-center px-3 py-2 rounded-lg text-gray-600 text-[13px] font-medium {{ request()->routeIs('busmetro.admin.transactions.*') ? 'active' : '' }}">
                <i class="fas fa-exchange-alt w-5 text-center"></i>
                <span x-show="sidebarOpen" x-cloak class="ml-3">Transactions</span>
            </a>

            <div x-show="sidebarOpen" x-cloak class="pt-4 pb-1"><span class="px-3 text-[10px] font-semibold uppercase tracking-wider text-gray-400">Système</span></div>
            <div x-show="!sidebarOpen" x-cloak class="pt-4 border-t border-gray-100"></div>

            <a href="{{ route('busmetro.admin.configuration.index') }}" class="sidebar-link flex items-center px-3 py-2 rounded-lg text-gray-600 text-[13px] font-medium {{ request()->routeIs('busmetro.admin.configuration.*') ? 'active' : '' }}">
                <i class="fas fa-sliders-h w-5 text-center"></i>
                <span x-show="sidebarOpen" x-cloak class="ml-3">Configuration</span>
            </a>
        </nav>

        <div class="p-3 border-t border-gray-100" x-show="sidebarOpen" x-cloak>
            <div class="flex items-center space-x-3 px-2">
                <div class="w-8 h-8 rounded-full bg-bm-100 flex items-center justify-center">
                    <span class="text-bm-700 text-xs font-bold">{{ substr(auth()->guard('busmetro')->user()->prenom ?? 'A', 0, 1) }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-gray-900 truncate">{{ auth()->guard('busmetro')->user()->nom_complet ?? 'Admin' }}</p>
                    <p class="text-[10px] text-gray-400">Administrateur</p>
                </div>
                <form action="{{ route('busmetro.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-gray-400 hover:text-red-500 transition-colors"><i class="fas fa-sign-out-alt text-sm"></i></button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Mobile overlay -->
    <div x-show="mobileMenuOpen" @click="mobileMenuOpen = false" class="lg:hidden fixed inset-0 bg-black/30 z-40" x-transition.opacity></div>

    <!-- Mobile sidebar -->
    <aside x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" class="lg:hidden fixed inset-y-0 left-0 w-72 bg-white shadow-2xl z-50 overflow-y-auto">
        <div class="flex items-center justify-between h-16 px-4 border-b border-gray-100">
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 rounded-xl bg-bm-600 flex items-center justify-center"><i class="fas fa-landmark text-white text-sm"></i></div>
                <span class="font-semibold text-gray-900">Business Room</span>
            </div>
            <button @click="mobileMenuOpen = false" class="text-gray-400"><i class="fas fa-times"></i></button>
        </div>
        <nav class="px-3 py-4 space-y-0.5">
            <a href="{{ route('busmetro.admin.dashboard') }}" class="sidebar-link flex items-center px-3 py-2.5 rounded-lg text-gray-600 text-sm font-medium {{ request()->routeIs('busmetro.admin.dashboard') ? 'active' : '' }}"><i class="fas fa-chart-pie w-5"></i><span class="ml-3">Tableau de bord</span></a>
            <a href="{{ route('busmetro.admin.adherents.index') }}" class="sidebar-link flex items-center px-3 py-2.5 rounded-lg text-gray-600 text-sm font-medium {{ request()->routeIs('busmetro.admin.adherents.*') ? 'active' : '' }}"><i class="fas fa-users w-5"></i><span class="ml-3">Adhérents</span></a>
            <a href="{{ route('busmetro.admin.users.index') }}" class="sidebar-link flex items-center px-3 py-2.5 rounded-lg text-gray-600 text-sm font-medium {{ request()->routeIs('busmetro.admin.users.*') ? 'active' : '' }}"><i class="fas fa-user-shield w-5"></i><span class="ml-3">Staff</span></a>
            <a href="{{ route('busmetro.admin.cotisations.index') }}" class="sidebar-link flex items-center px-3 py-2.5 rounded-lg text-gray-600 text-sm font-medium {{ request()->routeIs('busmetro.admin.cotisations.*') ? 'active' : '' }}"><i class="fas fa-coins w-5"></i><span class="ml-3">Cotisations</span></a>
            <a href="{{ route('busmetro.admin.sessions.index') }}" class="sidebar-link flex items-center px-3 py-2.5 rounded-lg text-gray-600 text-sm font-medium {{ request()->routeIs('busmetro.admin.sessions.*') ? 'active' : '' }}"><i class="fas fa-calendar-check w-5"></i><span class="ml-3">Sessions</span></a>
            <a href="{{ route('busmetro.admin.financements.index') }}" class="sidebar-link flex items-center px-3 py-2.5 rounded-lg text-gray-600 text-sm font-medium {{ request()->routeIs('busmetro.admin.financements.*') ? 'active' : '' }}"><i class="fas fa-hand-holding-usd w-5"></i><span class="ml-3">Financements</span></a>
            <a href="{{ route('busmetro.admin.soutiens.index') }}" class="sidebar-link flex items-center px-3 py-2.5 rounded-lg text-gray-600 text-sm font-medium {{ request()->routeIs('busmetro.admin.soutiens.*') ? 'active' : '' }}"><i class="fas fa-heart w-5"></i><span class="ml-3">Soutiens NKH</span></a>
            <a href="{{ route('busmetro.admin.transactions.index') }}" class="sidebar-link flex items-center px-3 py-2.5 rounded-lg text-gray-600 text-sm font-medium {{ request()->routeIs('busmetro.admin.transactions.*') ? 'active' : '' }}"><i class="fas fa-exchange-alt w-5"></i><span class="ml-3">Transactions</span></a>
            <a href="{{ route('busmetro.admin.configuration.index') }}" class="sidebar-link flex items-center px-3 py-2.5 rounded-lg text-gray-600 text-sm font-medium {{ request()->routeIs('busmetro.admin.configuration.*') ? 'active' : '' }}"><i class="fas fa-sliders-h w-5"></i><span class="ml-3">Configuration</span></a>
        </nav>
    </aside>

    <!-- Main Content -->
    <div :class="sidebarOpen ? 'lg:ml-64' : 'lg:ml-[72px]'" class="flex-1 transition-all duration-200">
        <header class="glass border-b border-gray-100 sticky top-0 z-30">
            <div class="flex items-center justify-between h-14 px-4 lg:px-6">
                <div class="flex items-center space-x-3">
                    <button @click="mobileMenuOpen = true" class="lg:hidden text-gray-500"><i class="fas fa-bars"></i></button>
                    <h1 class="text-[15px] font-semibold text-gray-900">@yield('page-title', 'Tableau de bord')</h1>
                </div>
                <div class="flex items-center space-x-3">
                    <button class="relative text-gray-400 hover:text-bm-600 transition-colors">
                        <i class="fas fa-bell"></i>
                        <span class="absolute -top-1 -right-1 w-3.5 h-3.5 bg-red-500 text-white text-[9px] rounded-full flex items-center justify-center">3</span>
                    </button>
                </div>
            </div>
        </header>

        <main class="p-4 lg:p-6">
            @if(session('success'))
            <div class="mb-4 bg-bm-50 border border-bm-200 text-bm-800 px-4 py-3 rounded-xl text-sm flex items-center" x-data="{ show: true }" x-show="show">
                <i class="fas fa-check-circle mr-2 text-bm-500"></i>
                <span class="flex-1">{{ session('success') }}</span>
                <button @click="show = false" class="text-bm-400 hover:text-bm-600"><i class="fas fa-times"></i></button>
            </div>
            @endif
            @if(session('error'))
            <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-sm flex items-center" x-data="{ show: true }" x-show="show">
                <i class="fas fa-exclamation-circle mr-2 text-red-500"></i>
                <span class="flex-1">{{ session('error') }}</span>
                <button @click="show = false" class="text-red-400 hover:text-red-600"><i class="fas fa-times"></i></button>
            </div>
            @endif
            @if($errors->any())
            <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-sm">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>
@endsection
