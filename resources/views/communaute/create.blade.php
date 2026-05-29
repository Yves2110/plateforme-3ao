<x-app-layout>
    <x-slot name="title">Nouvelle discussion</x-slot>

    <div class="bg-gradient-to-r from-[#2D6A4F] to-[#40916C] py-10">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <a href="{{ route('communaute.index') }}" class="text-white/70 hover:text-white text-sm mb-1 block">← Communauté</a>
            <h1 class="font-display text-2xl font-bold text-white">Nouvelle discussion</h1>
        </div>
    </div>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10"
         x-data="{
             withPoll: false,
             pollOptions: ['', ''],
             addOption() { if (this.pollOptions.length < 6) this.pollOptions.push(''); },
             removeOption(i) { if (this.pollOptions.length > 2) this.pollOptions.splice(i, 1); }
         }">

        @if($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700 space-y-1">
                @foreach($errors->all() as $err)
                    <p>• {{ $err }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('communaute.store') }}" method="POST" class="space-y-6">
            @csrf

            {{-- Titre --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-6 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Titre de la discussion <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" required minlength="5" maxlength="200"
                           placeholder="Ex : Comment améliorer la fertilité des sols en zone sahélienne ?"
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#52B788] text-sm">
                </div>

                {{-- Catégorie --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Catégorie <span class="text-red-500">*</span></label>
                    <select name="category" required
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#52B788] text-sm bg-white">
                        <option value="">  Choisir une catégorie  </option>
                        @foreach($categories as $slug => $name)
                            <option value="{{ $slug }}" {{ old('category', request('category')) === $slug ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Corps --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Message <span class="text-red-500">*</span></label>
                    <textarea name="body" rows="8" required minlength="20"
                              placeholder="Décrivez votre question, expérience ou sujet de discussion…"
                              class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#52B788] text-sm resize-y">{{ old('body') }}</textarea>
                    <p class="text-xs text-gray-400 mt-1">Minimum 20 caractères. Soyez précis et bienveillant.</p>
                </div>
            </div>

            {{-- Sondage optionnel --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#52B788]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <span class="font-semibold text-gray-700 text-sm">Ajouter un sondage</span>
                        <span class="text-xs text-gray-400">(optionnel)</span>
                    </div>
                    <button type="button" x-on:click="withPoll = !withPoll"
                            class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors"
                            x-bind:class="withPoll ? 'bg-[#2D6A4F]' : 'bg-gray-200'">
                        <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform"
                              x-bind:class="withPoll ? 'translate-x-6' : 'translate-x-1'"></span>
                    </button>
                </div>

                <div x-show="withPoll" x-transition class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Question du sondage</label>
                        <input type="text" name="poll_question" value="{{ old('poll_question') }}" maxlength="255"
                               placeholder="Ex : Quelle technique avez-vous utilisée ?"
                               class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#52B788] text-sm">
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Options (2 à 6)</label>
                        <template x-for="(opt, i) in pollOptions" :key="i">
                            <div class="flex gap-2">
                                <input type="text" x-bind:name="'poll_options[' + i + ']'" x-model="pollOptions[i]"
                                       x-bind:placeholder="'Option ' + (i + 1)"
                                       class="flex-1 px-4 py-2 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#52B788] text-sm">
                                <button type="button" x-on:click="removeOption(i)"
                                        x-show="pollOptions.length > 2"
                                        class="p-2 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </template>
                        <button type="button" x-on:click="addOption" x-show="pollOptions.length < 6"
                                class="flex items-center gap-1.5 text-sm text-[#2D6A4F] hover:underline font-medium mt-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Ajouter une option
                        </button>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date de clôture <span class="text-xs text-gray-400">(optionnel)</span></label>
                        <input type="date" name="poll_closes_at" value="{{ old('poll_closes_at') }}"
                               min="{{ now()->addDay()->toDateString() }}"
                               class="px-4 py-2 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#52B788] text-sm">
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-between gap-4">
                <a href="{{ route('communaute.index') }}" class="text-sm text-gray-500 hover:text-gray-700 transition-colors">Annuler</a>
                <button type="submit" class="btn-primary px-8">
                    Publier la discussion
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
