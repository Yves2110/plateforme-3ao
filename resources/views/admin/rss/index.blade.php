@extends('admin.layouts.admin')

@section('title', 'Flux RSS entrants')
@section('page-title', 'Flux RSS partenaires')

@section('content')
<div class="py-6 space-y-8 max-w-6xl">

    {{-- Ajouter une source --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-6">
        <h2 class="font-display font-semibold text-gray-800 mb-4">Ajouter une source</h2>
        <form action="{{ route('admin.rss.sources.store') }}" method="POST" class="grid sm:grid-cols-3 gap-3">
            @csrf
            <input type="text" name="name" placeholder="Nom (ex: FAO Agroécologie)" required
                   class="px-3 py-2 text-sm border border-gray-200 rounded-xl focus:ring-[#52B788] focus:border-[#2D6A4F]">
            <input type="url" name="url" placeholder="https://example.org/feed.xml" required
                   class="px-3 py-2 text-sm border border-gray-200 rounded-xl focus:ring-[#52B788] focus:border-[#2D6A4F] sm:col-span-2">
            <button type="submit" class="px-4 py-2 bg-[#2D6A4F] text-white text-sm font-semibold rounded-xl hover:bg-[#40916C]">Ajouter</button>
        </form>
        <form action="{{ route('admin.rss.fetch') }}" method="POST" class="mt-4">
            @csrf
            <button type="submit" class="text-sm text-[#2D6A4F] font-semibold hover:underline">↻ Importer maintenant tous les flux actifs</button>
        </form>
    </div>

    {{-- Sources --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-50">
            <h2 class="font-display font-semibold text-gray-800">Sources configurées</h2>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($sources as $source)
                <div class="px-6 py-4 flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="font-medium text-gray-800">{{ $source->name }}</p>
                        <p class="text-xs text-gray-400 truncate max-w-md">{{ $source->url }}</p>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $source->pending_count }} en attente
                            @if($source->last_fetched_at)
                                · Dernière sync {{ $source->last_fetched_at->diffForHumans() }}
                            @endif
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <form action="{{ route('admin.rss.sources.toggle', $source) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit" class="text-xs px-3 py-1.5 rounded-lg {{ $source->is_active ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $source->is_active ? 'Actif' : 'Inactif' }}
                            </button>
                        </form>
                        <form action="{{ route('admin.rss.sources.destroy', $source) }}" method="POST" onsubmit="return confirm('Supprimer cette source ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs px-3 py-1.5 rounded-lg bg-red-50 text-red-600">Supprimer</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="px-6 py-8 text-sm text-gray-500 text-center">Aucune source RSS configurée.</p>
            @endforelse
        </div>
    </div>

    {{-- Modération --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-50">
            <h2 class="font-display font-semibold text-gray-800">Articles en attente de validation</h2>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($pendingItems as $item)
                <div class="px-6 py-4">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div class="min-w-0 flex-1">
                            <p class="font-medium text-gray-800">{{ $item->title }}</p>
                            <p class="text-xs text-gray-400">{{ $item->source->name }} · {{ $item->published_at?->format('d/m/Y') }}</p>
                            @if($item->description)
                                <p class="text-sm text-gray-600 mt-2 line-clamp-2">{{ $item->description }}</p>
                            @endif
                            <a href="{{ $item->link }}" target="_blank" class="text-xs text-[#2D6A4F] hover:underline mt-1 inline-block">Voir la source →</a>
                        </div>
                        <div class="flex gap-2 shrink-0">
                            <form action="{{ route('admin.rss.items.approve', $item) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 text-xs font-semibold bg-[#2D6A4F] text-white rounded-lg">Publier</button>
                            </form>
                            <form action="{{ route('admin.rss.items.reject', $item) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 text-xs font-semibold bg-gray-100 text-gray-600 rounded-lg">Rejeter</button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <p class="px-6 py-8 text-sm text-gray-500 text-center">Aucun article en attente.</p>
            @endforelse
        </div>
        @if($pendingItems->hasPages())
            <div class="px-6 py-4">{{ $pendingItems->links() }}</div>
        @endif
    </div>
</div>
@endsection
