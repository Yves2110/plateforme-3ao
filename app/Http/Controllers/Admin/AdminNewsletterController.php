<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Actualite;
use App\Models\Event;
use App\Models\NewsletterCampaign;
use App\Models\NewsletterCampaignItem;
use App\Models\NewsletterSubscriber;
use App\Services\NewsletterCampaignSender;
use App\Services\NewsletterContentBuilder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminNewsletterController extends Controller
{
    protected function authorizeNewsletter(): void
    {
        abort_unless(auth()->user()?->can('gerer-newsletter'), 403);
    }

    public function index(Request $request, NewsletterCampaignSender $sender)
    {
        $this->authorizeNewsletter();

        $autoSentCount = 0;
        if (config('newsletter.process_on_admin_visit', true)) {
            $autoSentCount = $sender->processDueScheduled();
        }

        $tab = $request->query('tab', 'subscribers');

        $subscribers = NewsletterSubscriber::query()
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%' . $request->string('q') . '%';
                $q->where(function ($inner) use ($term) {
                    $inner->where('email', 'like', $term)
                        ->orWhere('name', 'like', $term);
                });
            })
            ->when($request->query('status') === 'active', fn ($q) => $q->where('is_active', true))
            ->when($request->query('status') === 'inactive', fn ($q) => $q->where('is_active', false))
            ->latest('subscribed_at')
            ->paginate(25, ['*'], 'subscribers_page')
            ->withQueryString();

        $campaigns = NewsletterCampaign::with('author')
            ->latest()
            ->paginate(15, ['*'], 'campaigns_page')
            ->withQueryString();

        $stats = [
            'total'    => NewsletterSubscriber::count(),
            'active'   => NewsletterSubscriber::active()->count(),
            'inactive' => NewsletterSubscriber::where('is_active', false)->count(),
        ];

        $mailDriverIsLog = config('mail.default') === 'log';

        return view('admin.newsletter.index', compact(
            'subscribers', 'campaigns', 'stats', 'tab', 'mailDriverIsLog', 'autoSentCount'
        ));
    }

    public function sendTestEmail(Request $request)
    {
        $this->authorizeNewsletter();

        $data = $request->validate([
            'email' => 'required|email',
        ]);

        if (config('mail.default') === 'log') {
            return back()->with('error',
                'MAIL_MAILER=log : aucun e-mail réel n\'est envoyé. Passez en smtp dans le fichier .env (voir l\'aide ci-dessus).'
            );
        }

        try {
            \Illuminate\Support\Facades\Mail::raw(
                "Ceci est un e-mail de test depuis la plateforme 3AO.\n\nSi vous recevez ce message, la configuration SMTP fonctionne.",
                function ($message) use ($data) {
                    $message->to($data['email'])
                        ->subject('Test — Plateforme agroécologique — ' . now()->format('d/m/Y H:i'));
                }
            );
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Échec d\'envoi : ' . $e->getMessage());
        }

        return back()->with('success', 'E-mail de test envoyé à ' . $data['email'] . '. Vérifiez votre boîte (et les spams).');
    }

    public function exportSubscribers(): StreamedResponse
    {
        $this->authorizeNewsletter();

        $filename = 'newsletter-abonnes-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($handle, [
                'ID', 'Email', 'Nom', 'Actif', 'Source', 'Inscription', 'Désinscription', 'IP',
            ], ';');

            NewsletterSubscriber::orderBy('id')->chunk(200, function ($rows) use ($handle) {
                foreach ($rows as $row) {
                    fputcsv($handle, [
                        $row->id,
                        $row->email,
                        $row->name ?? '',
                        $row->is_active ? 'oui' : 'non',
                        $row->source,
                        $row->subscribed_at?->format('Y-m-d H:i:s') ?? '',
                        $row->unsubscribed_at?->format('Y-m-d H:i:s') ?? '',
                        $row->ip ?? '',
                    ], ';');
                }
            });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function destroySubscriber(NewsletterSubscriber $subscriber)
    {
        $this->authorizeNewsletter();
        $subscriber->delete();

        return back()->with('success', 'Abonné supprimé.');
    }

    public function createCampaign()
    {
        $this->authorizeNewsletter();

        return view('admin.newsletter.campaign-form', [
            'campaign'   => new NewsletterCampaign,
            'actualites' => $this->selectableActualites(),
            'events'     => $this->selectableEvents(),
            'selected'   => ['actualite' => [], 'event' => []],
        ]);
    }

    public function storeCampaign(Request $request, NewsletterCampaignSender $sender)
    {
        $this->authorizeNewsletter();

        $data = $this->validateCampaign($request);
        $campaign = NewsletterCampaign::create([
            'subject'      => $data['subject'],
            'intro_html'   => $data['intro_html'] ?? null,
            'status'       => $this->resolveStatus($data),
            'scheduled_at' => $data['scheduled_at'] ?? null,
            'user_id'      => auth()->id(),
        ]);

        $this->syncItems($campaign, $request);

        if ($request->boolean('send_now')) {
            return $this->redirectAfterDispatch($sender, $campaign->fresh(), 'admin.newsletter.index', ['tab' => 'campaigns']);
        }

        if ($campaign->scheduled_at && $campaign->scheduled_at->lte(now())) {
            return $this->redirectAfterDispatch($sender, $campaign->fresh(), 'admin.newsletter.campaigns.show', $campaign);
        }

        $message = $campaign->scheduled_at
            ? 'Campagne programmée pour le ' . $campaign->scheduled_at->format('d/m/Y à H:i') . '. Elle partira automatiquement à cette heure (ou à l\'ouverture de cette page admin).'
            : 'Campagne enregistrée en brouillon.';

        return redirect()->route('admin.newsletter.campaigns.show', $campaign)->with('success', $message);
    }

    public function editCampaign(NewsletterCampaign $campaign)
    {
        $this->authorizeNewsletter();
        abort_unless($campaign->isEditable(), 403, 'Campagne non modifiable.');

        $campaign->load('items');

        $selected = [
            'actualite' => $campaign->items->where('item_type', 'actualite')->pluck('item_id')->all(),
            'event'     => $campaign->items->where('item_type', 'event')->pluck('item_id')->all(),
        ];

        return view('admin.newsletter.campaign-form', [
            'campaign'   => $campaign,
            'actualites' => $this->selectableActualites(),
            'events'     => $this->selectableEvents(),
            'selected'   => $selected,
        ]);
    }

    public function updateCampaign(Request $request, NewsletterCampaign $campaign, NewsletterCampaignSender $sender)
    {
        $this->authorizeNewsletter();
        abort_unless($campaign->isEditable(), 403, 'Campagne non modifiable.');

        $data = $this->validateCampaign($request);

        $campaign->update([
            'subject'      => $data['subject'],
            'intro_html'   => $data['intro_html'] ?? null,
            'status'       => $this->resolveStatus($data),
            'scheduled_at' => $data['scheduled_at'] ?? null,
        ]);

        $campaign->items()->delete();
        $this->syncItems($campaign, $request);

        if ($request->boolean('send_now')) {
            return $this->redirectAfterDispatch($sender, $campaign->fresh(), 'admin.newsletter.campaigns.show', $campaign);
        }

        if ($campaign->scheduled_at && $campaign->scheduled_at->lte(now())) {
            return $this->redirectAfterDispatch($sender, $campaign->fresh(), 'admin.newsletter.campaigns.show', $campaign);
        }

        return redirect()->route('admin.newsletter.campaigns.show', $campaign)
            ->with('success', 'Campagne mise à jour.');
    }

    public function previewCampaign(NewsletterCampaign $campaign)
    {
        $this->authorizeNewsletter();

        return response($this->renderCampaignEmailHtml($campaign))
            ->header('Content-Type', 'text/html; charset=UTF-8');
    }

    public function showCampaign(NewsletterCampaign $campaign, NewsletterContentBuilder $builder)
    {
        $this->authorizeNewsletter();
        $campaign->load(['items', 'author']);

        return view('admin.newsletter.campaign-show', compact('campaign'));
    }

    protected function renderCampaignEmailHtml(NewsletterCampaign $campaign): string
    {
        $campaign->loadMissing('items');

        return view('emails.newsletter-campaign', [
            'campaign'       => $campaign,
            'unsubscribeUrl' => url('/newsletter/desinscription/preview'),
            'siteUrl'        => url('/'),
        ])->render();
    }

    public function destroyCampaign(NewsletterCampaign $campaign)
    {
        $this->authorizeNewsletter();
        abort_unless($campaign->isEditable(), 403, 'Campagne non modifiable.');

        $campaign->delete();

        return redirect()->route('admin.newsletter.index', ['tab' => 'campaigns'])
            ->with('success', 'Campagne supprimée.');
    }

    public function sendCampaign(NewsletterCampaign $campaign, NewsletterCampaignSender $sender)
    {
        $this->authorizeNewsletter();
        abort_unless($campaign->isEditable(), 403, 'Campagne non modifiable.');

        return $this->runCampaignSend($campaign, $sender);
    }

    public function retryCampaign(NewsletterCampaign $campaign, NewsletterCampaignSender $sender)
    {
        $this->authorizeNewsletter();

        if (! in_array($campaign->status, [
            NewsletterCampaign::STATUS_SENDING,
            NewsletterCampaign::STATUS_FAILED,
        ], true)) {
            return back()->with('error', 'Seules les campagnes bloquées ou en échec peuvent être relancées.');
        }

        $campaign->update([
            'status'     => NewsletterCampaign::STATUS_SCHEDULED,
            'sent_at'    => null,
            'last_error' => null,
        ]);

        return $this->runCampaignSend($campaign->fresh(), $sender, 'Campagne relancée.');
    }

    protected function redirectAfterDispatch(
        NewsletterCampaignSender $sender,
        NewsletterCampaign $campaign,
        string $routeName,
        mixed $routeParameters = [],
    ) {
        try {
            $sender->dispatch($campaign);
        } catch (\InvalidArgumentException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        $campaign->refresh();
        $summary = $campaign->sendSummary();
        $message = 'Envoi terminé.' . ($summary ? " {$summary}" : '');

        return redirect()->route($routeName, $routeParameters)->with('success', $message);
    }

    protected function runCampaignSend(NewsletterCampaign $campaign, NewsletterCampaignSender $sender, ?string $successMessage = null)
    {
        try {
            $sender->dispatch($campaign);
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        $campaign->refresh();
        $summary = $campaign->sendSummary();

        $flash = ($successMessage ?? 'Envoi terminé.') . ($summary ? " {$summary}" : '');

        if (config('mail.default') === 'log') {
            return back()
                ->with('warning', 'MAIL_MAILER=log : les e-mails sont écrits dans storage/logs/laravel.log uniquement, pas envoyés à Gmail. Configurez SMTP dans .env.')
                ->with('success', $flash);
        }

        return back()->with('success', $flash);
    }

    public function cancelCampaign(NewsletterCampaign $campaign)
    {
        $this->authorizeNewsletter();

        if ($campaign->status !== NewsletterCampaign::STATUS_SCHEDULED) {
            return back()->with('error', 'Seules les campagnes programmées peuvent être annulées.');
        }

        $campaign->update([
            'status'       => NewsletterCampaign::STATUS_CANCELLED,
            'scheduled_at' => null,
        ]);

        return back()->with('success', 'Campagne annulée.');
    }

    protected function validateCampaign(Request $request): array
    {
        return $request->validate([
            'subject'        => 'required|string|max:255',
            'intro_html'     => 'nullable|string|max:5000',
            'scheduled_at'   => [
                'nullable',
                'date',
                Rule::when(! $request->boolean('send_now'), 'after:now'),
            ],
            'send_now'       => 'nullable|boolean',
            'schedule'       => 'nullable|boolean',
            'actualite_ids'  => 'nullable|array',
            'actualite_ids.*'=> 'integer|exists:actualites,id',
            'event_ids'      => 'nullable|array',
            'event_ids.*'    => 'integer|exists:events,id',
        ]);
    }

    protected function resolveStatus(array $data): string
    {
        if (! empty($data['send_now'])) {
            return NewsletterCampaign::STATUS_DRAFT;
        }

        if (! empty($data['scheduled_at'])) {
            return NewsletterCampaign::STATUS_SCHEDULED;
        }

        return NewsletterCampaign::STATUS_DRAFT;
    }

    protected function syncItems(NewsletterCampaign $campaign, Request $request): void
    {
        $order = 0;

        foreach ($request->input('actualite_ids', []) as $id) {
            NewsletterCampaignItem::create([
                'newsletter_campaign_id' => $campaign->id,
                'item_type'              => 'actualite',
                'item_id'                => (int) $id,
                'sort_order'             => $order++,
            ]);
        }

        foreach ($request->input('event_ids', []) as $id) {
            NewsletterCampaignItem::create([
                'newsletter_campaign_id' => $campaign->id,
                'item_type'              => 'event',
                'item_id'                => (int) $id,
                'sort_order'             => $order++,
            ]);
        }
    }

    protected function selectableActualites()
    {
        return Actualite::query()
            ->where('is_published', true)
            ->latest('published_at')
            ->limit(50)
            ->get(['id', 'title', 'published_at']);
    }

    protected function selectableEvents()
    {
        return Event::query()
            ->where('is_validated', true)
            ->where('start_date', '>=', now()->subMonth())
            ->orderBy('start_date')
            ->limit(50)
            ->get(['id', 'title', 'start_date', 'location']);
    }
}
