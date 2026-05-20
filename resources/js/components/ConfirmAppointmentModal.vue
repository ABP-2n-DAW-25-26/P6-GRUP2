<script setup lang="ts">
defineProps<{
    show: boolean;
    serviceName: string;
    date: string;
    time: string;
    isSubmitting: boolean;
}>();

const emit = defineEmits<{
    confirm: [];
    cancel: [];
}>();
</script>

<template>
    <transition
        enter-active-class="transition duration-200 ease-out"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition duration-150 ease-in"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div
            v-if="show"
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 px-4 py-6 backdrop-blur-sm"
            @click.self="!isSubmitting && emit('cancel')"
        >
            <div
                class="relative w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-2xl ring-1 ring-slate-900/10"
            >
                <div class="h-1.5 bg-gradient-to-r from-[#00617E] to-[#0a7494]"></div>

                <div class="flex items-start gap-4 px-6 pt-6 sm:px-8">
                    <span
                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-[#00617E]/10 text-[#00617E]"
                    >
                        <svg
                            class="h-6 w-6"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path d="M9 11l3 3L22 4" />
                            <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" />
                        </svg>
                    </span>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">
                            Confirmar la teva reserva
                        </h3>
                        <p class="mt-1 text-sm text-slate-500">
                            Revisa les dades abans d'enviar-les.
                        </p>
                    </div>
                </div>

                <div class="px-6 py-5 sm:px-8">
                    <dl class="divide-y divide-slate-100 rounded-xl bg-slate-50 ring-1 ring-slate-200/60">
                        <div class="grid grid-cols-3 gap-4 px-4 py-3">
                            <dt class="text-xs font-semibold tracking-wide text-slate-500 uppercase">Servei</dt>
                            <dd class="col-span-2 text-sm font-medium text-slate-900">{{ serviceName }}</dd>
                        </div>
                        <div class="grid grid-cols-3 gap-4 px-4 py-3">
                            <dt class="text-xs font-semibold tracking-wide text-slate-500 uppercase">Data</dt>
                            <dd class="col-span-2 text-sm font-medium text-slate-900">{{ date }}</dd>
                        </div>
                        <div class="grid grid-cols-3 gap-4 px-4 py-3">
                            <dt class="text-xs font-semibold tracking-wide text-slate-500 uppercase">Hora</dt>
                            <dd class="col-span-2 text-sm font-medium text-slate-900">{{ time }}</dd>
                        </div>
                    </dl>
                    <p class="mt-4 flex items-start gap-2 rounded-lg bg-slate-50 px-3 py-2 text-xs text-slate-600 ring-1 ring-slate-200/60">
                        <svg
                            class="mt-0.5 h-3.5 w-3.5 shrink-0 text-slate-400"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <circle cx="12" cy="12" r="10" />
                            <line x1="12" y1="16" x2="12" y2="12" />
                            <line x1="12" y1="8" x2="12.01" y2="8" />
                        </svg>
                        Un cop confirmada, rebràs un correu i podràs descarregar el PDF de la cita.
                    </p>
                </div>

                <div class="flex items-center justify-end gap-3 border-t border-slate-100 bg-slate-50/60 px-6 py-4 sm:px-8">
                    <button
                        type="button"
                        :disabled="isSubmitting"
                        class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 disabled:opacity-50"
                        @click="emit('cancel')"
                    >
                        Cancel·lar
                    </button>
                    <button
                        type="button"
                        :disabled="isSubmitting"
                        class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-br from-[#00617E] to-[#004e66] px-5 py-2 text-sm font-semibold text-white shadow-md shadow-[#00617E]/20 transition hover:shadow-lg hover:shadow-[#00617E]/30 disabled:opacity-50"
                        @click="emit('confirm')"
                    >
                        <svg
                            v-if="isSubmitting"
                            class="h-4 w-4 animate-spin"
                            viewBox="0 0 24 24"
                            fill="none"
                        >
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 00-8 8z" />
                        </svg>
                        {{ isSubmitting ? 'Confirmant…' : 'Sí, confirmar' }}
                    </button>
                </div>
            </div>
        </div>
    </transition>
</template>
