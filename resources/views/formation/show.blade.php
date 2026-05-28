<x-app-layout>
    <x-slot name="title">{{ $formation->title }}</x-slot>
    <x-slot name="description">{{ Str::limit($formation->description, 155) }}</x-slot>

    <div class="max-w-4xl mx-auto px-4 py-10">

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700">{{ session('success') }}</div>
        @endif

        <x-public-manage-bar
            label="Formations"
            :permissions="['gerer-formations', 'administrer-utilisateurs']"
            :create-route="route('admin.formations.create')"
            :list-route="route('admin.formations.index')"
            :item="$formation"
            :edit-route="route('admin.formations.edit', $formation)"
            :toggle-route="route('contenu.formations.toggle', $formation)"
            published-key="is_validated"
        />

        {{-- Breadcrumb --}}
        <nav class="text-xs text-gray-400 mb-6 flex items-center gap-1.5">
            <a href="{{ route('formation.index') }}" class="hover:text-[#2D6A4F] transition-colors">Hub Formation</a>
            <span>/</span>
            <span class="text-gray-600">{{ Str::limit($formation->title, 40) }}</span>
        </nav>

        <div class="grid lg:grid-cols-3 gap-8">

            {{-- Contenu principal --}}
            <div class="lg:col-span-2">
                @if($formation->thumbnail)
                    <img src="{{ asset('storage/'.$formation->thumbnail) }}" class="w-full h-56 object-cover rounded-2xl mb-6" alt="">
                @endif

                <div class="flex flex-wrap gap-2 mb-4">
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-[#F8F5F0] text-[#2D6A4F]">{{ ucfirst($formation->type) }}</span>
                    @if($formation->is_online)
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-blue-50 text-blue-700">🌐 En ligne</span>
                    @endif
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-gray-100 text-gray-600">{{ strtoupper($formation->language) }}</span>
                </div>

                <h1 class="text-2xl sm:text-3xl font-display font-bold text-[#1A1A2E] mb-4">{{ $formation->title }}</h1>

                @if($formation->description)
                <div class="prose prose-green max-w-none text-gray-700 text-sm leading-relaxed mb-6">
                    {!! nl2br(e($formation->description)) !!}
                </div>
                @endif

                @if($formation->objectives)
                <div class="bg-[#F8F5F0] rounded-2xl p-4 mb-6">
                    <h2 class="font-semibold text-[#2D6A4F] text-sm mb-2">🎯 Objectifs pédagogiques</h2>
                    <div class="text-sm text-gray-700 leading-relaxed">{!! nl2br(e($formation->objectives)) !!}</div>
                </div>
                @endif

                @if($formation->audience)
                <div class="border border-gray-100 rounded-2xl p-4 mb-6">
                    <h2 class="font-semibold text-gray-700 text-sm mb-1">👥 Public cible</h2>
                    <p class="text-sm text-gray-600">{{ $formation->audience }}</p>
                </div>
                @endif
            </div>

            {{-- Sidebar infos --}}
            <div class="space-y-4">
                <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-5 space-y-3 sticky top-4">
                    @if($formation->price)
                        <div class="text-2xl font-bold text-[#2D6A4F]">{{ number_format($formation->price, 0, ',', ' ') }} FCFA</div>
                    @else
                        <div class="text-2xl font-bold text-green-600">Gratuit</div>
                    @endif

                    @if($formation->registration_url)
                    <a href="{{ $formation->registration_url }}" target="_blank" rel="noopener"
                       class="block w-full text-center px-5 py-2.5 bg-[#2D6A4F] text-white text-sm font-semibold rounded-xl hover:bg-[#40916C] transition-colors">
                        S'inscrire →
                    </a>
                    @endif

                    <div class="border-t border-gray-100 pt-3 space-y-2 text-sm text-gray-600">
                        @if($formation->organizer)
                        <div class="flex gap-2"><span class="text-gray-400 w-5">🏛</span><span>{{ $formation->organizer }}</span></div>
                        @endif
                        @if($formation->start_date)
                        <div class="flex gap-2"><span class="text-gray-400 w-5">📅</span>
                            <span>{{ $formation->start_date->translatedFormat('d F Y') }}
                            @if($formation->end_date && $formation->end_date != $formation->start_date)
                                → {{ $formation->end_date->translatedFormat('d F Y') }}
                            @endif</span>
                        </div>
                        @endif
                        @if($formation->duration)
                        <div class="flex gap-2"><span class="text-gray-400 w-5">⏱</span><span>{{ $formation->duration }}</span></div>
                        @endif
                        @if(!$formation->is_online && $formation->location)
                        <div class="flex gap-2"><span class="text-gray-400 w-5">📍</span><span>{{ $formation->location }}{{ $formation->country ? ', '.$formation->country : '' }}</span></div>
                        @endif
                    </div>
                </div>

                @if($related->count())
                <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-4">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3">Formations similaires</h3>
                    @foreach($related as $r)
                    <a href="{{ route('formation.show', $r->slug) }}"
                       class="block py-2 border-b border-gray-50 last:border-0 text-xs text-gray-700 hover:text-[#2D6A4F] transition-colors">
                        {{ Str::limit($r->title, 55) }}
                    </a>
                    @endforeach
                </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
