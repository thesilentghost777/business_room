@extends('busmetro.layouts.adherent')
@section('title', 'Notifications')

@section('content')
<div class="space-y-4">
    <h2 class="text-lg font-bold text-gray-900">Notifications</h2>

    <div class="space-y-2">
        @forelse($notifications ?? [] as $notif)
        <div class="bg-white rounded-2xl border {{ $notif->lu ? 'border-gray-100' : 'border-bm-200 bg-bm-50/30' }} p-4">
            <div class="flex items-start justify-between">
                <div class="flex items-start space-x-3">
                    <div class="w-8 h-8 rounded-full {{ $notif->lu ? 'bg-gray-100' : 'bg-bm-100' }} flex items-center justify-center flex-shrink-0 mt-0.5">
                        <i class="fas fa-bell {{ $notif->lu ? 'text-gray-400' : 'text-bm-600' }} text-xs"></i>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-900">{{ $notif->titre }}</p>
                        <p class="text-xs text-gray-600 mt-0.5">{{ $notif->message }}</p>
                        <p class="text-[10px] text-gray-400 mt-1">{{ $notif->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                @if(!$notif->lu)
                <form action="{{ route('busmetro.adherent.notifications.lire', $notif) }}" method="POST">@csrf
                    <button class="text-[10px] text-bm-600 hover:text-bm-700 font-medium">Marquer lu</button>
                </form>
                @endif
            </div>
        </div>
        @empty
        <div class="bg-white rounded-2xl border border-gray-100 p-8 text-center">
            <i class="fas fa-bell-slash text-gray-200 text-3xl mb-3"></i>
            <p class="text-sm text-gray-400">Aucune notification</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
