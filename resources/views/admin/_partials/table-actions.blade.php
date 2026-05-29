@props([
    'editRoute' => null,
    'deleteRoute' => null,
    'deleteName' => 'cet élément',
    'toggleRoute' => null,
    'isActive' => true,
])
@if($editRoute || $toggleRoute || $deleteRoute)
<div class="flex items-center justify-end gap-1">
    @if($editRoute)
        <a href="{{ $editRoute }}"
           title="Éditer"
           class="p-1.5 rounded-lg text-gray-500 hover:text-[#2D6A4F] hover:bg-[#E8F5E9] transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            <span class="sr-only">Éditer</span>
        </a>
    @endif

    @if($toggleRoute)
        <form action="{{ $toggleRoute }}" method="POST" class="inline">
            @csrf
            <button type="submit"
                    title="{{ $isActive ? 'Désactiver le compte' : 'Réactiver le compte' }}"
                    class="p-1.5 rounded-lg transition-colors
                           {{ $isActive ? 'text-amber-600 hover:text-amber-700 hover:bg-amber-50' : 'text-green-600 hover:text-green-700 hover:bg-green-50' }}">
                @if($isActive)
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                    </svg>
                    <span class="sr-only">Désactiver</span>
                @else
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="sr-only">Réactiver</span>
                @endif
            </button>
        </form>
    @endif

    @if($deleteRoute)
        <form action="{{ $deleteRoute }}" method="POST" class="inline"
              onsubmit="return confirm('Supprimer {{ $deleteName }} ?')">
            @csrf @method('DELETE')
            <button type="submit"
                    title="Supprimer"
                    class="p-1.5 rounded-lg text-red-500 hover:text-red-700 hover:bg-red-50 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                <span class="sr-only">Supprimer</span>
            </button>
        </form>
    @endif
</div>
@endif
