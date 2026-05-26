@extends('admin.layouts.admin')

@section('title', 'Tableau de bord')
@section('page-title', 'Tableau de bord')
@section('page-subtitle', 'Vue d\'ensemble de la plateforme 3AO')

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
@endpush

@section('content')
<div class="py-6 space-y-8">

    {{-- Stats cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach([
            ['label' => 'Utilisateurs',  'value' => $stats['users'],      'color' => 'bg-blue-500',   'route' => 'admin.utilisateurs.index', 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197'],
            ['label' => 'Actualités',    'value' => $stats['actualites'], 'color' => 'bg-green-500',  'route' => 'admin.actualites.index',   'icon' => 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7'],
            ['label' => 'Ressources',    'value' => $stats['ressources'], 'color' => 'bg-purple-500', 'route' => 'admin.ressources.index',   'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13'],
            ['label' => 'Événements',    'value' => $stats['evenements'], 'color' => 'bg-orange-500', 'route' => 'admin.evenements.index',   'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5'],
            ['label' => 'Acteurs',       'value' => $stats['acteurs'],    'color' => 'bg-teal-500',   'route' => 'admin.acteurs.index',      'icon' => 'M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z'],
            ['label' => 'Médias',        'value' => $stats['medias'],     'color' => 'bg-pink-500',   'route' => 'admin.medias.index',       'icon' => 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764'],
            ['label' => 'Discussions',   'value' => $stats['threads'],    'color' => 'bg-indigo-500', 'route' => 'admin.forum.index',        'icon' => 'M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14'],
            ['label' => 'Réponses',      'value' => $stats['replies'],    'color' => 'bg-gray-500',   'route' => 'admin.forum.index',        'icon' => 'M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6'],
        ] as $stat)
            <a href="{{ route($stat['route']) }}"
               class="bg-white rounded-2xl border border-gray-100 p-5 hover:shadow-md transition-shadow group">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 {{ $stat['color'] }} rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $stat['icon'] }}"/>
                        </svg>
                    </div>
                    <svg class="w-4 h-4 text-gray-300 group-hover:text-gray-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </div>
                <p class="font-display text-2xl font-bold text-[#1A1A2E]">{{ number_format($stat['value']) }}</p>
                <p class="text-xs text-gray-500 mt-0.5">{{ $stat['label'] }}</p>
            </a>
        @endforeach
    </div>

    {{-- Stats modération --}}
    @if(array_sum($moderationStats) > 0)
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5">
        <h2 class="font-display font-semibold text-amber-800 mb-3 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            Modération en attente
        </h2>
        <div class="flex flex-wrap gap-3">
            @if($moderationStats['pending_actors'] > 0)
                <a href="{{ route('admin.acteurs.index') }}?validated=0" class="flex items-center gap-2 px-4 py-2 bg-white rounded-xl border border-amber-200 text-amber-700 hover:bg-amber-100 transition-colors">
                    <span class="font-bold">{{ $moderationStats['pending_actors'] }}</span>
                    <span class="text-sm">acteurs</span>
                </a>
            @endif
            @if($moderationStats['pending_events'] > 0)
                <a href="{{ route('admin.evenements.index') }}?validated=0" class="flex items-center gap-2 px-4 py-2 bg-white rounded-xl border border-amber-200 text-amber-700 hover:bg-amber-100 transition-colors">
                    <span class="font-bold">{{ $moderationStats['pending_events'] }}</span>
                    <span class="text-sm">événements</span>
                </a>
            @endif
            @if($moderationStats['pending_resources'] > 0)
                <a href="{{ route('admin.ressources.index') }}?validated=0" class="flex items-center gap-2 px-4 py-2 bg-white rounded-xl border border-amber-200 text-amber-700 hover:bg-amber-100 transition-colors">
                    <span class="font-bold">{{ $moderationStats['pending_resources'] }}</span>
                    <span class="text-sm">ressources</span>
                </a>
            @endif
            @if($moderationStats['pending_threads'] > 0)
                <a href="{{ route('admin.forum.index') }}?validated=0" class="flex items-center gap-2 px-4 py-2 bg-white rounded-xl border border-amber-200 text-amber-700 hover:bg-amber-100 transition-colors">
                    <span class="font-bold">{{ $moderationStats['pending_threads'] }}</span>
                    <span class="text-sm">discussions</span>
                </a>
            @endif
        </div>
    </div>
    @endif

    {{-- Graphique d'évolution --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-6">
        <h2 class="font-display font-semibold text-gray-800 mb-4">Évolution (30 derniers jours)</h2>
        <div class="h-64">
            <canvas id="growthChart"></canvas>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Activité récente --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-display font-semibold text-gray-800">Activité récente</h2>
                <span class="text-xs text-gray-400">Dernières 24h</span>
            </div>
            <div class="space-y-3">
                @forelse($recentActivity as $activity)
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full bg-{{ $activity['color'] }}-100 flex items-center justify-center shrink-0 mt-0.5">
                            @if($activity['icon'] === 'user')
                                <svg class="w-4 h-4 text-{{ $activity['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            @elseif($activity['icon'] === 'document')
                                <svg class="w-4 h-4 text-{{ $activity['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            @else
                                <svg class="w-4 h-4 text-{{ $activity['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            @if($activity['url'])
                                <a href="{{ $activity['url'] }}" target="_blank" class="text-sm font-medium text-gray-800 hover:text-[#2D6A4F] transition-colors line-clamp-1">{{ $activity['text'] }}</a>
                            @else
                                <p class="text-sm font-medium text-gray-800 line-clamp-1">{{ $activity['text'] }}</p>
                            @endif
                            <p class="text-xs text-gray-400">{{ $activity['time']->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 text-center py-6">Aucune activité récente</p>
                @endforelse
            </div>
        </div>

        {{-- Acteurs en attente de validation --}}
        <div class="lg:col-span-1 bg-white rounded-2xl border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-display font-semibold text-gray-800">Acteurs à valider</h2>
                <a href="{{ route('admin.acteurs.index') }}?validated=0" class="text-xs text-[#2D6A4F] hover:underline">Voir tout</a>
            </div>
            @forelse($pendingActors as $actor)
                <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                    <div>
                        <p class="text-sm font-medium text-gray-800">{{ $actor->name }}</p>
                        <p class="text-xs text-gray-400">{{ $actor->type }} · {{ $actor->country }}</p>
                    </div>
                    <a href="{{ route('admin.acteurs.edit', $actor) }}" class="text-xs text-[#2D6A4F] hover:underline font-medium">Éditer</a>
                </div>
            @empty
                <p class="text-sm text-gray-400 text-center py-6">Aucun acteur en attente ✓</p>
            @endforelse
        </div>

    </div>

    {{-- Accès rapides --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <a href="{{ route('admin.actualites.create') }}" class="flex items-center gap-2 p-3 bg-[#2D6A4F] text-white rounded-xl hover:bg-[#40916C] transition-colors text-sm font-medium">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nouvelle actualité
        </a>
        <a href="{{ route('admin.evenements.create') }}" class="flex items-center gap-2 p-3 bg-[#D4A017] text-white rounded-xl hover:bg-[#F4C842] transition-colors text-sm font-medium">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nouvel événement
        </a>
        <a href="{{ route('admin.ressources.create') }}" class="flex items-center gap-2 p-3 bg-purple-600 text-white rounded-xl hover:bg-purple-700 transition-colors text-sm font-medium">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nouvelle ressource
        </a>
        <a href="{{ route('admin.medias.create') }}" class="flex items-center gap-2 p-3 bg-pink-600 text-white rounded-xl hover:bg-pink-700 transition-colors text-sm font-medium">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nouveau média
        </a>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('growthChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($chartData['labels']),
            datasets: [
                {
                    label: 'Utilisateurs (cumul)',
                    data: @json($chartData['users']),
                    borderColor: '#2D6A4F',
                    backgroundColor: 'rgba(45, 106, 79, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 0,
                    pointHoverRadius: 4
                },
                {
                    label: 'Contenu créé',
                    data: @json($chartData['content']),
                    borderColor: '#D4A017',
                    backgroundColor: 'rgba(212, 160, 23, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 0,
                    pointHoverRadius: 4,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false
            },
            plugins: {
                legend: {
                    position: 'top',
                    align: 'end',
                    labels: {
                        usePointStyle: true,
                        boxWidth: 8
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: {
                        maxTicksLimit: 10,
                        font: { size: 11 }
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.05)' },
                    ticks: { font: { size: 11 } }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    grid: { display: false },
                    ticks: { font: { size: 11 } }
                }
            }
        }
    });
});
</script>
@endpush
@endsection
