<x-app-layout>
    <x-slot name="title">{{ __('formation.title') }}</x-slot>
    <x-slot name="description">{{ __('formation.description') }}</x-slot>

    {{-- Hero --}}
    <div class="bg-gradient-to-br from-[#1A1A2E] to-[#2D6A4F] text-white py-14 px-4">
        <div class="max-w-5xl mx-auto">
            <h1 class="text-3xl sm:text-4xl font-display font-bold mb-3">🎓 {{ __('formation.title') }}</h1>
            <p class="text-white/75 max-w-xl mb-6">{{ __('formation.description') }}</p>
            <form method="GET" action="{{ route('formation.index') }}" class="flex flex-wrap gap-2">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('formation.search_placeholder') }}"
                       class="flex-1 min-w-48 px-4 py-2 rounded-xl text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#52B788]">
                <select name="type" class="px-3 py-2 rounded-xl text-sm text-gray-700 bg-white focus:outline-none">
                    <option value="">{{ __('formation.all_types') }}</option>
                    @foreach($types as $type)
                        <option value="{{ $type }}" {{ request('type') === $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                    @endforeach
                </select>
                <select name="country" class="px-3 py-2 rounded-xl text-sm text-gray-700 bg-white focus:outline-none">
                    <option value="">{{ __('formation.all_countries') }}</option>
                    @foreach($countries as $country)
                        <option value="{{ $country }}" {{ request('country') === $country ? 'selected' : '' }}>{{ $country }}</option>
                    @endforeach
                </select>
                <button class="px-5 py-2 bg-[#52B788] text-white text-sm font-semibold rounded-xl hover:bg-[#40916C] transition-colors">{{ __('formation.filter') }}</button>
                @if(request()->hasAny(['q','type','country']))
                    <a href="{{ route('formation.index') }}" class="px-4 py-2 bg-white/10 text-white text-sm rounded-xl hover:bg-white/20 transition-colors">{{ __('formation.reset') }}</a>
                @endif
            </form>
        </div>
    </div>

    <div class="max-w-6xl mx-auto px-4 py-10">

        @if($formations->isEmpty())
            <div class="text-center py-16 text-gray-400">
                <p class="text-4xl mb-3">📭</p>
                <p class="text-lg font-medium">{{ __('formation.no_results') }}</p>
                <p class="text-sm mt-1">{{ __('formation.no_results_desc') }}</p>
            </div>
        @else
        <p class="text-sm text-gray-500 mb-6">{{ $formations->total() }} {{ __('formation.results_found') }}</p>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($formations as $formation)
            <a href="{{ route('formation.show', $formation->slug) }}"
               class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all overflow-hidden group flex flex-col">
                @if($formation->thumbnail)
                    <img src="{{ asset('storage/'.$formation->thumbnail) }}" class="w-full h-40 object-cover group-hover:scale-105 transition-transform duration-300" alt="">
                @else
                    <div class="w-full h-40 bg-gradient-to-br from-[#F8F5F0] to-[#d4e8dc] flex items-center justify-center">
                        <span class="text-4xl">🎓</span>
                    </div>
                @endif
                <div class="p-4 flex-1 flex flex-col">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-[#F8F5F0] text-[#2D6A4F]">{{ ucfirst($formation->type) }}</span>
                        @if($formation->is_online)
                            <span class="text-xs text-blue-600 font-medium">🌐 {{ __('formation.online') }}</span>
                        @endif
                    </div>
                    <h3 class="font-semibold text-gray-900 text-sm leading-snug mb-2 group-hover:text-[#2D6A4F] transition-colors">
                        {{ $formation->title }}
                    </h3>
                    @if($formation->organizer)
                        <p class="text-xs text-gray-500 mb-1">🏛 {{ $formation->organizer }}</p>
                    @endif
                    @if($formation->start_date)
                        <p class="text-xs text-gray-500 mb-1">📅 {{ $formation->start_date->translatedFormat('d F Y') }}</p>
                    @endif
                    @if($formation->country)
                        <p class="text-xs text-gray-500">📍 {{ $formation->country }}{{ $formation->location ? ' · '.$formation->location : '' }}</p>
                    @endif
                    <div class="mt-auto pt-3 flex items-center justify-between">
                        <span class="text-xs text-[#2D6A4F] font-semibold">
                            {{ $formation->price ? number_format($formation->price, 0, ',', ' ').' '.__('formation.currency') : __('formation.free') }}
                        </span>
                        <span class="text-xs text-gray-400">{{ strtoupper($formation->language) }}</span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
        <div class="mt-8">{{ $formations->links() }}</div>
        @endif
    </div>
</x-app-layout>
