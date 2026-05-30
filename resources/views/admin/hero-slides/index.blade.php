@extends('admin.layouts.admin')

@section('title', 'Accueil · visuels')
@section('page-title', 'Page d\'accueil · visuels')
@section('page-subtitle', 'Slider hero et logos partenaires')

@section('header-actions')
    <a href="{{ route('admin.hero-slides.create') }}" class="flex items-center gap-2 px-4 py-2 bg-[#2D6A4F] text-white text-sm font-semibold rounded-xl hover:bg-[#40916C] transition-colors">
        + Ajouter une image
    </a>
@endsection

@section('content')
<div class="py-6">
    <p class="text-sm text-gray-500 mb-6 max-w-2xl">
        L’image avec l’ordre <strong>0</strong> est la slide principale : elle affiche le titre, le texte et les boutons.
        Les autres images défilent sans texte. La colonne « Actualités » à droite reste fixe sur le site.
    </p>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($slides as $slide)
            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
                <div class="relative aspect-[16/10] bg-gray-100">
                    <img src="{{ $slide->imageUrl() }}" alt="{{ $slide->alt_text }}" class="w-full h-full object-cover">
                    @if($slide->sort_order === 0)
                        <span class="absolute top-2 left-2 px-2 py-0.5 text-[10px] font-bold uppercase bg-[#F4C842] text-[#1A1A2E] rounded-full">Slide principale</span>
                    @endif
                    @if(! $slide->is_active)
                        <span class="absolute top-2 right-2 px-2 py-0.5 text-[10px] font-bold uppercase bg-gray-800/80 text-white rounded-full">Inactive</span>
                    @endif
                </div>
                <div class="p-4 space-y-2">
                    <p class="font-semibold text-sm text-gray-900 truncate">{{ $slide->title ?: 'Sans titre' }}</p>
                    <p class="text-xs text-gray-500">Ordre : {{ $slide->sort_order }}</p>
                    <div class="flex gap-2 pt-2">
                        <a href="{{ route('admin.hero-slides.edit', $slide) }}" class="flex-1 text-center px-3 py-1.5 text-xs font-semibold bg-[#F8F5F0] text-[#2D6A4F] rounded-lg hover:bg-green-50">Modifier</a>
                        <form method="POST" action="{{ route('admin.hero-slides.destroy', $slide) }}" class="flex-1" onsubmit="return confirm('Supprimer cette image ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="w-full px-3 py-1.5 text-xs font-semibold bg-red-50 text-red-600 rounded-lg hover:bg-red-100">Supprimer</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <p class="col-span-full text-center text-gray-400 py-12">Aucune image. Des images par défaut s’affichent sur l’accueil.</p>
        @endforelse
    </div>

    <hr class="my-12 border-gray-200">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-lg font-bold text-gray-900">Partenaires & soutiens institutionnels</h2>
            <p class="text-sm text-gray-500 mt-1 max-w-2xl">
                Logos affichés en slider sous la page d’accueil. Téléversez un logo par organisation (PNG transparent recommandé).
            </p>
        </div>
        <a href="{{ route('admin.home-partners.create') }}" class="flex items-center justify-center gap-2 px-4 py-2 bg-[#2D6A4F] text-white text-sm font-semibold rounded-xl hover:bg-[#40916C] transition-colors shrink-0">
            + Ajouter un partenaire
        </a>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
        @forelse($partners as $partner)
            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
                <div class="flex items-center justify-center h-28 bg-gray-50 p-4">
                    @if($partner->logoUrl())
                        <img src="{{ $partner->logoUrl() }}" alt="{{ $partner->name }}" class="max-h-16 max-w-full object-contain">
                    @else
                        <span class="font-display font-bold text-gray-300 text-sm text-center">{{ $partner->name }}</span>
                    @endif
                </div>
                <div class="p-3 space-y-1 border-t border-gray-50">
                    <p class="font-semibold text-xs text-gray-900 truncate">{{ $partner->name }}</p>
                    <p class="text-[10px] text-gray-400">Ordre {{ $partner->sort_order }}</p>
                    @if(! $partner->is_active)
                        <span class="inline-block px-1.5 py-0.5 text-[10px] font-bold uppercase bg-gray-200 text-gray-600 rounded">Inactif</span>
                    @endif
                    <div class="flex gap-1.5 pt-2">
                        <a href="{{ route('admin.home-partners.edit', $partner) }}" class="flex-1 text-center px-2 py-1 text-[10px] font-semibold bg-[#F8F5F0] text-[#2D6A4F] rounded-lg hover:bg-green-50">Modifier</a>
                        <form method="POST" action="{{ route('admin.home-partners.destroy', $partner) }}" class="flex-1" onsubmit="return confirm('Supprimer ce partenaire ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="w-full px-2 py-1 text-[10px] font-semibold bg-red-50 text-red-600 rounded-lg hover:bg-red-100">Suppr.</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <p class="col-span-full text-center text-gray-400 py-8">Aucun partenaire. Les noms par défaut s’affichent en texte sur l’accueil.</p>
        @endforelse
    </div>
</div>
@endsection
