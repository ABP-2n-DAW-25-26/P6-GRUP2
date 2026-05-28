<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { useCookieConsent } from '@/composables/useCookieConsent';

const { bannerVisible, acceptAll, rejectNonEssential } = useCookieConsent();
</script>

<template>
    <Transition
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="translate-y-full opacity-0"
        enter-to-class="translate-y-0 opacity-100"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="translate-y-0 opacity-100"
        leave-to-class="translate-y-full opacity-0"
    >
        <div
            v-if="bannerVisible"
            class="fixed right-0 bottom-0 left-0 z-50 border-t border-slate-200 bg-white px-4 py-4 shadow-2xl sm:px-6"
        >
            <div class="mx-auto flex max-w-7xl flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm leading-relaxed text-slate-600">
                    Utilitzem cookies pròpies i de tercers per analitzar el trànsit i mostrar publicitat personalitzada.
                    Podeu acceptar-les totes o rebutjar les no essencials.
                    <Link href="/cookie-policy" class="font-semibold text-[#00617E] hover:underline">
                        Més informació
                    </Link>
                </p>
                <div class="flex shrink-0 gap-3">
                    <button
                        type="button"
                        class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 focus:ring-2 focus:ring-slate-200 focus:outline-none"
                        @click="rejectNonEssential"
                    >
                        Rebutjar no essencials
                    </button>
                    <button
                        type="button"
                        class="rounded-lg bg-[#00617E] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#004f66] focus:ring-2 focus:ring-[#00617E]/30 focus:outline-none"
                        @click="acceptAll"
                    >
                        Acceptar totes
                    </button>
                </div>
            </div>
        </div>
    </Transition>
</template>
