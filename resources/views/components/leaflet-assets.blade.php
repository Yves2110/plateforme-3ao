@once
    @push('styles')
        <link rel="stylesheet" href="{{ asset('vendor/leaflet/leaflet.css') }}">
        <link rel="stylesheet" href="{{ asset('vendor/leaflet/MarkerCluster.css') }}">
        <link rel="stylesheet" href="{{ asset('vendor/leaflet/MarkerCluster.Default.css') }}">
    @endpush
    @push('before-livewire')
        <script src="{{ asset('vendor/leaflet/leaflet.js') }}"></script>
        <script src="{{ asset('vendor/leaflet/leaflet.markercluster.js') }}"></script>
        <script src="{{ asset('js/carte-interactive.js') }}?v=3"></script>
    @endpush
@endonce
