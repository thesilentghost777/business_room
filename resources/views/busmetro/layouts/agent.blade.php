@extends('busmetro.layouts.app')

@section('body')
<div class="flex min-h-screen">
    <aside :class="sidebarOpen ? 'w-64' : 'w-[72px]'" class="hidden lg:flex flex-col bg-white border-r border-gray-100 fixed h-full z-40 transition-all duration-200">
        <div class="flex items-center justify-between h-16 px-4 border-b border-gray-100">
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 rounded-xl bg-bm-600 flex items-center justify-center flex-shrink-0"><i class="fas fa-landmark text-white text-sm"></i></div>
                <span x-show="sidebarOpen" x-cloak class="font-semibold text-gray-900 text-[15px]">Agent Terrain</span>
            </div>
            <button @click="sidebarOpen = !sidebarOpen" class="text-gray-400 hover:text-bm-600"><i class="fas" :class="sidebarOpen ? 'fa-chevron-left' : 'fa-chevron-right'"></i></button>
        </div>
        <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">
            <a href="{{ route('busmetro.agent.dashboard') }}" class="sidebar-link flex items-center px-3 py-2 rounded-lg text-gray-600 text-[13px] font-medium {{ request()->routeIs('busmetro.agent.dashboard') ? 'active' : '' }}"><i class="fas fa-chart-pie w-5 text-center"></i><span x-show="sidebarOpen" x-cloak class="ml-3">Tableau de bord</span></a>
            <a href="{{ route('busmetro.agent.enrolement.index') }}" class="sidebar-link flex items-center px-3 py-2 rounded-lg text-gray-600 text-[13px] font-medium {{ request()->routeIs('busmetro.agent.enrolement.*') ? 'active' : '' }}"><i class="fas fa-user-plus w-5 text-center"></i><span x-show="sidebarOpen" x-cloak class="ml-3">Enrôlement</span></a>
            <a href="{{ route('busmetro.agent.collecte.cotisation') }}" class="sidebar-link flex items-center px-3 py-2 rounded-lg text-gray-600 text-[13px] font-medium {{ request()->routeIs('busmetro.agent.collecte.cotisation*') ? 'active' : '' }}"><i class="fas fa-coins w-5 text-center"></i><span x-show="sidebarOpen" x-cloak class="ml-3">Collecte Cotisations</span></a>
            <a href="{{ route('busmetro.agent.collecte.remboursement') }}" class="sidebar-link flex items-center px-3 py-2 rounded-lg text-gray-600 text-[13px] font-medium {{ request()->routeIs('busmetro.agent.collecte.remboursement*') ? 'active' : '' }}"><i class="fas fa-undo w-5 text-center"></i><span x-show="sidebarOpen" x-cloak class="ml-3">Remboursements</span></a>
            <a href="{{ route('busmetro.agent.carnets.index') }}" class="sidebar-link flex items-center px-3 py-2 rounded-lg text-gray-600 text-[13px] font-medium {{ request()->routeIs('busmetro.agent.carnets.*') ? 'active' : '' }}"><i class="fas fa-book w-5 text-center"></i><span x-show="sidebarOpen" x-cloak class="ml-3">Carnets de recettes</span></a>
        </nav>
        <div class="p-3 border-t border-gray-100" x-show="sidebarOpen" x-cloak>
            <div class="flex items-center space-x-3 px-2">
                <div class="w-8 h-8 rounded-full bg-bm-100 flex items-center justify-center"><span class="text-bm-700 text-xs font-bold">{{ substr(auth()->guard('busmetro')->user()->prenom ?? 'A', 0, 1) }}</span></div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-gray-900 truncate">{{ auth()->guard('busmetro')->user()->nom_complet ?? 'Agent' }}</p>
                    <p class="text-[10px] text-gray-400">Agent terrain</p>
                </div>
                <form action="{{ route('busmetro.logout') }}" method="POST">@csrf<button type="submit" class="text-gray-400 hover:text-red-500"><i class="fas fa-sign-out-alt text-sm"></i></button></form>
            </div>
        </div>
    </aside>

    <div x-show="mobileMenuOpen" @click="mobileMenuOpen = false" class="lg:hidden fixed inset-0 bg-black/30 z-40" x-transition.opacity></div>
    <aside x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" class="lg:hidden fixed inset-y-0 left-0 w-72 bg-white shadow-2xl z-50">
        <div class="flex items-center justify-between h-16 px-4 border-b border-gray-100">
            <span class="font-semibold text-gray-900">Agent Terrain</span>
            <button @click="mobileMenuOpen = false" class="text-gray-400"><i class="fas fa-times"></i></button>
        </div>
        <nav class="px-3 py-4 space-y-0.5">
            <a href="{{ route('busmetro.agent.dashboard') }}" class="sidebar-link flex items-center px-3 py-2.5 rounded-lg text-gray-600 text-sm font-medium {{ request()->routeIs('busmetro.agent.dashboard') ? 'active' : '' }}"><i class="fas fa-chart-pie w-5"></i><span class="ml-3">Tableau de bord</span></a>
            <a href="{{ route('busmetro.agent.enrolement.index') }}" class="sidebar-link flex items-center px-3 py-2.5 rounded-lg text-gray-600 text-sm font-medium"><i class="fas fa-user-plus w-5"></i><span class="ml-3">Enrôlement</span></a>
            <a href="{{ route('busmetro.agent.collecte.cotisation') }}" class="sidebar-link flex items-center px-3 py-2.5 rounded-lg text-gray-600 text-sm font-medium"><i class="fas fa-coins w-5"></i><span class="ml-3">Collecte Cotisations</span></a>
            <a href="{{ route('busmetro.agent.collecte.remboursement') }}" class="sidebar-link flex items-center px-3 py-2.5 rounded-lg text-gray-600 text-sm font-medium"><i class="fas fa-undo w-5"></i><span class="ml-3">Remboursements</span></a>
            <a href="{{ route('busmetro.agent.carnets.index') }}" class="sidebar-link flex items-center px-3 py-2.5 rounded-lg text-gray-600 text-sm font-medium"><i class="fas fa-book w-5"></i><span class="ml-3">Carnets de recettes</span></a>
        </nav>
    </aside>

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
