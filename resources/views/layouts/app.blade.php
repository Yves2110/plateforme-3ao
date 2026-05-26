<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/jpeg" href="{{ asset(config('brand.logo')) }}">

    <x-seo
        :title="$title ?? config('app.name', 'Plateforme 3AO')"
        :description="$description ?? __('site.seo_description')"
        :image="$ogImage ?? null"
        :type="$ogType ?? 'website'"
    />

    <!-- RSS auto-discovery -->
    <link rel="alternate" type="application/rss+xml" title="Actualités 3AO" href="{{ route('rss.actualites') }}">
    <link rel="alternate" type="application/rss+xml" title="Bibliothèque 3AO" href="{{ route('rss.ressources') }}">
    <link rel="alternate" type="application/rss+xml" title="Événements 3AO"   href="{{ route('rss.evenements') }}">

    <!-- Fonts: Poppins (display) + Inter (body) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
</head>
<body class="font-sans antialiased bg-white text-3ao-text">

    <x-banner />

    <!-- Navigation -->
    @include('components.navbar')

    @stack('before-livewire')

    <!-- Page Content -->
    <main>
        {{ $slot }}
    </main>

    <!-- Footer -->
    @include('components.footer')

    <x-tutorial />
    <x-cookie-banner />

    @stack('modals')
    @livewireScripts
    @stack('scripts')

</body>
</html>
