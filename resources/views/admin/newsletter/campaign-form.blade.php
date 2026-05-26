@extends('admin.layouts.admin')

@section('title', $campaign->exists ? 'Modifier campagne' : 'Nouvelle campagne')
@section('page-title', $campaign->exists ? 'Modifier la campagne' : 'Nouvelle newsletter')

@section('content')
<div class="py-6 max-w-4xl space-y-4">
    <x-admin-back-link
        :href="$campaign->exists ? route('admin.newsletter.campaigns.show', $campaign) : route('admin.newsletter.index', ['tab' => 'campaigns'])"
        :label="$campaign->exists ? 'Retour à la campagne' : 'Retour aux campagnes'"
    />

    <form method="POST"
          action="{{ $campaign->exists ? route('admin.newsletter.campaigns.update', $campaign) : route('admin.newsletter.campaigns.store') }}"
          class="space-y-6">
        @csrf
        @if($campaign->exists) @method('PUT') @endif

        <div class="bg-white rounded-2xl border border-gray-100 p-6 space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Objet de l'e-mail *</label>
                <input type="text" name="subject" value="{{ old('subject', $campaign->subject) }}" required
                       class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:ring-[#52B788] focus:border-[#2D6A4F]">
                @error('subject')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Introduction (texte libre)</label>
                <textarea name="intro_html" rows="4" placeholder="Message d'accroche avant les articles sélectionnés…"
                          class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:ring-[#52B788] focus:border-[#2D6A4F]">{{ old('intro_html', $campaign->intro_html) }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Programmer l'envoi (optionnel)</label>
                <input type="datetime-local" name="scheduled_at"
                       value="{{ old('scheduled_at', $campaign->scheduled_at?->format('Y-m-d\TH:i')) }}"
                       class="px-3 py-2 text-sm border border-gray-200 rounded-xl">
                <p class="text-xs text-gray-500 mt-1">Laissez vide pour enregistrer en brouillon ou envoyer immédiatement.</p>
                @error('scheduled_at')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <h2 class="font-display font-semibold text-gray-800 mb-3">Actualités à inclure</h2>
            <div class="max-h-48 overflow-y-auto space-y-2 border border-gray-100 rounded-xl p-3">
                @forelse($actualites as $actualite)
                    <label class="flex items-start gap-2 text-sm cursor-pointer">
                        <input type="checkbox" name="actualite_ids[]" value="{{ $actualite->id }}"
                               @checked(in_array($actualite->id, old('actualite_ids', $selected['actualite'] ?? [])))
                               class="mt-1 rounded border-gray-300 text-[#2D6A4F]">
                        <span>
                            <span class="font-medium text-gray-800">{{ $actualite->title }}</span>
                            <span class="text-gray-400 text-xs block">{{ $actualite->published_at?->format('d/m/Y') }}</span>
                        </span>
                    </label>
                @empty
                    <p class="text-sm text-gray-500">Aucune actualité publiée disponible.</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <h2 class="font-display font-semibold text-gray-800 mb-3">Événements à inclure</h2>
            <div class="max-h-48 overflow-y-auto space-y-2 border border-gray-100 rounded-xl p-3">
                @forelse($events as $event)
                    <label class="flex items-start gap-2 text-sm cursor-pointer">
                        <input type="checkbox" name="event_ids[]" value="{{ $event->id }}"
                               @checked(in_array($event->id, old('event_ids', $selected['event'] ?? [])))
                               class="mt-1 rounded border-gray-300 text-[#2D6A4F]">
                        <span>
                            <span class="font-medium text-gray-800">{{ $event->title }}</span>
                            <span class="text-gray-400 text-xs block">{{ $event->start_date?->format('d/m/Y') }} · {{ $event->location }}</span>
                        </span>
                    </label>
                @empty
                    <p class="text-sm text-gray-500">Aucun événement validé récent.</p>
                @endforelse
            </div>
        </div>

        <div class="flex flex-wrap gap-3">
            <button type="submit" name="schedule" value="1"
                    class="px-5 py-2.5 bg-white border border-[#2D6A4F] text-[#2D6A4F] text-sm font-semibold rounded-xl hover:bg-[#2D6A4F]/5">
                Enregistrer
            </button>
            <button type="submit" name="send_now" value="1"
                    class="px-5 py-2.5 bg-[#D4A017] text-white text-sm font-semibold rounded-xl hover:bg-[#F4C842] hover:text-gray-900"
                    onclick="return confirm('Envoyer maintenant à tous les abonnés actifs ?')">
                Envoyer maintenant
            </button>
            <a href="{{ route('admin.newsletter.index', ['tab' => 'campaigns']) }}"
               class="px-5 py-2.5 text-sm text-gray-500 hover:text-gray-800">Annuler</a>
        </div>
    </form>
</div>
@endsection
