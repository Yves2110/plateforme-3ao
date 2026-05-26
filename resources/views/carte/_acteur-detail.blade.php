<div class="space-y-6">
    {{-- Header avec logo --}}
    <div class="flex items-start gap-4">
        <div class="w-20 h-20 rounded-2xl bg-[#2D6A4F] flex items-center justify-center shrink-0 overflow-hidden">
            @if($actor->logo)
                <img src="{{ asset('storage/' . $actor->logo) }}" alt="{{ $actor->name }}" class="w-full h-full object-cover">
            @else
                <span class="text-white font-display font-bold text-2xl">{{ strtoupper(substr($actor->name, 0, 2)) }}</span>
            @endif
        </div>
        <div class="flex-1 min-w-0">
            <span class="text-xs font-bold uppercase tracking-wider px-2.5 py-1 rounded-full bg-[#D4A017] text-[#1A1A2E]">{{ $actor->type }}</span>
            <h1 class="font-display text-2xl font-bold text-[#1A1A2E] mt-2">{{ $actor->name }}</h1>
            @if($actor->city || $actor->country)
                <p class="text-sm text-gray-500">
                    {{ collect([$actor->city, $actor->region, $actor->country])->filter()->join(', ') }}
                </p>
            @endif
        </div>
    </div>

    {{-- À propos --}}
    @if($actor->description)
        <section class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
            <h2 class="font-display text-lg font-bold text-[#1A1A2E] mb-3">À propos</h2>
            <div class="prose prose-sm max-w-none text-gray-700 leading-relaxed whitespace-pre-line">{{ $actor->description }}</div>
        </section>
    @endif

    {{-- Mini carte --}}
    @if($actor->lat && $actor->lng)
        <section class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
            <div id="mini-map-{{ $actor->id }}" style="height: 250px;"></div>
            <script>
                (function() {
                    const map = L.map('mini-map-{{ $actor->id }}', { scrollWheelZoom: false, zoomControl: false }).setView([{{ $actor->lat }}, {{ $actor->lng }}], 12);
                    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                        attribution: '© CARTO | © OSM',
                        maxZoom: 19,
                        subdomains: 'abcd'
                    }).addTo(map);
                    L.marker([{{ $actor->lat }}, {{ $actor->lng }}]).addTo(map);
                })();
            </script>
        </section>
    @endif

    {{-- Contact --}}
    <section class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
        <h2 class="font-display text-lg font-bold text-[#1A1A2E] mb-4">Contact</h2>
        <ul class="space-y-3 text-sm">
            @if($actor->website)
                <li class="flex items-start gap-2.5">
                    <svg class="w-4 h-4 text-[#52B788] mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                    <a href="{{ $actor->website }}" target="_blank" rel="noopener" class="text-[#2D6A4F] hover:underline break-all">{{ str_replace(['https://', 'http://'], '', $actor->website) }}</a>
                </li>
            @endif
            @if($actor->email)
                <li class="flex items-start gap-2.5">
                    <svg class="w-4 h-4 text-[#52B788] mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <a href="mailto:{{ $actor->email }}" class="text-[#2D6A4F] hover:underline break-all">{{ $actor->email }}</a>
                </li>
            @endif
            @if($actor->phone)
                <li class="flex items-start gap-2.5">
                    <svg class="w-4 h-4 text-[#52B788] mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    <a href="tel:{{ $actor->phone }}" class="text-[#2D6A4F] hover:underline">{{ $actor->phone }}</a>
                </li>
            @endif
            @if($actor->address || $actor->city)
                <li class="flex items-start gap-2.5">
                    <svg class="w-4 h-4 text-[#52B788] mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span class="text-gray-700">{{ collect([$actor->address, $actor->city, $actor->country])->filter()->join(', ') }}</span>
                </li>
            @endif
        </ul>

        @if($actor->email)
            <a href="mailto:{{ $actor->email }}" class="mt-4 w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-[#2D6A4F] hover:bg-[#40916C] text-white text-sm font-semibold rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                Contacter
            </a>
        @endif
    </section>

    {{-- Partenaires --}}
    @php
        $partnersFrom = \App\Models\ActorLink::where('actor_id_from', $actor->id)->with('actorTo')->get()->pluck('actorTo')->filter();
        $partnersTo = \App\Models\ActorLink::where('actor_id_to', $actor->id)->with('actorFrom')->get()->pluck('actorFrom')->filter();
        $partners = $partnersFrom->concat($partnersTo)->unique('id')->where('is_validated', true);
    @endphp
    @if($partners->count())
        <section class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
            <h2 class="font-display text-lg font-bold text-[#1A1A2E] mb-4">Partenaires ({{ $partners->count() }})</h2>
            <div class="grid gap-3">
                @foreach($partners->take(6) as $p)
                    <div class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 hover:border-[#52B788] transition-colors">
                        <div class="w-10 h-10 rounded-lg bg-[#2D6A4F] flex items-center justify-center text-white text-xs font-bold shrink-0">
                            {{ strtoupper(substr($p->name, 0, 2)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-[#1A1A2E] truncate">{{ $p->name }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ $p->type }} · {{ $p->country }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
            @if($partners->count() > 6)
                <p class="text-xs text-gray-500 mt-3 text-center">+ {{ $partners->count() - 6 }} autres partenaires</p>
            @endif
        </section>
    @endif

    {{-- Méta --}}
    <div class="text-xs text-gray-500 text-center">
        <p>Inscrit depuis {{ $actor->created_at->isoFormat('MMMM YYYY') }}</p>
    </div>
</div>
