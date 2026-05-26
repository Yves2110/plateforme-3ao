@extends('admin.layouts.admin')

@section('title', 'Acteurs')
@section('page-title', 'Acteurs')
@section('page-subtitle', $acteurs->total() . ' acteurs')

@section('header-actions')
    <a href="{{ route('admin.acteurs.create') }}" class="flex items-center gap-2 px-4 py-2 bg-[#2D6A4F] text-white text-sm font-semibold rounded-xl hover:bg-[#40916C] transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Nouvel acteur
    </a>
@endsection

@section('content')
<div class="py-6">
    <form method="GET" class="flex flex-wrap gap-3 mb-6">
        <input type="search" name="search" value="{{ request('search') }}" placeholder="Nom…"
               class="flex-1 max-w-sm px-4 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788]">
        <select name="validated" class="px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none bg-white">
            <option value="">Tous</option>
            <option value="1" {{ request('validated') === '1' ? 'selected' : '' }}>Validés</option>
            <option value="0" {{ request('validated') === '0' ? 'selected' : '' }}>En attente</option>
        </select>
        <button class="px-4 py-2 bg-[#2D6A4F] text-white text-sm font-medium rounded-xl">Filtrer</button>
    </form>

    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-5 py-3 font-semibold text-gray-600">Nom</th>
                    <th class="text-left px-5 py-3 font-semibold text-gray-600">Type</th>
                    <th class="text-left px-5 py-3 font-semibold text-gray-600">Pays</th>
                    <th class="text-left px-5 py-3 font-semibold text-gray-600">Statut</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($acteurs as $a)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-3 font-medium text-gray-900">{{ $a->name }}</td>
                        <td class="px-5 py-3 text-gray-600 text-xs">{{ $a->type }}</td>
                        <td class="px-5 py-3 text-gray-600 text-xs">{{ $a->country }}</td>
                        <td class="px-5 py-3">
                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ $a->is_validated ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                                {{ $a->is_validated ? 'Validé' : 'En attente' }}
                            </span>
                        </td>
                        <td class="px-5 py-3">
                            @include('admin._partials.table-actions', [
                                'editRoute'   => route('admin.acteurs.edit', $a),
                                'deleteRoute' => route('admin.acteurs.destroy', $a),
                                'deleteName'  => $a->name,
                            ])
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-10 text-center text-gray-400">Aucun acteur.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($acteurs->hasPages()) <div class="mt-6">{{ $acteurs->links() }}</div> @endif
</div>
@endsection
