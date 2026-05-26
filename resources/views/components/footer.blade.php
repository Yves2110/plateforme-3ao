<footer class="bg-[#1A1A2E] text-white">
    {{-- Newsletter band --}}
    <div class="bg-[#2D6A4F] py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                <div>
                    <h3 class="font-display text-xl font-bold text-white">{{ __('site.newsletter_title') }}</h3>
                    <p class="text-white/80 text-sm mt-1">{{ __('site.newsletter_subtitle') }}</p>
                </div>
                @if(session('newsletter_status'))
                    <p class="text-sm text-white/95 bg-white/10 rounded-full px-4 py-2.5 max-w-md">
                        {{ __('site.newsletter_' . session('newsletter_status')) }}
                    </p>
                @else
                    <form class="flex flex-wrap gap-2 w-full md:w-auto min-w-[320px]" action="{{ route('newsletter.subscribe') }}" method="POST">
                        @csrf
                        <x-spam-protection />
                        <input type="email" name="email" value="{{ old('email') }}" required
                               placeholder="{{ __('site.newsletter_email') }}"
                               class="flex-1 px-4 py-2.5 rounded-full text-sm text-gray-800 bg-white border-0 focus:outline-none focus:ring-2 focus:ring-[#F4C842]">
                        <button type="submit"
                                class="px-5 py-2.5 bg-[#D4A017] hover:bg-[#F4C842] text-white hover:text-gray-900 font-semibold rounded-full text-sm transition-colors whitespace-nowrap">
                            {{ __('site.newsletter_subscribe') }}
                        </button>
                    </form>
                    @error('email')
                        <p class="text-xs text-[#F4C842] mt-2 w-full md:text-right">{{ $message }}</p>
                    @enderror
                @endif
            </div>
        </div>
    </div>

    {{-- Contenu principal --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10">

            {{-- Col 1 : À propos --}}
            <div class="lg:col-span-1">
                <div class="mb-4">
                    <x-logo href="{{ route('home') }}" size="lg" class="bg-white/95 rounded-xl p-2" />
                </div>
                <p class="text-white/60 text-sm leading-relaxed">
                    {{ __('site.about_blurb') }}
                </p>
                <div class="flex items-center gap-3 mt-5">
                    <a href="#" class="w-8 h-8 bg-white/10 hover:bg-[#52B788] rounded-full flex items-center justify-center transition-colors">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
                    </a>
                    <a href="#" class="w-8 h-8 bg-white/10 hover:bg-[#52B788] rounded-full flex items-center justify-center transition-colors">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                    </a>
                    <a href="#" class="w-8 h-8 bg-white/10 hover:bg-[#52B788] rounded-full flex items-center justify-center transition-colors">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M22.54 6.42a2.78 2.78 0 00-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46A2.78 2.78 0 001.46 6.42 29 29 0 001 12a29 29 0 00.46 5.58 2.78 2.78 0 001.95 1.96C5.12 20 12 20 12 20s6.88 0 8.59-.46a2.78 2.78 0 001.95-1.96A29 29 0 0023 12a29 29 0 00-.46-5.58zM9.75 15.02V8.98L15.5 12l-5.75 3.02z"/></svg>
                    </a>
                    <a href="#" class="w-8 h-8 bg-white/10 hover:bg-[#52B788] rounded-full flex items-center justify-center transition-colors">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z"/><circle cx="4" cy="4" r="2"/></svg>
                    </a>
                </div>
            </div>

            {{-- Col 2 : Navigation --}}
            <div>
                <h4 class="font-display font-semibold text-sm uppercase tracking-widest text-[#52B788] mb-4">{{ __('site.footer_platform') }}</h4>
                <ul class="space-y-2.5 text-sm text-white/70">
                    <li><a href="{{ route('home') }}" class="hover:text-white transition-colors">{{ __('site.footer_home') }}</a></li>
                    <li><a href="{{ route('bibliotheque.index') }}" class="hover:text-white transition-colors">{{ __('site.footer_library') }}</a></li>
                    <li><a href="{{ route('communaute.index') }}" class="hover:text-white transition-colors">{{ __('site.footer_community') }}</a></li>
                    <li><a href="{{ route('multimedia.index') }}" class="hover:text-white transition-colors">{{ __('site.footer_multimedia') }}</a></li>
                    <li><a href="{{ route('carte.index') }}" class="hover:text-white transition-colors">{{ __('site.footer_map') }}</a></li>
                    <li><a href="{{ route('evenements.index') }}" class="hover:text-white transition-colors">{{ __('site.footer_events') }}</a></li>
                </ul>
            </div>

            {{-- Col 3 : Ressources --}}
            <div>
                <h4 class="font-display font-semibold text-sm uppercase tracking-widest text-[#52B788] mb-4">{{ __('site.footer_resources') }}</h4>
                <ul class="space-y-2.5 text-sm text-white/70">
                    <li><a href="{{ route('formation.index') }}" class="hover:text-white transition-colors">{{ __('site.footer_training_hub') }}</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">{{ __('site.footer_publications') }}</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">{{ __('site.footer_peasant') }}</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">{{ __('site.footer_factsheets') }}</a></li>
                    <li><a href="{{ route('rss.actualites') }}" class="hover:text-white transition-colors">{{ __('site.footer_rss') }}</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">{{ __('site.footer_api') }}</a></li>
                </ul>
            </div>

            {{-- Col 4 : Contact --}}
            <div>
                <h4 class="font-display font-semibold text-sm uppercase tracking-widest text-[#52B788] mb-4">{{ __('site.footer_contact') }}</h4>
                <ul class="space-y-3 text-sm text-white/70">
                    <li class="flex items-start gap-2">
                        <svg class="w-4 h-4 mt-0.5 shrink-0 text-[#52B788]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        {{ __('site.footer_address') }}
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 shrink-0 text-[#52B788]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <a href="mailto:contact3ao@gmail.com" class="hover:text-white transition-colors">contact3ao@gmail.com</a>
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 shrink-0 text-[#52B788]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <a href="mailto:secretariat@roppa-afrique.org" class="hover:text-white transition-colors">secretariat@roppa-afrique.org</a>
                    </li>
                </ul>
                <div class="mt-5 p-3 bg-white/5 rounded-xl text-xs text-white/50">
                    {{ __('site.footer_funded') }} <strong class="text-white/70">{{ __('site.footer_funder') }}</strong>
                </div>
            </div>
        </div>
    </div>

    {{-- Bas de footer --}}
    <div class="border-t border-white/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-white/40">
            <span>{{ __('site.footer_copyright', ['year' => date('Y')]) }}</span>
            <div class="flex items-center gap-4">
                <a href="{{ route('mentions-legales') }}" class="hover:text-white/70 transition-colors">{{ __('site.footer_privacy') }}</a>
                <a href="{{ route('mentions-legales') }}" class="hover:text-white/70 transition-colors">{{ __('site.footer_legal') }}</a>
                <a href="{{ route('rss.actualites') }}" class="hover:text-white/70 transition-colors flex items-center gap-1">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M6.18 15.64a2.18 2.18 0 0 1 2.18 2.18C8.36 19.01 7.38 20 6.18 20C4.98 20 4 19.01 4 17.82a2.18 2.18 0 0 1 2.18-2.18M4 4.44A15.56 15.56 0 0 1 19.56 20h-2.83A12.73 12.73 0 0 0 4 7.27V4.44m0 5.66a9.9 9.9 0 0 1 9.9 9.9h-2.83A7.07 7.07 0 0 0 4 12.93V10.1z"/></svg>
                    RSS
                </a>
            </div>
        </div>
    </div>
</footer>
