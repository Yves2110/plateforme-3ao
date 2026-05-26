@props(['editRoute', 'deleteRoute', 'deleteName' => 'cet élément'])
<div class="flex items-center gap-2">
    <a href="{{ $editRoute }}"
       class="px-3 py-1.5 text-xs font-medium bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">
        Éditer
    </a>
    <form action="{{ $deleteRoute }}" method="POST"
          onsubmit="return confirm('Supprimer {{ $deleteName }} ?')">
        @csrf @method('DELETE')
        <button class="px-3 py-1.5 text-xs font-medium bg-red-50 hover:bg-red-100 text-red-600 rounded-lg transition-colors">
            Supprimer
        </button>
    </form>
</div>
