@extends('admin.layouts.admin')

@section('title', 'Campagne newsletter')
@section('page-title', $campaign->subject)

@section('content')
<div class="py-6 space-y-6 max-w-5xl">

    <x-admin-back-link
        :href="route('admin.newsletter.index', ['tab' => 'campaigns'])"
        label="Retour aux campagnes"
        class="mb-2"
    />

    <div class="flex flex-wrap items-center gap-2 text-sm">
        <span @class([
            'px-3 py-1 rounded-full font-semibold',
            'bg-green-100 text-green-800' => $campaign->status === \App\Models\NewsletterCampaign::STATUS_SENT,
            'bg-amber-100 text-amber-800' => $campaign->status === \App\Models\NewsletterCampaign::STATUS_SENDING,
            'bg-red-100 text-red-800' => $campaign->status === \App\Models\NewsletterCampaign::STATUS_FAILED,
            'bg-gray-100 text-gray-700' => !in_array($campaign->status, [\App\Models\NewsletterCampaign::STATUS_SENT, \App\Models\NewsletterCampaign::STATUS_SENDING, \App\Models\NewsletterCampaign::STATUS_FAILED]),
        ])>{{ $campaign->statusLabel() }}</span>
        @if($summary = $campaign->sendSummary())
            <span class="text-gray-600">{{ $summary }}</span>
        @endif
        @if($campaign->author)
            <span class="text-gray-500">par {{ $campaign->author->name }}</span>
        @endif
    </div>

    <div class="flex flex-wrap gap-2">
        @if($campaign->isEditable())
            <a href="{{ route('admin.newsletter.campaigns.edit', $campaign) }}"
               class="px-4 py-2 bg-white border border-gray-200 text-sm font-semibold rounded-xl hover:border-[#2D6A4F]">
                Modifier
            </a>
            <form action="{{ route('admin.newsletter.campaigns.send', $campaign) }}" method="POST"
                  onsubmit="return confirm('Envoyer à tous les abonnés actifs ?')">
                @csrf
                <button type="submit" class="px-4 py-2 bg-[#2D6A4F] text-white text-sm font-semibold rounded-xl hover:bg-[#40916C]">
                    Envoyer maintenant
                </button>
            </form>
            @if($campaign->status === \App\Models\NewsletterCampaign::STATUS_SCHEDULED)
                <form action="{{ route('admin.newsletter.campaigns.cancel', $campaign) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-red-50 text-red-600 text-sm font-semibold rounded-xl">
                        Annuler la programmation
                    </button>
                </form>
            @endif
            <form action="{{ route('admin.newsletter.campaigns.destroy', $campaign) }}" method="POST"
                  onsubmit="return confirm('Supprimer cette campagne ?')">
                @csrf @method('DELETE')
                <button type="submit" class="px-4 py-2 text-sm text-red-600 hover:underline">Supprimer</button>
            </form>
        @endif
        @if(in_array($campaign->status, [\App\Models\NewsletterCampaign::STATUS_SENDING, \App\Models\NewsletterCampaign::STATUS_FAILED]))
            <form action="{{ route('admin.newsletter.campaigns.retry', $campaign) }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2 bg-amber-500 text-white text-sm font-semibold rounded-xl hover:bg-amber-600">
                    Relancer l'envoi
                </button>
            </form>
        @endif
    </div>

    @if($campaign->scheduled_at && $campaign->status === \App\Models\NewsletterCampaign::STATUS_SCHEDULED)
        <p class="text-sm text-[#2D6A4F] bg-[#2D6A4F]/5 rounded-xl px-4 py-3">
            Envoi programmé le <strong>{{ $campaign->scheduled_at->format('d/m/Y à H:i') }}</strong>.
        </p>
    @endif

    @if($campaign->sent_at)
        <p class="text-sm text-gray-600">
            Envoyée le {{ $campaign->sent_at->format('d/m/Y H:i') }}
            @if($campaign->sendSummary())
                — {{ $campaign->sendSummary() }}
            @endif
        </p>
    @endif
    @if($campaign->last_error && $campaign->status === \App\Models\NewsletterCampaign::STATUS_FAILED)
        <p class="text-sm text-red-700 bg-red-50 rounded-xl px-4 py-3">{{ $campaign->last_error }}</p>
    @endif

    {{-- Aperçu visuel type boîte mail --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
        <div class="px-5 py-4 border-b border-gray-100 flex flex-wrap items-center justify-between gap-3 bg-gray-50/80">
            <div>
                <h2 class="font-display font-semibold text-gray-800">Aperçu du message</h2>
                <p class="text-xs text-gray-500 mt-0.5">Rendu tel que le destinataire le verra dans sa messagerie</p>
            </div>
            <a href="{{ route('admin.newsletter.campaigns.preview', $campaign) }}"
               target="_blank"
               rel="noopener"
               class="text-xs font-semibold text-[#2D6A4F] hover:underline">
                Ouvrir en plein écran ↗
            </a>
        </div>

        <div class="bg-[#e8ece9] p-6 sm:p-8 flex justify-center">
            <div class="w-full max-w-[640px] rounded-xl overflow-hidden shadow-xl ring-1 ring-black/5 bg-white">
                <div class="bg-[#1A1A2E] px-4 py-2 flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-red-400"></span>
                    <span class="w-3 h-3 rounded-full bg-amber-400"></span>
                    <span class="w-3 h-3 rounded-full bg-green-400"></span>
                    <span class="ml-2 text-[10px] text-white/50 truncate flex-1">De : Plateforme agroécologique</span>
                </div>
                <iframe
                    src="{{ route('admin.newsletter.campaigns.preview', $campaign) }}"
                    title="Aperçu newsletter"
                    class="w-full border-0 block bg-white"
                    style="height: min(75vh, 820px); min-height: 480px;"
                    loading="lazy"
                ></iframe>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 p-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-2">Contenu inclus dans cette campagne</h3>
        <ul class="text-sm text-gray-600 space-y-1">
            @forelse($campaign->items as $item)
                @php $model = $item->resolveModel(); @endphp
                <li class="flex items-start gap-2">
                    <span class="text-[#2D6A4F]">•</span>
                    @if($model)
                        <span>{{ $item->item_type === 'actualite' ? 'Actualité' : 'Événement' }} : <strong>{{ $model->title }}</strong></span>
                    @else
                        <span class="text-red-500">Contenu indisponible</span>
                    @endif
                </li>
            @empty
                <li>Introduction uniquement (aucun article ni événement sélectionné).</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection
