@extends('admin.layouts.admin')

@section('title', $action === 'create' ? 'Nouvel événement' : 'Éditer l\'événement')
@section('page-title', $action === 'create' ? 'Nouvel événement' : 'Éditer : ' . $evenement->title)

@section('content')
<div class="py-6 max-w-3xl">
    <div class="bg-white rounded-2xl border border-gray-100 p-6">
        @if($errors->any())
            <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700 space-y-1">
                @foreach($errors->all() as $err)<p>• {{ $err }}</p>@endforeach
            </div>
        @endif

        <form action="{{ $action === 'create' ? route('admin.evenements.store') : route('admin.evenements.update', $evenement) }}"
              method="POST" class="space-y-4">
            @csrf
            @if($action === 'edit') @method('PUT') @endif

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Titre <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title', $evenement->title) }}" required
                       class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788]">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Type <span class="text-red-500">*</span></label>
                    <select name="type" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788] bg-white">
                        @foreach(['Conférence','Atelier','Webinaire','Forum','Formation','Séminaire','Foire','Visite de terrain'] as $t)
                            <option value="{{ $t }}" {{ old('type', $evenement->type) === $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Lieu</label>
                    <input type="text" name="location" value="{{ old('location', $evenement->location) }}"
                           placeholder="Ville, Pays"
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Date début <span class="text-red-500">*</span></label>
                    <input type="date" name="start_date" value="{{ old('start_date', $evenement->start_date?->toDateString()) }}" required
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Date fin</label>
                    <input type="date" name="end_date" value="{{ old('end_date', $evenement->end_date?->toDateString()) }}"
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Capacité max</label>
                    <input type="number" name="capacity" value="{{ old('capacity', $evenement->capacity) }}" min="1"
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788]">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Description</label>
                <textarea name="description" rows="5" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788] resize-y">{{ old('description', $evenement->description) }}</textarea>
            </div>

            <div class="flex flex-wrap gap-4">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="hidden" name="is_online" value="0">
                    <input type="checkbox" name="is_online" value="1" {{ old('is_online', $evenement->is_online) ? 'checked' : '' }} class="w-4 h-4 text-[#2D6A4F] rounded">
                    <span class="text-sm font-medium text-gray-700">Événement en ligne</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="hidden" name="is_validated" value="0">
                    <input type="checkbox" name="is_validated" value="1" {{ old('is_validated', $evenement->is_validated) ? 'checked' : '' }} class="w-4 h-4 text-[#2D6A4F] rounded">
                    <span class="text-sm font-medium text-gray-700">Validé (visible publiquement)</span>
                </label>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-5 py-2 bg-[#2D6A4F] text-white text-sm font-semibold rounded-xl hover:bg-[#40916C] transition-colors">
                    {{ $action === 'create' ? 'Créer l\'événement' : 'Enregistrer' }}
                </button>
                <a href="{{ route('admin.evenements.index') }}" class="px-5 py-2 bg-gray-100 text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-200 transition-colors">Annuler</a>
            </div>
        </form>
    </div>
</div>
@endsection
