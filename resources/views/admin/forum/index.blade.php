@extends('admin.layouts.admin')

@section('title', 'Modération Forum')
@section('page-title', 'Modération Forum')
@section('page-subtitle', $threads->total() . ' discussions')

@section('content')
<div class="py-6">
    <form method="GET" class="flex flex-wrap gap-3 mb-6">
        <input type="search" name="search" value="{{ request('search') }}" placeholder="Titre…"
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
                    <th class="text-left px-5 py-3 font-semibold text-gray-600">Titre</th>
                    <th class="text-left px-5 py-3 font-semibold text-gray-600">Catégorie</th>
                    <th class="text-left px-5 py-3 font-semibold text-gray-600">Auteur</th>
                    <th class="text-left px-5 py-3 font-semibold text-gray-600">Réponses</th>
                    <th class="text-left px-5 py-3 font-semibold text-gray-600">Statut</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($threads as $t)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-3 font-medium text-gray-900 max-w-xs truncate">
                            <a href="{{ route('communaute.thread', [$t->category, $t->slug]) }}" target="_blank"
                               class="hover:text-[#2D6A4F] hover:underline">
                                {{ $t->title }}
                            </a>
                        </td>
                        <td class="px-5 py-3 text-gray-600 text-xs">{{ $t->category }}</td>
                        <td class="px-5 py-3 text-gray-600 text-xs">{{ $t->author?->name ?? ' ' }}</td>
                        <td class="px-5 py-3 text-gray-600">{{ $t->replies->count() }}</td>
                        <td class="px-5 py-3">
                            <div class="flex flex-wrap gap-1">
                                @if($t->is_pinned) <span class="px-1.5 py-0.5 text-xs bg-amber-100 text-amber-700 rounded-full">📌</span> @endif
                                @if($t->is_locked) <span class="px-1.5 py-0.5 text-xs bg-gray-100 text-gray-600 rounded-full">🔒</span> @endif
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full
                                    {{ isset($t->is_validated) && $t->is_validated ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                                    {{ isset($t->is_validated) && $t->is_validated ? 'Validé' : 'En attente' }}
                                </span>
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                @if(!isset($t->is_validated) || !$t->is_validated)
                                    <form action="{{ route('admin.forum.threads.validate', $t) }}" method="POST">
                                        @csrf
                                        <button class="px-3 py-1.5 text-xs font-medium bg-green-50 hover:bg-green-100 text-green-700 rounded-lg transition-colors">
                                            Valider
                                        </button>
                                    </form>
                                @endif
                                <form action="{{ route('admin.forum.threads.destroy', $t) }}" method="POST"
                                      onsubmit="return confirm('Supprimer cette discussion ?')">
                                    @csrf @method('DELETE')
                                    <button class="px-3 py-1.5 text-xs font-medium bg-red-50 hover:bg-red-100 text-red-600 rounded-lg transition-colors">
                                        Supprimer
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-10 text-center text-gray-400">Aucune discussion.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($threads->hasPages()) <div class="mt-6">{{ $threads->links() }}</div> @endif
</div>
@endsection
