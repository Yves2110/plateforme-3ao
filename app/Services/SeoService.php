<?php

namespace App\Services;

use Illuminate\Support\Str;

class SeoService
{
    private string $title        = '';
    private string $description  = '';
    private string $image        = '';
    private string $canonicalUrl = '';
    private string $type         = 'website';

    public function set(
        string  $title,
        string  $description  = '',
        ?string $image        = null,
        ?string $canonicalUrl = null,
        string  $type         = 'website',
    ): static {
        $siteName = config('app.name', 'Plateforme 3AO');

        $this->title        = $title . ' · ' . $siteName;
        $this->description  = \Str::limit(strip_tags($description), 155);
        $this->image        = $image  ?? asset(config('brand.logo', 'images/logo-3ao.jpeg'));
        $this->canonicalUrl = $canonicalUrl ?? url()->current();
        $this->type         = $type;

        return $this;
    }

    public function title(): string        { return $this->title; }
    public function description(): string  { return $this->description; }
    public function image(): string        { return $this->image; }
    public function canonical(): string    { return $this->canonicalUrl; }
    public function type(): string         { return $this->type; }
}
