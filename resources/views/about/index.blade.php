<x-app-layout>
    <x-slot name="title">{{ __('about.title') }}</x-slot>

    {{-- Hero --}}
    <section class="relative bg-gradient-to-br from-[#1A1A2E] to-[#2D6A4F] text-white overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24 relative">
            <div class="max-w-3xl">
                <h1 class="text-4xl lg:text-5xl font-bold mb-6">
                    {{ __('about.title') }}
                </h1>
                <p class="text-lg lg:text-xl text-white/90 leading-relaxed">
                    {{ __('about.subtitle') }}
                </p>
            </div>
        </div>
    </section>

    {{-- Mission & Contexte --}}
    <section id="mission" class="py-16 lg:py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-10 lg:gap-14 items-center">
                <div class="lg:col-span-3">
                    <span class="inline-block px-3 py-1 rounded-full bg-[#2D6A4F]/10 text-[#2D6A4F] text-sm font-semibold mb-4">
                        {{ __('about.mission_badge') }}
                    </span>
                    <h2 class="text-3xl font-bold text-[#1A1A2E] mb-6">
                        {{ __('about.mission_title') }}
                    </h2>
                    <div class="prose prose-lg text-gray-700 max-w-none">
                        <p>{{ __('about.mission_paragraph_1') }}</p>
                        <p>{{ __('about.mission_paragraph_2') }}</p>
                        <p>{{ __('about.mission_paragraph_3') }}</p>
                    </div>
                </div>
                <div class="lg:col-span-2 flex justify-center">
                    <div class="w-full max-w-[320px] aspect-square rounded-2xl bg-[#F8F5F0] p-6 shadow-lg flex items-center justify-center">
                        <img src="{{ asset('images/logo-3ao.jpeg') }}"
                             alt="Logo 3AO"
                             class="max-w-full max-h-full object-contain rounded-xl">
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Chiffres clés --}}
    <section class="py-14 bg-[#F8F5F0]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white rounded-xl p-6 text-center shadow-sm">
                    <div class="text-3xl lg:text-4xl font-bold text-[#2D6A4F]">2018</div>
                    <div class="text-sm text-gray-600 mt-2">{{ __('about.year_created') }}</div>
                </div>
                <div class="bg-white rounded-xl p-6 text-center shadow-sm">
                    <div class="text-3xl lg:text-4xl font-bold text-[#2D6A4F]">120+</div>
                    <div class="text-sm text-gray-600 mt-2">{{ __('about.member_organizations') }}</div>
                </div>
                <div class="bg-white rounded-xl p-6 text-center shadow-sm">
                    <div class="text-3xl lg:text-4xl font-bold text-[#2D6A4F]">7</div>
                    <div class="text-sm text-gray-600 mt-2">{{ __('about.committee_members') }}</div>
                </div>
                <div class="bg-white rounded-xl p-6 text-center shadow-sm">
                    <div class="text-3xl lg:text-4xl font-bold text-[#2D6A4F]">17</div>
                    <div class="text-sm text-gray-600 mt-2">{{ __('about.west_african_countries') }}</div>
                </div>
            </div>
        </div>
    </section>

    {{-- Axes d'action --}}
    <section id="axes" class="py-16 lg:py-20 bg-[#1A1A2E] text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-stretch">
                {{-- Card à gauche --}}
                <div class="bg-white/10 rounded-2xl p-8 lg:p-10 flex flex-col justify-center">
                    <h2 class="text-3xl font-bold mb-4">{{ __('about.brief_title') }}</h2>
                    <p class="text-white/80 leading-relaxed mb-6">
                        {{ __('about.brief_text') }}
                    </p>
                    <ul class="space-y-3">
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-[#52B788] flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-white/80">{{ __('about.brief_point_1') }}</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-[#52B788] flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-white/80">{{ __('about.brief_point_2') }}</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-[#52B788] flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-white/80">{{ __('about.brief_point_3') }}</span>
                        </li>
                    </ul>
                </div>

                {{-- Axes à droite --}}
                <div class="bg-white/5 rounded-2xl p-8 lg:p-10 flex flex-col justify-center">
                    <h3 class="text-xl font-bold mb-6">{{ __('about.actions_title') }}</h3>
                    <ul class="space-y-5">
                        <li class="flex items-start gap-4">
                            <span class="w-8 h-8 rounded-full bg-[#52B788] text-white flex items-center justify-center text-sm font-bold flex-shrink-0">1</span>
                            <span class="text-white/80">{{ __('about.action_1') }}</span>
                        </li>
                        <li class="flex items-start gap-4">
                            <span class="w-8 h-8 rounded-full bg-[#52B788] text-white flex items-center justify-center text-sm font-bold flex-shrink-0">2</span>
                            <span class="text-white/80">{{ __('about.action_2') }}</span>
                        </li>
                        <li class="flex items-start gap-4">
                            <span class="w-8 h-8 rounded-full bg-[#52B788] text-white flex items-center justify-center text-sm font-bold flex-shrink-0">3</span>
                            <span class="text-white/80">{{ __('about.action_3') }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- Contact --}}
    <section id="contact" class="py-16 lg:py-20 bg-[#F8F5F0]">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-bold text-[#1A1A2E] mb-4">{{ __('about.contact_title') }}</h2>
            <p class="text-gray-600 mb-8">{{ __('about.contact_text') }}</p>
            <a href="mailto:contact3AO@gmail.com" class="btn-primary text-lg px-8 py-3 inline-flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                contact3AO@gmail.com
            </a>
        </div>
    </section>
</x-app-layout>
