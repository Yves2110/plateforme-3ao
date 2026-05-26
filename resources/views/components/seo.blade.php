@props([
    'title'       => config('app.name'),
    'description' => 'Plateforme collaborative pour l\'agroécologie en Afrique de l\'Ouest — Alliance 3AO',
    'image'       => null,
    'canonical'   => null,
    'type'        => 'website',
])
@php
    $siteName   = config('app.name', 'Plateforme 3AO');
    $fullTitle   = $title !== $siteName ? $title . ' — ' . $siteName : $siteName;
    $ogImage     = $image ?? asset(config('brand.logo', 'images/logo-3ao.jpeg'));
    $canonicalUrl = $canonical ?? url()->current();
    $desc        = Str::limit(strip_tags($description), 155);
@endphp

<title>{{ $fullTitle }}</title>
<meta name="description" content="{{ $desc }}">
<link rel="canonical" href="{{ $canonicalUrl }}">

{{-- Open Graph --}}
<meta property="og:type"        content="{{ $type }}">
<meta property="og:title"       content="{{ $fullTitle }}">
<meta property="og:description" content="{{ $desc }}">
<meta property="og:image"       content="{{ $ogImage }}">
<meta property="og:url"         content="{{ $canonicalUrl }}">
<meta property="og:site_name"   content="{{ $siteName }}">
<meta property="og:locale"      content="{{ str_replace('-', '_', app()->getLocale() === 'en' ? 'en_GB' : 'fr_FR') }}">

{{-- Twitter Card --}}
<meta name="twitter:card"        content="summary_large_image">
<meta name="twitter:title"       content="{{ $fullTitle }}">
<meta name="twitter:description" content="{{ $desc }}">
<meta name="twitter:image"       content="{{ $ogImage }}">

{{-- JSON-LD Structured Data --}}
@php
$jsonLd = json_encode([
    '@context' => 'https://schema.org',
    '@graph' => [
        [
            '@type' => 'Organization',
            '@id' => url('/') . '#organization',
            'name' => $siteName,
            'url' => url('/'),
            'logo' => [
                '@type' => 'ImageObject',
                'url' => asset('images/og-default.png')
            ],
            'description' => $desc,
            'sameAs' => [
                'https://twitter.com/3ao_org',
                'https://linkedin.com/company/alliance-3ao'
            ]
        ],
        [
            '@type' => 'WebSite',
            '@id' => url('/') . '#website',
            'url' => url('/'),
            'name' => $siteName,
            'publisher' => [
                '@id' => url('/') . '#organization'
            ],
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => [
                    '@type' => 'EntryPoint',
                    'urlTemplate' => route('search') . '?q={search_term_string}'
                ],
                'query-input' => 'required name=search_term_string'
            ]
        ],
        [
            '@type' => 'WebPage',
            '@id' => $canonicalUrl . '#webpage',
            'url' => $canonicalUrl,
            'name' => $fullTitle,
            'description' => $desc,
            'isPartOf' => [
                '@id' => url('/') . '#website'
            ],
            'about' => [
                '@id' => url('/') . '#organization'
            ]
        ]
    ]
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
@endphp
<script type="application/ld+json">
{!! $jsonLd !!}
</script>
