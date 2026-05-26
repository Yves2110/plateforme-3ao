<?php

namespace App\Services;

use App\Models\NewsletterCampaign;
use Illuminate\Mail\Message;
use Illuminate\Support\Str;

class NewsletterContentBuilder
{
    protected function brandColor(string $key, string $fallback): string
    {
        return config("brand.colors.{$key}", $fallback);
    }

    public function publicAssetUrl(string $relativePath, ?Message $message = null): string
    {
        $fullPath = public_path(ltrim($relativePath, '/'));

        if ($message && is_file($fullPath)) {
            return $message->embed($fullPath);
        }

        return url(asset($relativePath));
    }

    protected function imageUrl(?string $path, ?Message $message = null): ?string
    {
        if (! $path) {
            return null;
        }

        $storagePath = storage_path('app/public/' . ltrim($path, '/'));

        if ($message && is_file($storagePath)) {
            return $message->embed($storagePath);
        }

        return url(asset('storage/' . ltrim($path, '/')));
    }

    public function buildHtml(NewsletterCampaign $campaign, ?Message $message = null): string
    {
        $campaign->load('items');

        $sections = [];
        $green = $this->brandColor('green', '#2D6A4F');
        $orange = $this->brandColor('orange', '#E85D04');
        $gold = $this->brandColor('gold', '#F4C842');

        if ($campaign->intro_html) {
            $sections[] = '<div style="margin-bottom:24px;line-height:1.65;color:#444;font-size:15px;">'
                . nl2br(e($campaign->intro_html))
                . '</div>';
        }

        $actualites = [];
        $events = [];

        foreach ($campaign->items as $item) {
            $model = $item->resolveModel();
            if (! $model) {
                continue;
            }

            if ($item->item_type === 'actualite') {
                $actualites[] = $model;
            } elseif ($item->item_type === 'event') {
                $events[] = $model;
            }
        }

        if ($actualites !== []) {
            $sections[] = $this->sectionTitle('Actualités', $green, $gold);
            foreach ($actualites as $actualite) {
                $url = route('actualites.show', $actualite->slug);
                $excerpt = Str::limit(strip_tags($actualite->content ?? ''), 200);
                $sections[] = $this->articleBlock(
                    $actualite->title,
                    $excerpt,
                    $url,
                    'Lire l\'article',
                    $orange,
                    $this->imageUrl($actualite->thumbnail, $message),
                );
            }
        }

        if ($events !== []) {
            $sections[] = $this->sectionTitle('Événements à venir', $green, $gold);
            foreach ($events as $event) {
                $url = route('evenements.show', $event->slug);
                $date = $event->start_date?->translatedFormat('d F Y');
                $meta = trim(collect([$date, $event->location, $event->country])->filter()->implode(' · '));
                $excerpt = Str::limit(strip_tags($event->description ?? ''), 200);
                $sections[] = $this->articleBlock(
                    $event->title,
                    $meta . ($excerpt ? "\n\n" . $excerpt : ''),
                    $url,
                    'Voir l\'événement',
                    $orange,
                    $this->imageUrl($event->thumbnail, $message),
                );
            }
        }

        if ($sections === []) {
            $sections[] = '<p style="color:#666;font-size:14px;">Aucun contenu sélectionné pour cette campagne.</p>';
        }

        return implode("\n", $sections);
    }

    protected function sectionTitle(string $title, string $green, string $gold): string
    {
        return '<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin:28px 0 16px;">'
            . '<tr><td style="border-bottom:3px solid ' . e($gold) . ';padding-bottom:8px;">'
            . '<h2 style="margin:0;font-family:Georgia,\'Times New Roman\',serif;color:' . e($green) . ';font-size:20px;font-weight:700;">'
            . e($title)
            . '</h2></td></tr></table>';
    }

    protected function articleBlock(
        string $title,
        string $text,
        string $url,
        string $cta,
        string $orange,
        ?string $imageUrl = null,
    ): string {
        $body = nl2br(e($text));
        $radius = $imageUrl ? '12px 12px 0 0' : '12px 12px 0 0';

        $imageRow = '';
        if ($imageUrl) {
            $imageRow = '<tr><td style="padding:0;line-height:0;">'
                . '<a href="' . e($url) . '" style="text-decoration:none;display:block;">'
                . '<img src="' . e($imageUrl) . '" alt="' . e($title) . '" width="544"'
                . ' style="display:block;width:100%;max-width:100%;height:auto;max-height:220px;object-fit:cover;border-radius:' . $radius . ';">'
                . '</a></td></tr>';
        }

        return '<table role="presentation" width="100%" cellspacing="0" cellpadding="0"'
            . ' style="margin-bottom:20px;background:#ffffff;border:1px solid #dde5e1;border-radius:12px;overflow:hidden;box-shadow:0 2px 12px rgba(26,26,46,0.08);">'
            . $imageRow
            . '<tr><td style="padding:18px 20px 20px;">'
            . '<h3 style="margin:0 0 10px;font-size:17px;color:#1A1A2E;font-weight:700;line-height:1.35;">' . e($title) . '</h3>'
            . '<p style="margin:0 0 16px;font-size:14px;line-height:1.55;color:#555;">' . $body . '</p>'
            . '<a href="' . e($url) . '" style="display:inline-block;padding:10px 22px;background:' . e($orange) . ';color:#ffffff;text-decoration:none;border-radius:999px;font-size:13px;font-weight:700;">'
            . e($cta)
            . '</a></td></tr></table>';
    }
}
