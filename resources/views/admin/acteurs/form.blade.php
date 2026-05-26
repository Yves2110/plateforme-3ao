@extends('admin.layouts.admin')

@section('title', $action === 'create' ? 'Nouvel acteur' : 'Éditer l\'acteur')
@section('page-title', $action === 'create' ? 'Nouvel acteur' : 'Éditer : ' . $acteur->name)

@section('content')
<div class="py-6 max-w-3xl">
    <div class="bg-white rounded-2xl border border-gray-100 p-6">
        @if($errors->any())
            <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700 space-y-1">
                @foreach($errors->all() as $err)<p>• {{ $err }}</p>@endforeach
            </div>
        @endif

        <form action="{{ $action === 'create' ? route('admin.acteurs.store') : route('admin.acteurs.update', $acteur) }}"
              method="POST" class="space-y-5" enctype="multipart/form-data">
            @csrf
            @if($action === 'edit') @method('PUT') @endif

            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Nom <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $acteur->name) }}" required
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Type <span class="text-red-500">*</span></label>
                    <select name="type" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788] bg-white">
                        @foreach(['ONG','Organisation paysanne','Coopérative','Institution publique','Entreprise','Réseau','Université','Fondation'] as $t)
                            <option value="{{ $t }}" {{ old('type', $acteur->type) === $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Région</label>
                    <input type="text" name="region" value="{{ old('region', $acteur->region) }}"
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $acteur->email) }}"
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Téléphone</label>
                    <input type="tel" name="phone" value="{{ old('phone', $acteur->phone) }}"
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Site web</label>
                    <input type="url" name="website" value="{{ old('website', $acteur->website) }}"
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788]">
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Logo (PNG/JPG, max 2 Mo)</label>
                    <input type="file" name="logo" accept="image/*"
                           class="w-full text-sm text-gray-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-[#2D6A4F] file:text-white file:text-xs file:font-semibold hover:file:bg-[#40916C]">
                    @if($acteur->logo)
                        <p class="text-xs text-gray-500 mt-1">Logo actuel : {{ basename($acteur->logo) }}</p>
                    @endif
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Description</label>
                <textarea name="description" rows="4" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788] resize-y">{{ old('description', $acteur->description) }}</textarea>
            </div>

            {{-- ===== Bloc géocodage ===== --}}
            <div class="border-t border-gray-100 pt-5">
                <h3 class="text-sm font-display font-bold text-[#2D6A4F] mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Localisation géographique
                </h3>
                <x-geocoder-map
                    :address="old('address', $acteur->address ?? '')"
                    :city="old('city', $acteur->city ?? '')"
                    :country="old('country', $acteur->country ?? '')"
                    :lat="old('lat', $acteur->lat)"
                    :lng="old('lng', $acteur->lng)"
                    id="actor-geocoder-map"
                />
            </div>

            @can('gerer-carte')
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="hidden" name="is_validated" value="0">
                <input type="checkbox" name="is_validated" value="1"
                       {{ old('is_validated', $acteur->is_validated) ? 'checked' : '' }}
                       class="w-4 h-4 text-[#2D6A4F] rounded">
                <span class="text-sm font-medium text-gray-700">Validé (affiché sur la carte publique)</span>
            </label>
            @else
            <p class="text-sm text-amber-700 bg-amber-50 border border-amber-100 rounded-xl px-4 py-3">
                Votre fiche sera visible sur la carte après validation par un modérateur.
            </p>
            @endcan

            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-5 py-2 bg-[#2D6A4F] text-white text-sm font-semibold rounded-xl hover:bg-[#40916C] transition-colors">
                    {{ $action === 'create' ? 'Créer l\'acteur' : 'Enregistrer' }}
                </button>
                <a href="{{ route('admin.acteurs.index') }}" class="px-5 py-2 bg-gray-100 text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-200 transition-colors">Annuler</a>
            </div>
        </form>
    </div>
</div>
@endsection
