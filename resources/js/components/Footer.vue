<script setup lang="ts">
import { Icon } from '@iconify/vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <footer class="bg-[#00617E] text-white">
        <div class="mx-auto max-w-7xl px-6 py-12">
            <div class="grid grid-cols-1 gap-10 sm:grid-cols-2 lg:grid-cols-3">
                <!-- Brand -->
                <div class="flex flex-col gap-4">
                    <a href="/">
                        <img
                            src="/storage/FarmaciaSolerLogoTallDark.webp"
                            alt="Farmàcia Soler"
                            class="w-32"
                        />
                    </a>
                    <p class="text-sm leading-relaxed text-white/60">
                        La teva farmàcia de confiança. Atenció personalitzada,
                        productes de qualitat i serveis adaptats a les teves
                        necessitats.
                    </p>
                    <div class="flex flex-col gap-2 text-sm text-white/70">
                        <a
                            href="tel:688466225"
                            class="flex items-center gap-2 transition hover:text-white"
                        >
                            <Icon icon="mdi:phone" width="16" height="16" />
                            688 466 225
                        </a>
                        <a
                            href="https://wa.me/34688466225"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="flex items-center gap-2 transition hover:text-white"
                        >
                            <Icon
                                icon="fontisto:whatsapp"
                                width="16"
                                height="16"
                            />
                            WhatsApp
                        </a>
                    </div>
                </div>

                <!-- Navigation -->
                <div>
                    <h3
                        class="mb-4 text-xs font-semibold tracking-widest text-white/40 uppercase"
                    >
                        Navegació
                    </h3>
                    <ul class="flex flex-col gap-2 text-sm text-white/70">
                        <li>
                            <a
                                href="/#qui-som"
                                class="transition hover:text-white"
                                >Qui som</a
                            >
                        </li>
                        <li>
                            <a
                                href="/#farmacies-guardia"
                                class="transition hover:text-white"
                                >Farmàcies de guàrdia</a
                            >
                        </li>
                        <li>
                            <Link
                                href="/workshops"
                                class="transition hover:text-white"
                                >Tallers</Link
                            >
                        </li>
                        <li>
                            <Link
                                href="/appointments/create"
                                class="transition hover:text-white"
                                >Demanar cita</Link
                            >
                        </li>
                        <li>
                            <Link
                                href="/assignments/create"
                                class="transition hover:text-white"
                                >Demanar encàrrec</Link
                            >
                        </li>
                        <li>
                            <Link
                                href="/contactans"
                                class="transition hover:text-white"
                                >Contacta'ns</Link
                            >
                        </li>
                    </ul>
                </div>

                <!-- Horaris -->
                <div>
                    <h3
                        class="mb-4 text-xs font-semibold tracking-widest text-white/40 uppercase"
                    >
                        Horaris
                    </h3>
                    <ul class="flex flex-col gap-2 text-sm text-white/70">
                        <li class="flex justify-between gap-4">
                            <span>Dilluns – Dissabte</span>
                            <span class="text-white/70">8:30 – 20:30</span>
                        </li>
                        <li class="flex justify-between gap-4">
                            <span>Diumenge</span>
                            <span class="text-white/40">Tancat</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Bottom bar -->
        <div class="border-t border-white/10">
            <div
                class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-3 px-6 py-4 text-xs text-white/40 sm:flex-row"
            >
                <span>© 2026 Farmàcia Soler. Tots els drets reservats.</span>
                <div class="flex items-center gap-4">
                    <a href="#" class="transition hover:text-white"
                        >Política de privacitat</a
                    >
                    <a href="#" class="transition hover:text-white"
                        >Avís legal</a
                    >
                </div>
            </div>
        </div>
    </footer>
</template>
