@extends('admin.layouts.admin')

@section('title', 'Formations')
@section('page-title', 'Hub Formation')
@section('page-subtitle', 'Gérez les ateliers, cours, webinaires et certifications')

@section('header-actions')
    <a href="{{ route('admin.formations.create') }}" class="flex items-center gap-2 px-4 py-2 bg-[#2D6A4F] text-white rounded-xl hover:bg-[#40916C] transition-colors text-sm font-medium">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Nouvelle formation
    </a>
@endsection

@section('content')
<div class="py-6 space-y-6">

    {{-- Filtres et stats --}}
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.formations.index') }}"
               class="px-4 py-2 rounded-xl text-sm font-medium transition-colors {{ !request()->has('validated') ? 'bg-[#2D6A4F] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Toutes ({{ $counts['all'] }})
            </a>
            <a href="{{ route('admin.formations.index', ['validated' => 1]) }}"
               class="px-4 py-2 rounded-xl text-sm font-medium transition-colors {{ request('validated') === '1' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Validées ({{ $counts['validated'] }})
            </a>
            <a href="{{ route('admin.formations.index', ['validated' => 0]) }}"
               class="px-4 py-2 rounded-xl text-sm font-medium transition-colors {{ request('validated') === '0' ? 'bg-amber-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                En attente ({{ $counts['pending'] }})
            </a>
        </div>

        <form method="GET" action="{{ route('admin.formations.index') }}" class="flex items-center gap-2">
            @if(request('validated'))
                <input type="hidden" name="validated" value="{{ request('validated') }}">
            @endif
            <select name="type" class="px-3 py-2 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#52B788]">
                <option value="">Tous les types</option>
                @foreach($types as $type)
                    <option value="{{ $type }}" {{ request('type') === $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                @endforeach
            </select>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Rechercher..."
                   class="px-3 py-2 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#52B788]">
            <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-colors text-sm">
                Filtrer
            </button>
            @if(request()->hasAny(['q', 'type']))
                <a href="{{ route('admin.formations.index', request()->only('validated')) }}" class="px-3 py-2 text-gray-500 hover:text-gray-700 text-sm">
                    Réinitialiser
                </a>
            @endif
        </form>
    </div>

    {{-- Liste des formations --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Formation</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Dates</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Prix</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Statut</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($formations as $formation)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    @if($formation->thumbnail)
                                        <img src="{{ asset('storage/'.$formation->thumbnail) }}" class="w-12 h-12 rounded-lg object-cover" alt="">
                                    @else
                                        <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-[#F8F5F0] to-[#d4e8dc] flex items-center justify-center text-lg">
                                            🎓
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $formation->title }}</p>
                                        <p class="text-xs text-gray-500">{{ $formation->organizer ?? 'Organisateur non spécifié' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                    @php $typeColor = match($formation->type) {
                                        'atelier' => 'bg-orange-100 text-orange-700',
                                        'cours' => 'bg-blue-100 text-blue-700',
                                        'webinaire' => 'bg-purple-100 text-purple-700',
                                        'certification' => 'bg-green-100 text-green-700',
                                        default => 'bg-gray-100 text-gray-700'
                                    }; @endphp">
                                    {{ ucfirst($formation->type) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                @if($formation->start_date)
                                    {{ $formation->start_date->format('d/m/Y') }}
                                    @if($formation->end_date && $formation->end_date != $formation->start_date)
                                        → {{ $formation->end_date->format('d/m/Y') }}
                                    @endif
                                @else
                                    <span class="text-gray-400">À définir</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                @if($formation->price)
                                    {{ number_format($formation->price, 0, ',', ' ') }} FCFA
                                @else
                                    <span class="text-green-600 font-medium">Gratuit</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($formation->is_validated)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Validée
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-700">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        En attente
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    <form method="POST" action="{{ route('admin.formations.toggle-validation', $formation) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="p-2 text-gray-500 hover:text-{{ $formation->is_validated ? 'amber' : 'green' }}-600 hover:bg-{{ $formation->is_validated ? 'amber' : 'green' }}-50 rounded-lg transition-colors" title="{{ $formation->is_validated ? 'Dévalider' : 'Valider' }}">
                                            @if($formation->is_validated)
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            @else
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            @endif
                                        </button>
                                    </form>
                                    <a href="{{ route('admin.formations.edit', $formation) }}" class="p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Modifier">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form method="POST" action="{{ route('admin.formations.destroy', $formation) }}" class="inline" onsubmit="return confirm('Supprimer cette formation ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Supprimer">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-gray-500">
                                <div class="text-4xl mb-2">📭</div>
                                <p>Aucune formation trouvée</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="flex justify-center">
        {{ $formations->links() }}
    </div>

</div>
@endsection
