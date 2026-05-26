<x-app-layout>
    <x-slot name="title">Mentions légales &amp; Politique de confidentialité</x-slot>

    <div class="max-w-3xl mx-auto px-4 py-12">

        <h1 class="text-3xl font-display font-bold text-[#2D6A4F] mb-2">Mentions légales</h1>
        <p class="text-gray-500 text-sm mb-10">Dernière mise à jour : {{ date('d/m/Y') }}</p>

        <div class="prose prose-green max-w-none space-y-8 text-gray-700">

            <section>
                <h2 class="text-xl font-semibold text-[#1A1A2E] mb-2">1. Éditeur du site</h2>
                <p>
                    La Plateforme 3AO est éditée par <strong>l'Alliance pour l'Agroécologie en Afrique de l'Ouest (3AO)</strong>,
                    organisation à but non lucratif, dont le siège est situé en Afrique de l'Ouest.
                </p>
                <p>Contact : <a href="mailto:contact3ao@gmail.com" class="text-[#2D6A4F] underline">contact3ao@gmail.com</a></p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-[#1A1A2E] mb-2">2. Hébergement</h2>
                <p>
                    Le site est hébergé sur des serveurs sécurisés. Les données sont stockées en conformité avec la réglementation applicable.
                </p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-[#1A1A2E] mb-2">3. Propriété intellectuelle</h2>
                <p>
                    L'ensemble des contenus présents sur ce site (textes, images, vidéos, ressources documentaires) sont protégés par le droit d'auteur.
                    Toute reproduction sans autorisation préalable est interdite, sauf mention contraire de licence libre (Creative Commons).
                </p>
            </section>

            <hr class="border-gray-200 my-8">

            <h2 class="text-2xl font-display font-bold text-[#2D6A4F] mb-2">Politique de confidentialité</h2>

            <section>
                <h3 class="text-lg font-semibold text-[#1A1A2E] mb-2">4. Données collectées</h3>
                <p>Lors de votre inscription, nous collectons les données suivantes :</p>
                <ul class="list-disc pl-5 space-y-1 text-sm">
                    <li>Nom et prénom</li>
                    <li>Adresse email</li>
                    <li>Pays et organisation (optionnels)</li>
                    <li>Contenus publiés (actualités, ressources, contributions forum)</li>
                </ul>
                <p class="mt-3">Ces données sont utilisées exclusivement pour le fonctionnement de la plateforme et ne sont pas revendues à des tiers.</p>
            </section>

            <section>
                <h3 class="text-lg font-semibold text-[#1A1A2E] mb-2">5. Cookies</h3>
                <p>Nous utilisons uniquement des cookies :</p>
                <ul class="list-disc pl-5 space-y-1 text-sm">
                    <li><strong>Essentiels</strong> : session d'authentification, protection CSRF.</li>
                    <li><strong>Analytique anonymisé</strong> : mesure d'audience sans identification personnelle.</li>
                    <li><strong>Traduction</strong> : Google Translate (tiers) — uniquement si accepté.</li>
                </ul>
                <p class="mt-3">Vous pouvez refuser les cookies non essentiels via la bannière de consentement.</p>
            </section>

            <section>
                <h3 class="text-lg font-semibold text-[#1A1A2E] mb-2">6. Vos droits (RGPD)</h3>
                <p>Conformément au Règlement Général sur la Protection des Données (RGPD), vous disposez des droits suivants :</p>
                <ul class="list-disc pl-5 space-y-1 text-sm">
                    <li><strong>Droit d'accès</strong> : obtenir une copie de vos données.</li>
                    <li><strong>Droit de rectification</strong> : corriger vos données inexactes.</li>
                    <li><strong>Droit à l'effacement</strong> : demander la suppression de votre compte et données.</li>
                    <li><strong>Droit à la portabilité</strong> : exporter vos données.</li>
                    <li><strong>Droit d'opposition</strong> : vous opposer à certains traitements.</li>
                </ul>
                <p class="mt-3">
                    Pour exercer ces droits, contactez-nous à :
                    <a href="mailto:contact3ao@gmail.com" class="text-[#2D6A4F] underline">contact3ao@gmail.com</a>
                </p>
            </section>

            <section>
                <h3 class="text-lg font-semibold text-[#1A1A2E] mb-2">7. Durée de conservation</h3>
                <p>Vos données sont conservées tant que votre compte est actif. En cas de suppression de compte, vos données personnelles sont effacées sous 30 jours, à l'exception des contributions publiées anonymisées.</p>
            </section>

            <section>
                <h3 class="text-lg font-semibold text-[#1A1A2E] mb-2">8. Contact DPO</h3>
                <p>
                    Pour toute question relative à la protection de vos données, contactez :
                    <a href="mailto:contact3ao@gmail.com" class="text-[#2D6A4F] underline">contact3ao@gmail.com</a>
                </p>
            </section>

        </div>

        <div class="mt-10 pt-6 border-t border-gray-200">
            <a href="{{ url()->previous() }}" class="text-sm text-[#2D6A4F] hover:underline">&larr; Retour</a>
        </div>

    </div>
</x-app-layout>
