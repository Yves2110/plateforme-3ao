<x-app-layout>
    <x-slot name="title">{{ $actualite->title }}</x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700">{{ session('success') }}</div>
        @endif

        <x-public-manage-bar
            label="Actualités"
            :permissions="['publier-actualites', 'administrer-utilisateurs']"
            :create-route="route('admin.actualites.create')"
            :list-route="route('admin.actualites.index')"
            :item="$actualite"
            :edit-route="route('admin.actualites.edit', $actualite)"
            :toggle-route="route('contenu.actualites.toggle', $actualite)"
            published-key="is_published"
        />

        <div class="mb-4 flex flex-wrap gap-2 items-center">
            <x-actualite-category-badge :actualite="$actualite" />
            <x-syndicated-notice :actualite="$actualite" />
            <span class="text-xs text-gray-400">{{ $actualite->published_at?->translatedFormat('d F Y') }}</span>
        </div>
        <h1 class="font-display text-2xl sm:text-3xl font-bold text-[#1A1A2E] leading-tight mb-6">
            {{ $actualite->title }}
        </h1>
        @if($actualite->thumbnail)
            <img src="{{ asset('storage/'.$actualite->thumbnail) }}" alt="{{ $actualite->title }}"
                 class="w-full h-64 object-cover rounded-2xl mb-8">
        @endif
        @if($actualite->isSyndicated() && $actualite->source_url)
            <a href="{{ $actualite->source_url }}" target="_blank" rel="noopener noreferrer"
               class="mb-6 inline-flex items-center gap-2 px-4 py-2.5 bg-amber-50 border border-amber-200 text-amber-900 text-sm font-semibold rounded-xl hover:bg-amber-100 transition-colors">
                {{ __('actualites.read_full_article', ['source' => $actualite->syndicated_source]) }}
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            </a>
        @endif

        <div class="prose prose-green max-w-none text-gray-700 leading-relaxed">
            {!! $actualite->renderedContent() !!}
        </div>

        <div class="mt-10 pt-6 border-t border-gray-100">
            <a href="{{ route('actualites.index') }}" class="text-sm text-[#2D6A4F] hover:underline font-medium">
                ← Retour aux actualités
            </a>
        </div>
    </div>
</x-app-layout>
