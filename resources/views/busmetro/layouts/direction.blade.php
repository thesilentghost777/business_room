@extends('busmetro.layouts.app')

@section('body')
<div class="flex min-h-screen">
    <aside :class="sidebarOpen ? 'w-64' : 'w-[72px]'" class="hidden lg:flex flex-col bg-white border-r border-gray-100 fixed h-full z-40 transition-all duration-200">
        <div class="flex items-center justify-between h-16 px-4 border-b border-gray-100">
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 rounded-xl bg-bm-600 flex items-center justify-center flex-shrink-0"><i class="fas fa-landmark text-white text-sm"></i></div>
                <span x-show="sidebarOpen" x-cloak class="font-semibold text-gray-900 text-[15px]">Direction</span>
            </div>
            <button @click="sidebarOpen = !sidebarOpen" class="text-gray-400 hover:text-bm-600"><i class="fas" :class="sidebarOpen ? 'fa-chevron-left' : 'fa-chevron-right'"></i></button>
        </div>
        <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">
            <a href="{{ route('busmetro.direction.dashboard') }}" class="sidebar-link flex items-center px-3 py-2 rounded-lg text-gray-600 text-[13px] font-medium {{ request()->routeIs('busmetro.direction.dashboard') ? 'active' : '' }}"><i class="fas fa-chart-line w-5 text-center"></i><span x-show="sidebarOpen" x-cloak class="ml-3">Vue d'ensemble</span></a>
            <a href="{{ route('busmetro.direction.sessions') }}" class="sidebar-link flex items-center px-3 py-2 rounded-lg text-gray-600 text-[13px] font-medium {{ request()->routeIs('busmetro.direction.sessions*') ? 'active' : '' }}"><i class="fas fa-calendar-check w-5 text-center"></i><span x-show="sidebarOpen" x-cloak class="ml-3">Sessions</span></a>
        </nav>
        <div class="p-3 border-t border-gray-100" x-show="sidebarOpen" x-cloak>
            <div class="flex items-center space-x-3 px-2">
                <div class="w-8 h-8 rounded-full bg-bm-100 flex items-center justify-center"><span class="text-bm-700 text-xs font-bold">{{ substr(auth()->guard('busmetro')->user()->prenom ?? 'D', 0, 1) }}</span></div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-gray-900 truncate">{{ auth()->guard('busmetro')->user()->nom_complet ?? 'Direction' }}</p>
                    <p class="text-[10px] text-gray-400">Direction financement</p>
                </div>
                <form action="{{ route('busmetro.logout') }}" method="POST">@csrf<button type="submit" class="text-gray-400 hover:text-red-500"><i class="fas fa-sign-out-alt text-sm"></i></button></form>
            </div>
        </div>
    </aside>

    <div x-show="mobileMenuOpen" @click="mobileMenuOpen = false" class="lg:hidden fixed inset-0 bg-black/30 z-40" x-transition.opacity></div>

    <div :class="sidebarOpen ? 'lg:ml-64' : 'lg:ml-[72px]'" class="flex-1 transition-all duration-200">
        <header class="glass border-b border-gray-100 sticky top-0 z-30">
            <div class="flex items-center justify-between h-14 px-4 lg:px-6">
                <div class="flex items-center space-x-3">
                    <button @click="mobileMenuOpen = true" class="lg:hidden text-gray-500"><i class="fas fa-bars"></i></button>
                    <h1 class="text-[15px] font-semibold text-gray-900">@yield('page-title')</h1>
                </div>
            </div>
        </header>
        <main class="p-4 lg:p-6">
            @if(session('success'))<div class="mb-4 bg-bm-50 border border-bm-200 text-bm-800 px-4 py-3 rounded-xl text-sm"><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</div>@endif
            @if(session('error'))<div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-sm"><i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}</div>@endif
            @yield('content')
        </main>
    </div>
</div>
@endsection
