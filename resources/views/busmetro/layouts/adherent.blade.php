@extends('busmetro.layouts.app')

@section('body')
<div class="min-h-screen bg-gray-50/50">
    <!-- Top navbar for adherent (mobile-first) -->
    <header class="glass border-b border-gray-100 sticky top-0 z-50">
        <div class="max-w-5xl mx-auto flex items-center justify-between h-14 px-4">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 rounded-lg bg-bm-600 flex items-center justify-center"><i class="fas fa-landmark text-white text-xs"></i></div>
                <span class="font-semibold text-gray-900 text-sm">Business Room</span>
            </div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('busmetro.adherent.notifications') }}" class="relative p-2 text-gray-400 hover:text-bm-600">
                    <i class="fas fa-bell"></i>
                    @if(($notifCount ?? 0) > 0)<span class="absolute top-0 right-0 w-4 h-4 bg-red-500 text-white text-[9px] rounded-full flex items-center justify-center">{{ $notifCount }}</span>@endif
                </a>
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="w-8 h-8 rounded-full bg-bm-100 flex items-center justify-center">
                        <span class="text-bm-700 text-xs font-bold">{{ substr(auth()->guard('adherent')->user()->prenom ?? 'M', 0, 1) }}</span>
                    </button>
                    <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-50">
                        <a href="{{ route('busmetro.adherent.profil') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-bm-50"><i class="fas fa-user mr-2 text-gray-400"></i>Mon profil</a>
                        <hr class="my-1 border-gray-100">
                        <form action="{{ route('busmetro.adherent.logout') }}" method="POST">@csrf<button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50"><i class="fas fa-sign-out-alt mr-2"></i>Déconnexion</button></form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Bottom navigation mobile -->
    <nav class="lg:hidden fixed bottom-0 inset-x-0 bg-white border-t border-gray-100 z-50 safe-area-bottom">
        <div class="grid grid-cols-5 h-14">
            <a href="{{ route('busmetro.adherent.dashboard') }}" class="flex flex-col items-center justify-center {{ request()->routeIs('busmetro.adherent.dashboard') ? 'text-bm-600' : 'text-gray-400' }}">
                <i class="fas fa-home text-lg"></i><span class="text-[9px] mt-0.5">Accueil</span>
            </a>
            <a href="{{ route('busmetro.adherent.cotisations') }}" class="flex flex-col items-center justify-center {{ request()->routeIs('busmetro.adherent.cotisations*') ? 'text-bm-600' : 'text-gray-400' }}">
                <i class="fas fa-coins text-lg"></i><span class="text-[9px] mt-0.5">Cotisations</span>
            </a>
            <a href="{{ route('busmetro.adherent.financement') }}" class="flex flex-col items-center justify-center {{ request()->routeIs('busmetro.adherent.financement*') ? 'text-bm-600' : 'text-gray-400' }}">
                <i class="fas fa-hand-holding-usd text-lg"></i><span class="text-[9px] mt-0.5">Finance</span>
            </a>
            <a href="{{ route('busmetro.adherent.carnet') }}" class="flex flex-col items-center justify-center {{ request()->routeIs('busmetro.adherent.carnet*') ? 'text-bm-600' : 'text-gray-400' }}">
                <i class="fas fa-book text-lg"></i><span class="text-[9px] mt-0.5">Carnet</span>
            </a>
            <a href="{{ route('busmetro.adherent.kits') }}" class="flex flex-col items-center justify-center {{ request()->routeIs('busmetro.adherent.kits*') ? 'text-bm-600' : 'text-gray-400' }}">
                <i class="fas fa-box text-lg"></i>
                <span class="text-[9px] mt-0.5">Kits</span>
            </a>
        </div>
    </nav>

    <!-- Desktop sidebar for adherent -->
    <div class="hidden lg:flex">
        <aside class="w-56 bg-white border-r border-gray-100 fixed h-full pt-14">
            <nav class="px-3 py-4 space-y-0.5">
                <a href="{{ route('busmetro.adherent.dashboard') }}" class="sidebar-link flex items-center px-3 py-2 rounded-lg text-gray-600 text-[13px] font-medium {{ request()->routeIs('busmetro.adherent.dashboard') ? 'active' : '' }}"><i class="fas fa-home w-5 text-center"></i><span class="ml-3">Accueil</span></a>
                <a href="{{ route('busmetro.adherent.cotisations') }}" class="sidebar-link flex items-center px-3 py-2 rounded-lg text-gray-600 text-[13px] font-medium {{ request()->routeIs('busmetro.adherent.cotisations*') ? 'active' : '' }}"><i class="fas fa-coins w-5 text-center"></i><span class="ml-3">Cotisations</span></a>
                <a href="{{ route('busmetro.adherent.financement') }}" class="sidebar-link flex items-center px-3 py-2 rounded-lg text-gray-600 text-[13px] font-medium {{ request()->routeIs('busmetro.adherent.financement*') ? 'active' : '' }}"><i class="fas fa-hand-holding-usd w-5 text-center"></i><span class="ml-3">Financement</span></a>
                <a href="{{ route('busmetro.adherent.carnet') }}" class="sidebar-link flex items-center px-3 py-2 rounded-lg text-gray-600 text-[13px] font-medium {{ request()->routeIs('busmetro.adherent.carnet*') ? 'active' : '' }}"><i class="fas fa-book w-5 text-center"></i><span class="ml-3">Carnet de recettes</span></a>
                <a href="{{ route('busmetro.adherent.notifications') }}" class="sidebar-link flex items-center px-3 py-2 rounded-lg text-gray-600 text-[13px] font-medium {{ request()->routeIs('busmetro.adherent.notifications*') ? 'active' : '' }}"><i class="fas fa-bell w-5 text-center"></i><span class="ml-3">Notifications</span></a>
                <a href="{{ route('busmetro.adherent.profil') }}" class="sidebar-link flex items-center px-3 py-2 rounded-lg text-gray-600 text-[13px] font-medium {{ request()->routeIs('busmetro.adherent.profil*') ? 'active' : '' }}"><i class="fas fa-user w-5 text-center"></i><span class="ml-3">Mon profil</span></a>
            </nav>
        </aside>
        <div class="ml-56 flex-1">
            <main class="p-6 max-w-5xl mx-auto">
                @if(session('success'))<div class="mb-4 bg-bm-50 border border-bm-200 text-bm-800 px-4 py-3 rounded-xl text-sm"><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</div>@endif
                @if(session('error'))<div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-sm"><i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}</div>@endif
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Mobile content -->
    <div class="lg:hidden pb-16">
        <main class="p-4 max-w-lg mx-auto">
            @if(session('success'))<div class="mb-4 bg-bm-50 border border-bm-200 text-bm-800 px-3 py-2.5 rounded-xl text-sm"><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</div>@endif
            @if(session('error'))<div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-3 py-2.5 rounded-xl text-sm"><i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}</div>@endif
            @yield('content')
        </main>
    </div>
</div>
@endsection
