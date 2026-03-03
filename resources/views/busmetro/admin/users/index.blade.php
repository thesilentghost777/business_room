@extends('busmetro.layouts.admin')
@section('title', 'Staff')
@section('page-title', 'Gestion du staff')

@section('content')
<div class="space-y-4">
    <div class="flex justify-between items-center">
        <p class="text-sm text-gray-500">{{ $users->count() ?? 0 }} utilisateurs</p>
        <a href="{{ route('busmetro.admin.users.create') }}" class="px-4 py-2 bg-bm-600 text-white rounded-xl text-sm font-medium hover:bg-bm-700"><i class="fas fa-plus mr-1.5"></i>Nouveau</a>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <table class="w-full">
            <thead><tr class="border-b border-gray-100 bg-gray-50/50">
                <th class="text-left text-[10px] font-semibold text-gray-400 uppercase px-4 py-3">Utilisateur</th>
                <th class="text-left text-[10px] font-semibold text-gray-400 uppercase px-4 py-3">Rôle</th>
                <th class="text-left text-[10px] font-semibold text-gray-400 uppercase px-4 py-3">Zone</th>
                <th class="text-center text-[10px] font-semibold text-gray-400 uppercase px-4 py-3">Statut</th>
                <th class="text-right text-[10px] font-semibold text-gray-400 uppercase px-4 py-3">Actions</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($users ?? [] as $user)
                <tr class="hover:bg-gray-50/50">
                    <td class="px-4 py-3">
                        <p class="text-xs font-medium text-gray-900">{{ $user->prenom }} {{ $user->nom }}</p>
                        <p class="text-[10px] text-gray-400">{{ $user->email }}</p>
                    </td>
                    <td class="px-4 py-3"><span class="text-xs px-2 py-0.5 rounded-full font-medium {{ $user->role === 'admin' ? 'bg-bm-100 text-bm-700' : ($user->role === 'agent' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700') }}">{{ ucfirst($user->role) }}</span></td>
                    <td class="px-4 py-3 text-xs text-gray-600">{{ $user->zone_affectation ?? '—' }}</td>
                    <td class="px-4 py-3 text-center">
                        <form action="{{ route('busmetro.admin.users.toggle', $user) }}" method="POST" class="inline">@csrf
                            <button type="submit" class="w-8 h-4 rounded-full {{ $user->is_active ? 'bg-bm-500' : 'bg-gray-300' }} relative transition-colors">
                                <span class="absolute top-0.5 {{ $user->is_active ? 'right-0.5' : 'left-0.5' }} w-3 h-3 bg-white rounded-full shadow transition-all"></span>
                            </button>
                        </form>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('busmetro.admin.users.edit', $user) }}" class="p-1.5 text-gray-400 hover:text-blue-600"><i class="fas fa-edit text-xs"></i></a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="py-12 text-center text-sm text-gray-400">Aucun utilisateur</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
