@extends('admin.layouts.admin')

@section('title', 'Ressources')
@section('page-title', 'Bibliothèque')
@section('page-subtitle', $ressources->total() . ' ressources')

@section('header-actions')
    <a href="{{ route('admin.ressources.create') }}" class="flex items-center gap-2 px-4 py-2 bg-[#2D6A4F] text-white text-sm font-semibold rounded-xl hover:bg-[#40916C] transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Nouvelle ressource
    </a>
@endsection

@section('content')
<div class="py-6">
    <form method="GET" class="flex gap-3 mb-6">
        <input type="search" name="search" value="{{ request('search') }}" placeholder="Titre…"
               class="flex-1 max-w-sm px-4 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788]">
        <button class="px-4 py-2 bg-[#2D6A4F] text-white text-sm font-medium rounded-xl">Rechercher</button>
    </form>

    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-5 py-3 font-semibold text-gray-600">Titre</th>
                    <th class="text-left px-5 py-3 font-semibold text-gray-600">Type</th>
                    <th class="text-left px-5 py-3 font-semibold text-gray-600">Langue</th>
                    <th class="text-left px-5 py-3 font-semibold text-gray-600">Thématique</th>
                    <th class="text-left px-5 py-3 font-semibold text-gray-600">Statut</th>
                    <th class="text-left px-5 py-3 font-semibold text-gray-600">Date</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($ressources as $r)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-3 font-medium text-gray-900 max-w-xs truncate">{{ $r->title }}</td>
                        <td class="px-5 py-3">
                            <span class="px-2 py-0.5 text-xs font-semibold bg-purple-100 text-purple-700 rounded-full uppercase">{{ $r->type }}</span>
                        </td>
                        <td class="px-5 py-3 text-gray-600 uppercase text-xs">{{ $r->language }}</td>
                        <td class="px-5 py-3 text-gray-600 text-xs max-w-[8rem] truncate">{{ $r->tags->first()?->name ?? '—' }}</td>
                        <td class="px-5 py-3">
                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ $r->is_validated ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                                {{ $r->is_validated ? 'Validé' : 'En attente' }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-gray-400 text-xs">{{ $r->created_at->translatedFormat('d M Y') }}</td>
                        <td class="px-5 py-3">
                            @include('admin._partials.table-actions', [
                                'editRoute'   => route('admin.ressources.edit', $r),
                                'deleteRoute' => route('admin.ressources.destroy', $r),
                                'deleteName'  => $r->title,
                            ])
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-5 py-10 text-center text-gray-400">Aucune ressource.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($ressources->hasPages()) <div class="mt-6">{{ $ressources->links() }}</div> @endif
</div>
@endsection
