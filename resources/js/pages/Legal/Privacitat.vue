<script setup lang="ts">
import WebAppLayout from '@/layouts/WebAppLayout.vue';

const drets = [
    { nom: 'Accés', desc: "Obtenir confirmació sobre si tractem dades vostres i accedir-hi." },
    { nom: 'Rectificació', desc: "Sol·licitar la correcció de dades inexactes." },
    { nom: 'Supressió', desc: "Demanar l'eliminació de les vostres dades quan ja no siguin necessàries." },
    { nom: 'Oposició', desc: "Oposar-vos al tractament per motius relacionats amb la vostra situació particular." },
    { nom: 'Limitació', desc: "Sol·licitar que limitem el tractament de les vostres dades." },
    { nom: 'Portabilitat', desc: "Rebre les vostres dades en un format estructurat i d'ús comú." },
    { nom: 'Retirada del consentiment', desc: "En qualsevol moment, sense efecte retroactiu." },
];
</script>

<template>
    <WebAppLayout>
        <div class="min-h-screen bg-slate-50">
            <!-- Header -->
            <div class="bg-gradient-to-br from-[#00617E] via-[#0a7494] to-[#003d50] px-4 py-16 text-white sm:px-6 lg:px-8">
                <div class="mx-auto max-w-3xl">
                    <p class="mb-2 text-sm font-semibold tracking-widest text-white/60 uppercase">
                        Farmàcia Soler · www.farmaciasoler.es
                    </p>
                    <h1 class="text-4xl font-bold tracking-tight">
                        Política de Privacitat
                    </h1>
                    <p class="mt-3 text-sm text-white/70">
                        Darrera actualització: 20 de maig de 2025
                    </p>
                </div>
            </div>

            <!-- Content -->
            <div class="mx-auto max-w-3xl px-4 py-12 sm:px-6 lg:px-8">
                <div class="space-y-10">

                    <!-- 1 -->
                    <section>
                        <h2 class="mb-4 text-lg font-bold text-[#00617E]">
                            1. Responsable del tractament
                        </h2>
                        <p class="mb-4 text-sm leading-relaxed text-slate-600">
                            En compliment del Reglament (UE) 2016/679 (RGPD) i la Llei Orgànica 3/2018 (LOPDGDD), s'informa que el responsable del tractament de les dades personals recollides a través del lloc web <strong class="text-slate-800">www.farmaciasoler.es</strong> és:
                        </p>
                        <div class="rounded-xl border border-slate-200 bg-white p-5 text-sm text-slate-700">
                            <dl class="space-y-2">
                                <div class="flex gap-2"><dt class="font-semibold text-slate-900 w-28 shrink-0">Titular</dt><dd>Farmàcia Josep Soler Portell</dd></div>
                                <div class="flex gap-2"><dt class="font-semibold text-slate-900 w-28 shrink-0">NIF</dt><dd>40426246C</dd></div>
                                <div class="flex gap-2"><dt class="font-semibold text-slate-900 w-28 shrink-0">Adreça</dt><dd>C/Nou 22, 17600 Figueres (Girona)</dd></div>
                                <div class="flex gap-2"><dt class="font-semibold text-slate-900 w-28 shrink-0">Correu</dt><dd><a href="mailto:josepsoler@cogfi.com" class="text-[#00617E] hover:underline">josepsoler@cogfi.com</a></dd></div>
                                <div class="flex gap-2"><dt class="font-semibold text-slate-900 w-28 shrink-0">Web</dt><dd>www.farmaciasoler.es</dd></div>
                            </dl>
                        </div>
                    </section>

                    <!-- 2 -->
                    <section>
                        <h2 class="mb-4 text-lg font-bold text-[#00617E]">
                            2. Finalitats del tractament
                        </h2>
                        <p class="mb-3 text-sm leading-relaxed text-slate-600">
                            Les dades personals que pugueu facilitar-nos s'utilitzen per a les finalitats següents:
                        </p>
                        <ul class="space-y-2 text-sm text-slate-600">
                            <li class="flex gap-2"><span class="mt-1 h-1.5 w-1.5 shrink-0 rounded-full bg-[#00617E]"></span>Gestionar les consultes i sol·licituds d'informació enviades a través del formulari de contacte o per correu electrònic.</li>
                            <li class="flex gap-2"><span class="mt-1 h-1.5 w-1.5 shrink-0 rounded-full bg-[#00617E]"></span>Enviar comunicacions comercials i informació sobre serveis, productes o novetats de la farmàcia, sempre que hagueu donat el vostre consentiment previ.</li>
                            <li class="flex gap-2"><span class="mt-1 h-1.5 w-1.5 shrink-0 rounded-full bg-[#00617E]"></span>Complir les obligacions legals aplicables, especialment les derivades de la normativa farmacèutica, fiscal i sanitària.</li>
                            <li class="flex gap-2"><span class="mt-1 h-1.5 w-1.5 shrink-0 rounded-full bg-[#00617E]"></span>Gestionar la relació professional o comercial amb clients, proveïdors i col·laboradors.</li>
                        </ul>
                    </section>

                    <!-- 3 -->
                    <section>
                        <h2 class="mb-4 text-lg font-bold text-[#00617E]">
                            3. Base jurídica del tractament
                        </h2>
                        <p class="mb-3 text-sm leading-relaxed text-slate-600">
                            El tractament de les vostres dades es fonamenta en:
                        </p>
                        <ul class="space-y-2 text-sm text-slate-600">
                            <li class="flex gap-2"><span class="mt-1 h-1.5 w-1.5 shrink-0 rounded-full bg-[#00617E]"></span>El vostre <strong class="text-slate-800">consentiment explícit</strong> (art. 6.1.a RGPD), per a l'enviament de comunicacions comercials.</li>
                            <li class="flex gap-2"><span class="mt-1 h-1.5 w-1.5 shrink-0 rounded-full bg-[#00617E]"></span>L'<strong class="text-slate-800">execució d'una relació contractual o precontractual</strong> (art. 6.1.b RGPD).</li>
                            <li class="flex gap-2"><span class="mt-1 h-1.5 w-1.5 shrink-0 rounded-full bg-[#00617E]"></span>El <strong class="text-slate-800">compliment d'obligacions legals</strong> (art. 6.1.c RGPD).</li>
                            <li class="flex gap-2"><span class="mt-1 h-1.5 w-1.5 shrink-0 rounded-full bg-[#00617E]"></span>L'<strong class="text-slate-800">interès legítim del responsable</strong> (art. 6.1.f RGPD), per a la gestió interna i millora dels serveis.</li>
                        </ul>
                    </section>

                    <!-- 4 -->
                    <section>
                        <h2 class="mb-4 text-lg font-bold text-[#00617E]">
                            4. Conservació de les dades
                        </h2>
                        <p class="text-sm leading-relaxed text-slate-600">
                            Les dades es conservaran durant el temps necessari per complir la finalitat per a la qual es van recollir i per atendre les possibles responsabilitats legals derivades del tractament. Un cop deixi de ser necessari, les dades seran cancel·lades de conformitat amb la normativa de protecció de dades.
                        </p>
                    </section>

                    <!-- 5 -->
                    <section>
                        <h2 class="mb-4 text-lg font-bold text-[#00617E]">
                            5. Destinataris
                        </h2>
                        <p class="mb-3 text-sm leading-relaxed text-slate-600">
                            Les vostres dades no seran cedides a tercers, excepte en els casos següents:
                        </p>
                        <ul class="space-y-2 text-sm text-slate-600">
                            <li class="flex gap-2"><span class="mt-1 h-1.5 w-1.5 shrink-0 rounded-full bg-[#00617E]"></span>Quan hi hagi una obligació legal.</li>
                            <li class="flex gap-2"><span class="mt-1 h-1.5 w-1.5 shrink-0 rounded-full bg-[#00617E]"></span>A proveïdors de serveis tecnològics (allotjament web, analytics) que actuen com a encarregats del tractament i que disposen de les garanties adequades, inclosos els contractes de tractament de dades corresponents.</li>
                        </ul>
                        <p class="mt-3 text-sm text-slate-600">
                            Concretament, fem servir <strong class="text-slate-800">Google Analytics i Google Ads</strong> (Google LLC, Clàusules contractuals tipus) i <strong class="text-slate-800">Meta Pixel</strong> (Meta Platforms Ireland Ltd.).
                        </p>
                    </section>

                    <!-- 6 -->
                    <section>
                        <h2 class="mb-4 text-lg font-bold text-[#00617E]">
                            6. Transferències internacionals
                        </h2>
                        <p class="text-sm leading-relaxed text-slate-600">
                            Alguns dels nostres proveïdors (Google, Meta) poden realitzar transferències de dades fora de l'Espai Econòmic Europeu. Aquestes transferències es realitzen emparades en les Clàusules Contractuals Tipus aprovades per la Comissió Europea o en altres garanties adequades previstes al RGPD.
                        </p>
                    </section>

                    <!-- 7 -->
                    <section>
                        <h2 class="mb-4 text-lg font-bold text-[#00617E]">
                            7. Drets de les persones interessades
                        </h2>
                        <p class="mb-4 text-sm leading-relaxed text-slate-600">
                            Podeu exercir en qualsevol moment els drets següents:
                        </p>
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <div v-for="dret in drets" :key="dret.nom" class="rounded-lg border border-slate-200 bg-white p-4">
                                <p class="font-semibold text-slate-900 text-sm">{{ dret.nom }}</p>
                                <p class="mt-1 text-xs text-slate-500 leading-relaxed">{{ dret.desc }}</p>
                            </div>
                        </div>
                        <div class="mt-4 rounded-xl border border-blue-100 bg-blue-50 p-4 text-sm text-slate-700">
                            Per exercir qualsevol d'aquests drets, adreceu-vos per escrit a
                            <a href="mailto:josepsoler@cogfi.com" class="font-semibold text-[#00617E] hover:underline">josepsoler@cogfi.com</a>,
                            indicant el dret que voleu exercir i adjuntant còpia del vostre document d'identitat.
                            <br /><br />
                            Si considereu que el tractament no s'ajusta a la normativa, podeu presentar una reclamació davant l'
                            <a href="https://www.aepd.es" target="_blank" rel="noopener noreferrer" class="font-semibold text-[#00617E] hover:underline">Agència Espanyola de Protecció de Dades</a>.
                        </div>
                    </section>

                    <!-- 8 -->
                    <section>
                        <h2 class="mb-4 text-lg font-bold text-[#00617E]">
                            8. Mesures de seguretat
                        </h2>
                        <p class="text-sm leading-relaxed text-slate-600">
                            Hem adoptat les mesures tècniques i organitzatives necessàries per garantir la seguretat de les vostres dades personals i evitar-ne l'alteració, pèrdua, tractament o accés no autoritzat, tenint en compte l'estat de la tecnologia, la naturalesa de les dades i els riscos als quals estan exposades.
                        </p>
                    </section>

                    <!-- 9 -->
                    <section>
                        <h2 class="mb-4 text-lg font-bold text-[#00617E]">
                            9. Modificació de la política
                        </h2>
                        <p class="text-sm leading-relaxed text-slate-600">
                            Ens reservem el dret de modificar aquesta política per adaptar-la a novetats legislatives, jurisprudencials o de pràctica empresarial. En cas de canvis substancials, us ho notificarem per correu electrònic o mitjançant un avís destacat al web.
                        </p>
                    </section>

                </div>

                <!-- Footer note -->
                <div class="mt-12 border-t border-slate-200 pt-6 text-center text-xs text-slate-400">
                    Farmàcia Josep Soler Portell · C/Nou 22, 17600 Figueres (Girona) · josepsoler@cogfi.com
                </div>
            </div>
        </div>
    </WebAppLayout>
</template>
