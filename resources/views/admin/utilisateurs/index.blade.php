@extends('admin.layouts.admin')

@section('title', 'Utilisateurs')
@section('page-title', 'Utilisateurs')
@section('page-subtitle', $users->total() . ' comptes enregistrés')

@section('content')
<div class="py-6">
    {{-- Barre de recherche --}}
    <form method="GET" class="flex gap-3 mb-6">
        <input type="search" name="search" value="{{ request('search') }}" placeholder="Nom ou email…"
               class="flex-1 max-w-sm px-4 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788]">
        <button class="px-4 py-2 bg-[#2D6A4F] text-white text-sm font-medium rounded-xl hover:bg-[#40916C] transition-colors">Rechercher</button>
    </form>

    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-5 py-3 font-semibold text-gray-600">Nom</th>
                    <th class="text-left px-5 py-3 font-semibold text-gray-600">Email</th>
                    <th class="text-left px-5 py-3 font-semibold text-gray-600">Rôle</th>
                    <th class="text-left px-5 py-3 font-semibold text-gray-600">Inscription</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($users as $user)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-[#2D6A4F] flex items-center justify-center text-white text-xs font-bold shrink-0">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                <span class="font-medium text-gray-900">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-gray-600">{{ $user->email }}</td>
                        <td class="px-5 py-3">
                            @foreach($user->roles as $role)
                                <span class="px-2 py-0.5 text-xs font-semibold bg-[#E8F5E9] text-[#2D6A4F] rounded-full">{{ $role->name }}</span>
                            @endforeach
                        </td>
                        <td class="px-5 py-3 text-gray-400 text-xs">{{ $user->created_at->translatedFormat('d M Y') }}</td>
                        <td class="px-5 py-3">
                            @include('admin._partials.table-actions', [
                                'editRoute'   => route('admin.utilisateurs.edit', $user),
                                'deleteRoute' => route('admin.utilisateurs.destroy', $user),
                                'deleteName'  => $user->name,
                            ])
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-10 text-center text-gray-400">Aucun utilisateur trouvé.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
        <div class="mt-6">{{ $users->links() }}</div>
    @endif
</div>
@endsection
