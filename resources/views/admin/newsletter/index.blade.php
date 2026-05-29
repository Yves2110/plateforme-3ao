@extends('admin.layouts.admin')

@section('title', 'Newsletter')
@section('page-title', 'Newsletter')

@section('content')
<div class="py-6 space-y-6 max-w-6xl">

    <x-admin-back-link
        :href="route('admin.dashboard')"
        label="Retour au tableau de bord"
    />

    @if(!empty($autoSentCount) && $autoSentCount > 0)
        <div class="rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ $autoSentCount }} campagne(s) programmée(s) dont l'heure est passée viennent d'être envoyée(s).
        </div>
    @endif

    @if($mailDriverIsLog)
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 text-sm text-amber-800">
            <strong>Mode log actif</strong>   configurez SMTP dans <code class="bg-amber-100 px-1 rounded">.env</code> pour un envoi réel.
        </div>
    @else
        <div class="rounded-2xl border border-green-200 bg-green-50 p-4 flex flex-wrap items-center justify-between gap-3">
            <p class="text-sm text-green-800">
                Expéditeur affiché : <strong>{{ config('mail.from.name') }}</strong>
                <span class="text-green-600">({{ config('mail.from.address') }} en arrière-plan technique)</span>
            </p>
            <form action="{{ route('admin.newsletter.test-email') }}" method="POST" class="flex flex-wrap gap-2 items-center">
                @csrf
                <input type="email" name="email" value="{{ old('email') }}" required
                       placeholder="votre@email.com"
                       class="px-3 py-2 text-sm border border-green-200 rounded-xl min-w-[260px]">
                <button type="submit" class="px-4 py-2 bg-[#2D6A4F] text-white text-sm font-semibold rounded-xl hover:bg-[#40916C]">
                    E-mail test
                </button>
            </form>
        </div>
    @endif

    <div class="flex flex-wrap gap-2 border-b border-gray-200 pb-1">
        <a href="{{ route('admin.newsletter.index', ['tab' => 'subscribers']) }}"
           class="px-4 py-2 text-sm font-semibold rounded-t-lg {{ $tab === 'subscribers' ? 'bg-white border border-b-0 border-gray-200 text-[#2D6A4F]' : 'text-gray-500 hover:text-gray-800' }}">
            Abonnés ({{ $stats['active'] }} actifs)
        </a>
        <a href="{{ route('admin.newsletter.index', ['tab' => 'campaigns']) }}"
           class="px-4 py-2 text-sm font-semibold rounded-t-lg {{ $tab === 'campaigns' ? 'bg-white border border-b-0 border-gray-200 text-[#2D6A4F]' : 'text-gray-500 hover:text-gray-800' }}">
            Campagnes
        </a>
    </div>

    @if($tab === 'subscribers')
        <div class="grid sm:grid-cols-3 gap-4">
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <p class="text-xs text-gray-500 uppercase tracking-wide">Total</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['total'] }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <p class="text-xs text-gray-500 uppercase tracking-wide">Actifs</p>
                <p class="text-2xl font-bold text-[#2D6A4F] mt-1">{{ $stats['active'] }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <p class="text-xs text-gray-500 uppercase tracking-wide">Désinscrits</p>
                <p class="text-2xl font-bold text-gray-400 mt-1">{{ $stats['inactive'] }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
                <form method="GET" class="flex flex-wrap gap-2 flex-1">
                    <input type="hidden" name="tab" value="subscribers">
                    <input type="search" name="q" value="{{ request('q') }}" placeholder="Rechercher un e-mail…"
                           class="px-3 py-2 text-sm border border-gray-200 rounded-xl flex-1 min-w-[200px]">
                    <select name="status" class="px-3 py-2 text-sm border border-gray-200 rounded-xl">
                        <option value="">Tous</option>
                        <option value="active" @selected(request('status') === 'active')>Actifs</option>
                        <option value="inactive" @selected(request('status') === 'inactive')>Inactifs</option>
                    </select>
                    <button type="submit" class="px-4 py-2 bg-gray-100 text-sm font-semibold rounded-xl hover:bg-gray-200">Filtrer</button>
                </form>
                <a href="{{ route('admin.newsletter.subscribers.export') }}"
                   class="px-4 py-2 bg-[#2D6A4F] text-white text-sm font-semibold rounded-xl hover:bg-[#40916C] whitespace-nowrap">
                    Exporter Excel (CSV)
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs text-gray-500 border-b border-gray-100">
                            <th class="pb-3 pr-4">E-mail</th>
                            <th class="pb-3 pr-4">Statut</th>
                            <th class="pb-3 pr-4">Inscription</th>
                            <th class="pb-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($subscribers as $subscriber)
                            <tr>
                                <td class="py-3 pr-4">
                                    <p class="font-medium text-gray-800">{{ $subscriber->email }}</p>
                                    @if($subscriber->name)
                                        <p class="text-xs text-gray-400">{{ $subscriber->name }}</p>
                                    @endif
                                </td>
                                <td class="py-3 pr-4">
                                    @if($subscriber->is_active)
                                        <span class="text-xs px-2 py-1 rounded-full bg-green-50 text-green-700">Actif</span>
                                    @else
                                        <span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-500">Désinscrit</span>
                                    @endif
                                </td>
                                <td class="py-3 pr-4 text-gray-500">{{ $subscriber->subscribed_at?->format('d/m/Y H:i') }}</td>
                                <td class="py-3">
                                    <form action="{{ route('admin.newsletter.subscribers.destroy', $subscriber) }}" method="POST"
                                          onsubmit="return confirm('Supprimer cet abonné ?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs text-red-600 hover:underline">Supprimer</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-8 text-center text-gray-500">Aucun abonné pour le moment.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $subscribers->links() }}</div>
        </div>
    @else
        <div class="flex justify-end">
            <a href="{{ route('admin.newsletter.campaigns.create') }}"
               class="px-4 py-2 bg-[#2D6A4F] text-white text-sm font-semibold rounded-xl hover:bg-[#40916C]">
                + Nouvelle campagne
            </a>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
            <div class="divide-y divide-gray-50">
                @forelse($campaigns as $campaign)
                    <div class="px-6 py-4 flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <p class="font-medium text-gray-800">{{ $campaign->subject }}</p>
                            <p class="text-xs text-gray-500 mt-1">
                                <span @class([
                                    'font-semibold',
                                    'text-green-700' => $campaign->status === \App\Models\NewsletterCampaign::STATUS_SENT,
                                    'text-amber-600' => $campaign->status === \App\Models\NewsletterCampaign::STATUS_SENDING,
                                    'text-red-600' => $campaign->status === \App\Models\NewsletterCampaign::STATUS_FAILED,
                                ])>{{ $campaign->statusLabel() }}</span>
                                @if($summary = $campaign->sendSummary())
                                    · {{ $summary }}
                                @endif
                                @if($campaign->scheduled_at && $campaign->status === \App\Models\NewsletterCampaign::STATUS_SCHEDULED)
                                    · Programmée le {{ $campaign->scheduled_at->format('d/m/Y H:i') }}
                                @endif
                                @if($campaign->sent_at)
                                    · {{ $campaign->sent_at->format('d/m/Y H:i') }}
                                @endif
                                @if($campaign->last_error && $campaign->status === \App\Models\NewsletterCampaign::STATUS_FAILED)
                                    · {{ \Illuminate\Support\Str::limit($campaign->last_error, 80) }}
                                @endif
                            </p>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.newsletter.campaigns.show', $campaign) }}"
                               class="text-xs px-3 py-1.5 rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200">Aperçu</a>
                            @if($campaign->isEditable())
                                <a href="{{ route('admin.newsletter.campaigns.edit', $campaign) }}"
                                   class="text-xs px-3 py-1.5 rounded-lg bg-[#2D6A4F]/10 text-[#2D6A4F]">Modifier</a>
                            @endif
                            @if(in_array($campaign->status, [\App\Models\NewsletterCampaign::STATUS_SENDING, \App\Models\NewsletterCampaign::STATUS_FAILED]))
                                <form action="{{ route('admin.newsletter.campaigns.retry', $campaign) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-xs px-3 py-1.5 rounded-lg bg-amber-50 text-amber-800 hover:bg-amber-100">
                                        Relancer
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="px-6 py-10 text-sm text-gray-500 text-center">Aucune campagne créée.</p>
                @endforelse
            </div>
            <div class="px-6 py-3 border-t border-gray-50">{{ $campaigns->links() }}</div>
        </div>

        <p class="text-xs text-gray-500">
            Les campagnes programmées partent chaque minute si le planificateur tourne
            (<code class="bg-gray-100 px-1 rounded">php artisan schedule:work</code>).
            L'envoi s'exécute immédiatement (sans worker queue obligatoire).
        </p>
    @endif
</div>
@endsection
