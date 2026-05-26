@props([
    'addressName'   => 'address',
    'cityName'      => 'city',
    'countryName'   => 'country',
    'latName'       => 'lat',
    'lngName'       => 'lng',
    'address'       => '',
    'city'          => '',
    'country'       => '',
    'lat'           => null,
    'lng'           => null,
    'defaultLat'    => 12.6392,
    'defaultLng'    => -8.0029,
    'defaultZoom'   => 5,
    'mapHeight'     => '400px',
    'id'            => 'geocoder-map',
])

<x-leaflet-assets />

<div
    x-data="geocoderMap({
        id: '{{ $id }}',
        initLat: {{ $lat !== null && $lat !== '' ? $lat : 'null' }},
        initLng: {{ $lng !== null && $lng !== '' ? $lng : 'null' }},
        defaultLat: {{ $defaultLat }},
        defaultLng: {{ $defaultLng }},
        defaultZoom: {{ $defaultZoom }},
        initAddress: @js($address),
        initCity: @js($city),
        initCountry: @js($country),
    })"
    x-init="init()"
    class="space-y-3"
>
    <div class="flex gap-2 items-stretch">
        <div class="flex-1 relative">
            <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase tracking-wide">Adresse</label>
            <input type="text"
                   name="{{ $addressName }}"
                   x-model="address"
                   @keydown.enter.prevent="geocode()"
                   placeholder="Ex: Ouagadougou, Burkina Faso"
                   class="w-full rounded-lg border-gray-200 focus:border-[#2D6A4F] focus:ring-[#52B788] text-sm">
        </div>
        <button type="button"
                @click="geocode()"
                :disabled="loading || !address"
                class="self-end flex items-center gap-2 px-4 py-2 bg-[#2D6A4F] hover:bg-[#40916C] disabled:bg-gray-300 text-white text-sm font-medium rounded-lg transition-colors">
            <svg x-show="!loading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <svg x-show="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            <span x-text="loading ? 'Recherche…' : 'Localiser'"></span>
        </button>
    </div>

    <div x-show="message" x-transition
         :class="messageType === 'error' ? 'bg-red-50 text-red-700 border-red-200' : 'bg-green-50 text-green-700 border-green-200'"
         class="text-xs px-3 py-2 rounded-lg border flex items-start gap-2">
        <span x-text="message"></span>
    </div>

    <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase tracking-wide">
            Position sur la carte
            <span class="text-gray-400 font-normal normal-case ml-2">— Glisser le marqueur ou cliquer sur la carte pour ajuster</span>
        </label>
        <div id="{{ $id }}" style="height: {{ $mapHeight }}; border-radius: 0.75rem; z-index: 1;"
             class="border border-gray-200 shadow-sm"></div>
    </div>

    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase tracking-wide">Latitude</label>
            <input type="number" step="any" name="{{ $latName }}" x-model="lat"
                   @input="updateMarkerFromInputs()"
                   class="w-full rounded-lg border-gray-200 text-sm bg-gray-50">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase tracking-wide">Longitude</label>
            <input type="number" step="any" name="{{ $lngName }}" x-model="lng"
                   @input="updateMarkerFromInputs()"
                   class="w-full rounded-lg border-gray-200 text-sm bg-gray-50">
        </div>
    </div>

    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase tracking-wide">Ville</label>
            <input type="text" name="{{ $cityName }}" x-model="city"
                   class="w-full rounded-lg border-gray-200 text-sm">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase tracking-wide">Pays</label>
            <input type="text" name="{{ $countryName }}" x-model="country"
                   class="w-full rounded-lg border-gray-200 text-sm">
        </div>
    </div>
</div>
