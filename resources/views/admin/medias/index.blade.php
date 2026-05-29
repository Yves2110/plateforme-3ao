@extends('admin.layouts.admin')

@section('title', 'Médias')
@section('page-title', 'Médiathèque')
@section('page-subtitle', $medias->total() . ' contenus')

@section('header-actions')
    <a href="{{ route('admin.medias.create') }}" class="flex items-center gap-2 px-4 py-2 bg-[#2D6A4F] text-white text-sm font-semibold rounded-xl hover:bg-[#40916C] transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Nouveau média
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
                    <th class="text-left px-5 py-3 font-semibold text-gray-600">Images</th>
                    <th class="text-left px-5 py-3 font-semibold text-gray-600">Source</th>
                    <th class="text-left px-5 py-3 font-semibold text-gray-600">Vues</th>
                    <th class="text-left px-5 py-3 font-semibold text-gray-600">Statut</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($medias as $m)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-3 font-medium text-gray-900 max-w-xs truncate">{{ $m->title }}</td>
                        <td class="px-5 py-3">
                            @php $colors = ['video'=>'bg-red-100 text-red-700','podcast'=>'bg-purple-100 text-purple-700','gallery'=>'bg-blue-100 text-blue-700','photo'=>'bg-green-100 text-green-700']; @endphp
                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ $colors[$m->type] ?? 'bg-gray-100 text-gray-600' }}">{{ ucfirst($m->type) }}</span>
                        </td>
                        <td class="px-5 py-3 text-gray-600 text-xs">
                            @if($m->type === 'gallery')
                                {{ $m->photos_count }} photo(s)
                            @elseif($m->file_path)
                                1 fichier
                            @else
                                 
                            @endif
                        </td>
                        <td class="px-5 py-3 text-gray-500 text-xs truncate max-w-[120px]">{{ $m->source ?? ' ' }}</td>
                        <td class="px-5 py-3 text-gray-600">{{ $m->views }}</td>
                        <td class="px-5 py-3">
                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ $m->is_published ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $m->is_published ? 'Publié' : 'Brouillon' }}
                            </span>
                        </td>
                        <td class="px-5 py-3">
                            @include('admin._partials.table-actions', [
                                'editRoute'   => route('admin.medias.edit', $m),
                                'deleteRoute' => route('admin.medias.destroy', $m),
                                'deleteName'  => $m->title,
                            ])
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-5 py-10 text-center text-gray-400">Aucun média.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($medias->hasPages()) <div class="mt-6">{{ $medias->links() }}</div> @endif
</div>
@endsection
