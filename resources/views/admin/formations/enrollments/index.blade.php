@extends('admin.layouts.admin')

@section('title', 'Inscriptions — ' . $formation->title)
@section('page-title', 'Inscriptions')
@section('page-subtitle', $formation->title)

@section('content')
<div class="py-6 space-y-6 max-w-5xl mx-auto">

    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.formations.edit', $formation) }}" class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div class="flex flex-wrap gap-2 text-sm">
                <span class="px-3 py-1 rounded-full bg-amber-100 text-amber-800">{{ $counts['pending'] }} en attente</span>
                <span class="px-3 py-1 rounded-full bg-green-100 text-green-800">{{ $counts['active'] }} actives</span>
                <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-800">{{ $counts['completed'] }} terminées</span>
            </div>
        </div>
        <a href="{{ route('admin.formations.enrollments.export', $formation) }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-[#2D6A4F] text-white text-sm font-semibold rounded-xl hover:bg-[#40916C] transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Exporter Excel
        </a>
    </div>

    @if(session('success'))
        <div class="p-4 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Apprenant</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Statut</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Inscrit le</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($enrollments as $enrollment)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-800">{{ $enrollment->user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $enrollment->user->email }}</p>
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $statusBadge = match($enrollment->status) {
                                    'pending' => 'bg-amber-100 text-amber-800',
                                    'active' => 'bg-green-100 text-green-800',
                                    'completed' => 'bg-blue-100 text-blue-800',
                                    'cancelled' => 'bg-gray-100 text-gray-600',
                                    default => 'bg-gray-100 text-gray-600',
                                };
                            @endphp
                            <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium {{ $statusBadge }}">{{ $enrollment->statusLabel() }}</span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">
                            {{ $enrollment->enrolled_at?->format('d/m/Y H:i') ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-right">
                            @if($enrollment->isPending())
                                <form method="POST" action="{{ route('admin.formations.enrollments.activate', [$formation, $enrollment]) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 text-xs font-medium bg-green-600 text-white rounded-lg hover:bg-green-700">
                                        Activer l'accès
                                    </button>
                                </form>
                            @endif
                            @if(! $enrollment->isCompleted() && $enrollment->status !== 'cancelled')
                                <form method="POST" action="{{ route('admin.formations.enrollments.cancel', [$formation, $enrollment]) }}" class="inline ml-1" onsubmit="return confirm('Annuler cette inscription ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 rounded-lg">
                                        Annuler
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-12 text-center text-gray-500 text-sm">Aucune inscription pour cette formation.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $enrollments->links() }}
</div>
@endsection
