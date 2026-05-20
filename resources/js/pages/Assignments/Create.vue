<script setup lang="ts">
import { Form, Link } from '@inertiajs/vue3';
import { onMounted, onUnmounted, ref } from 'vue';
import WebAppLayout from '@/layouts/WebAppLayout.vue';
import { store } from '@/routes/assignments';
import PublicFlashToast from '@/components/PublicFlashToast.vue';

const props = defineProps<{
    turnstileSiteKey: string | null;
    name: string | null;
    address: string | null;
    phone_number: number | null;
    description: string | null;
}>();

const turnstileWidgetId = ref<string | null>(null);

// Render the Turnstile widget when the script is loaded and the site key is available
function renderTurnstile() {
    const el = document.querySelector('.cf-turnstile') as HTMLElement | null;
    const t = (window as any).turnstile;

    if (!el || !props.turnstileSiteKey || !t) return;

    el.innerHTML = '';
    turnstileWidgetId.value = t.render(el, {
        sitekey: props.turnstileSiteKey,
        language: 'es',
    });
}

// On error reset the widget so the user can try again without refreshing the page
function onError() {
    const t = (window as any).turnstile;
    if (t && turnstileWidgetId.value) {
        t.reset(turnstileWidgetId.value);
    }
}

// Load Turnstile script on mount and render the widget, and remove it on unmount
onMounted(() => {
    if (!props.turnstileSiteKey) return;

    if (document.getElementById('cf-turnstile-api')) {
        renderTurnstile();
        return;
    }

    const s = document.createElement('script');
    s.id = 'cf-turnstile-api';
    s.src =
        'https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit';
    s.async = true;
    s.defer = true;
    s.onload = renderTurnstile;
    document.head.appendChild(s);
});

// Remove the Turnstile script and widget on unmount to clean up the DOM
onUnmounted(() => {
    document.getElementById('cf-turnstile-api')?.remove();
});

const formRef = ref<HTMLFormElement | null>(null);
const showModal = ref(false);
const descriptionLength = ref(0);
const focusedField = ref<string | null>(null);

// Reset form and show success modal on successful submission
function onSuccess() {
    showModal.value = true;
    formRef.value?.reset();
    descriptionLength.value = 0;
}

// Update description length counter on input
function onDescriptionInput(e: Event) {
    descriptionLength.value = (e.target as HTMLTextAreaElement).value.length;
}
</script>

<template>
    <WebAppLayout>
        <div class="relative min-h-screen overflow-hidden bg-slate-50">
            <!-- Decorative background -->
            <div
                class="pointer-events-none absolute inset-x-0 top-0 -z-0 h-[420px] bg-gradient-to-br from-[#00617E] via-[#0a7494] to-[#003d50]"
            ></div>
            <div
                class="pointer-events-none absolute inset-x-0 top-0 -z-0 h-[420px] opacity-[0.06]"
                style="
                    background-image: radial-gradient(
                        circle at 1px 1px,
                        white 1px,
                        transparent 0
                    );
                    background-size: 24px 24px;
                "
            ></div>

            <div
                class="relative z-10 mx-auto max-w-7xl px-4 pt-10 pb-16 sm:px-6 lg:px-8"
            >
                <!-- Flash message -->
                <PublicFlashToast />
                <div
                    v-if="$page.flash.message"
                    class="mb-6 flex items-start gap-3 rounded-xl border border-emerald-200 bg-white px-4 py-3 shadow-sm"
                    role="alert"
                >
                    <span
                        class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-600"
                    >
                        <svg
                            class="h-3.5 w-3.5"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="3"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <polyline points="20 6 9 17 4 12" />
                        </svg>
                    </span>
                    <p class="text-sm font-medium text-slate-700">
                        {{ $page.flash.message }}
                    </p>
                </div>

                <!-- Hero header -->
                <div
                    class="mb-10 flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between"
                >
                    <div class="max-w-2xl">
                        <h1
                            class="mt-4 text-4xl font-bold tracking-tight text-white sm:text-5xl"
                        >
                            Demana el teu encàrrec
                        </h1>
                        <p class="sm:text-l mt-3 text-base text-white/80">
                            Sol·licita el que necessitis i te'l preparem en
                            poques hores. Senzill, ràpid i sense esperes.
                        </p>
                    </div>
                    <Link
                        href="/assignments"
                        class="inline-flex shrink-0 items-center justify-center gap-2 rounded-lg bg-white/10 px-4 py-2.5 text-sm font-semibold text-white shadow-sm ring-1 ring-white/30 backdrop-blur-sm transition hover:bg-white hover:text-[#00617E] hover:ring-white focus:ring-2 focus:ring-white/60 focus:outline-none"
                    >
                        <svg
                            class="h-4 w-4"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path
                                d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"
                            />
                            <polyline points="14 2 14 8 20 8" />
                            <line x1="16" y1="13" x2="8" y2="13" />
                            <line x1="16" y1="17" x2="8" y2="17" />
                        </svg>
                        Els meus encàrrecs
                    </Link>
                </div>

                <!-- Main grid -->
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                    <!-- Form card -->
                    <div class="lg:col-span-2">
                        <div
                            class="overflow-hidden rounded-2xl bg-white shadow-xl ring-1 ring-slate-900/5"
                        >
                            <!-- Step header -->
                            <div
                                class="border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white px-6 py-5 sm:px-8"
                            >
                                <div class="flex items-center gap-4">
                                    <span
                                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-[#00617E] to-[#004e66] text-sm font-bold text-white shadow-md ring-4 shadow-[#00617E]/20 ring-[#00617E]/10"
                                    >
                                        1
                                    </span>
                                    <div>
                                        <h2
                                            class="text-lg font-semibold text-slate-900"
                                        >
                                            Detalls de l'encàrrec
                                        </h2>
                                        <p
                                            class="mt-0.5 text-sm text-slate-500"
                                        >
                                            Omple el formulari amb les teves
                                            dades. Tots els camps marcats amb *
                                            són obligatoris.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <Form
                                ref="formRef"
                                :action="store()"
                                method="POST"
                                class="space-y-6 p-6 sm:p-8"
                                @success="onSuccess"
                                @error="onError"
                                #default="{ errors, validating }"
                            >
                                <!-- Name -->
                                <div>
                                    <label
                                        for="name"
                                        class="mb-2 block text-sm font-semibold text-slate-700"
                                    >
                                        Nom complet
                                        <span class="text-rose-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <span
                                            class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400"
                                            :class="{
                                                'text-[#00617E]':
                                                    focusedField === 'name',
                                            }"
                                        >
                                            <svg
                                                class="h-4 w-4"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                            >
                                                <path
                                                    d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"
                                                />
                                                <circle cx="12" cy="7" r="4" />
                                            </svg>
                                        </span>
                                        <input
                                            id="name"
                                            type="text"
                                            name="name"
                                            autocomplete="name"
                                            placeholder="Joan Pérez"
                                            class="block w-full rounded-lg border border-slate-300 bg-white py-2.5 pr-4 pl-10 text-sm text-slate-900 placeholder-slate-400 shadow-sm transition focus:border-[#00617E] focus:ring-4 focus:ring-[#00617E]/10 focus:outline-none"
                                            :class="{
                                                'border-rose-400 ring-4 ring-rose-100':
                                                    errors.name,
                                            }"
                                            @focus="focusedField = 'name'"
                                            @blur="focusedField = null"
                                        />
                                    </div>
                                    <p
                                        v-if="errors.name"
                                        class="mt-1.5 flex items-center gap-1 text-xs text-rose-600"
                                    >
                                        <svg
                                            class="h-3.5 w-3.5 shrink-0"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        >
                                            <circle cx="12" cy="12" r="10" />
                                            <line
                                                x1="12"
                                                y1="8"
                                                x2="12"
                                                y2="12"
                                            />
                                            <line
                                                x1="12"
                                                y1="16"
                                                x2="12.01"
                                                y2="16"
                                            />
                                        </svg>
                                        {{ errors.name }}
                                    </p>
                                </div>

                                <!-- Address & phone -->
                                <div
                                    class="grid grid-cols-1 gap-5 md:grid-cols-2"
                                >
                                    <div>
                                        <label
                                            for="address"
                                            class="mb-2 block text-sm font-semibold text-slate-700"
                                        >
                                            Adreça electrònica

                                            <span class="text-rose-500">*</span>
                                        </label>
                                        <div class="relative">
                                            <span
                                                class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400"
                                                :class="{
                                                    'text-[#00617E]':
                                                        focusedField ===
                                                        'address',
                                                }"
                                            >
                                                <svg
                                                    class="h-4 w-4"
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    stroke-width="2"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                >
                                                    <rect
                                                        x="3"
                                                        y="5"
                                                        width="18"
                                                        height="14"
                                                        rx="2"
                                                    />
                                                    <path d="m3 7 9 6 9-6" />
                                                </svg>
                                            </span>
                                            <input
                                                id="address"
                                                type="text"
                                                name="address"
                                                autocomplete="email-address"
                                                placeholder="Nom@domini.com"
                                                class="block w-full rounded-lg border border-slate-300 bg-white py-2.5 pr-4 pl-10 text-sm text-slate-900 placeholder-slate-400 shadow-sm transition focus:border-[#00617E] focus:ring-4 focus:ring-[#00617E]/10 focus:outline-none"
                                                :class="{
                                                    'border-rose-400 ring-4 ring-rose-100':
                                                        errors.address,
                                                }"
                                                @focus="
                                                    focusedField = 'address'
                                                "
                                                @blur="focusedField = null"
                                            />
                                        </div>
                                        <p
                                            v-if="errors.address"
                                            class="mt-1.5 flex items-center gap-1 text-xs text-rose-600"
                                        >
                                            <svg
                                                class="h-3.5 w-3.5 shrink-0"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                            >
                                                <circle
                                                    cx="12"
                                                    cy="12"
                                                    r="10"
                                                />
                                                <line
                                                    x1="12"
                                                    y1="8"
                                                    x2="12"
                                                    y2="12"
                                                />
                                                <line
                                                    x1="12"
                                                    y1="16"
                                                    x2="12.01"
                                                    y2="16"
                                                />
                                            </svg>
                                            {{ errors.address }}
                                        </p>
                                    </div>
                                    <div>
                                        <label
                                            for="phone_number"
                                            class="mb-2 block text-sm font-semibold text-slate-700"
                                        >
                                            Telèfon
                                            <span class="text-rose-500">*</span>
                                        </label>
                                        <div class="relative">
                                            <span
                                                class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400"
                                                :class="{
                                                    'text-[#00617E]':
                                                        focusedField ===
                                                        'phone_number',
                                                }"
                                            >
                                                <svg
                                                    class="h-4 w-4"
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    stroke-width="2"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                >
                                                    <path
                                                        d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"
                                                    />
                                                </svg>
                                            </span>
                                            <input
                                                id="phone_number"
                                                type="tel"
                                                name="phone_number"
                                                autocomplete="tel"
                                                inputmode="numeric"
                                                placeholder="600 12 31 23"
                                                class="block w-full rounded-lg border border-slate-300 bg-white py-2.5 pr-4 pl-10 text-sm text-slate-900 placeholder-slate-400 shadow-sm transition focus:border-[#00617E] focus:ring-4 focus:ring-[#00617E]/10 focus:outline-none"
                                                :class="{
                                                    'border-rose-400 ring-4 ring-rose-100':
                                                        errors.phone_number,
                                                }"
                                                @focus="
                                                    focusedField =
                                                        'phone_number'
                                                "
                                                @blur="focusedField = null"
                                            />
                                        </div>
                                        <p
                                            v-if="errors.phone_number"
                                            class="mt-1.5 flex items-center gap-1 text-xs text-rose-600"
                                        >
                                            <svg
                                                class="h-3.5 w-3.5 shrink-0"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                            >
                                                <circle
                                                    cx="12"
                                                    cy="12"
                                                    r="10"
                                                />
                                                <line
                                                    x1="12"
                                                    y1="8"
                                                    x2="12"
                                                    y2="12"
                                                />
                                                <line
                                                    x1="12"
                                                    y1="16"
                                                    x2="12.01"
                                                    y2="16"
                                                />
                                            </svg>
                                            {{ errors.phone_number }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Description -->
                                <div>
                                    <div
                                        class="mb-2 flex items-center justify-between"
                                    >
                                        <label
                                            for="description"
                                            class="block text-sm font-semibold text-slate-700"
                                        >
                                            Descripció de l'encàrrec
                                            <span class="text-rose-500">*</span>
                                        </label>
                                        <span
                                            class="text-xs font-medium tabular-nums"
                                            :class="
                                                descriptionLength > 450
                                                    ? 'text-amber-600'
                                                    : 'text-slate-400'
                                            "
                                        >
                                            {{ descriptionLength }} / 500
                                        </span>
                                    </div>
                                    <textarea
                                        id="description"
                                        name="description"
                                        rows="6"
                                        maxlength="500"
                                        placeholder="Indica el producte, marca o quantitat que necessites…"
                                        class="block w-full resize-none rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 placeholder-slate-400 shadow-sm transition focus:border-[#00617E] focus:ring-4 focus:ring-[#00617E]/10 focus:outline-none"
                                        :class="{
                                            'border-rose-400 ring-4 ring-rose-100':
                                                errors.description,
                                        }"
                                        @input="onDescriptionInput"
                                    />
                                    <p
                                        v-if="errors.description"
                                        class="mt-1.5 flex items-center gap-1 text-xs text-rose-600"
                                    >
                                        <svg
                                            class="h-3.5 w-3.5 shrink-0"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        >
                                            <circle cx="12" cy="12" r="10" />
                                            <line
                                                x1="12"
                                                y1="8"
                                                x2="12"
                                                y2="12"
                                            />
                                            <line
                                                x1="12"
                                                y1="16"
                                                x2="12.01"
                                                y2="16"
                                            />
                                        </svg>
                                        {{ errors.description }}
                                    </p>
                                </div>

                                <!-- Terms -->
                                <div
                                    class="flex items-start gap-3 rounded-xl border border-slate-200 bg-slate-50/80 p-4"
                                >
                                    <input
                                        id="terms"
                                        required
                                        type="checkbox"
                                        class="mt-0.5 h-4 w-4 shrink-0 cursor-pointer rounded border-slate-300 text-[#00617E] focus:ring-2 focus:ring-[#00617E]/30"
                                    />
                                    <label
                                        for="terms"
                                        class="text-sm leading-relaxed text-slate-700"
                                    >
                                        He llegit i accepto les
                                        <a
                                            href="#"
                                            class="font-semibold text-[#00617E] underline-offset-2 hover:underline"
                                            >condicions d'ús</a
                                        >
                                        i la
                                        <a
                                            href="#"
                                            class="font-semibold text-[#00617E] underline-offset-2 hover:underline"
                                            >política de privacitat</a
                                        >.
                                    </label>
                                </div>

                                <!-- Turnstile -->
                                <div v-if="props.turnstileSiteKey">
                                    <div
                                        class="cf-turnstile"
                                        :data-sitekey="props.turnstileSiteKey"
                                        data-language="es"
                                    ></div>
                                    <p
                                        v-if="errors['cf-turnstile-response']"
                                        class="mt-1.5 flex items-center gap-1 text-xs text-rose-600"
                                    >
                                        <svg
                                            class="h-3.5 w-3.5 shrink-0"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        >
                                            <circle cx="12" cy="12" r="10" />
                                            <line
                                                x1="12"
                                                y1="8"
                                                x2="12"
                                                y2="12"
                                            />
                                            <line
                                                x1="12"
                                                y1="16"
                                                x2="12.01"
                                                y2="16"
                                            />
                                        </svg>
                                        {{ errors['cf-turnstile-response'] }}
                                    </p>
                                </div>

                                <!-- Submit -->
                                <div
                                    class="flex flex-col-reverse items-stretch gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:items-center sm:justify-between"
                                >
                                    <p
                                        class="flex items-center gap-2 text-xs text-slate-500"
                                    >
                                        <svg
                                            class="h-3.5 w-3.5 text-emerald-500"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        >
                                            <rect
                                                x="3"
                                                y="11"
                                                width="18"
                                                height="11"
                                                rx="2"
                                                ry="2"
                                            />
                                            <path
                                                d="M7 11V7a5 5 0 0 1 10 0v4"
                                            />
                                        </svg>
                                        Les teves dades estan protegides
                                    </p>
                                    <button
                                        type="submit"
                                        :disabled="validating"
                                        class="group inline-flex items-center justify-center gap-2 rounded-lg bg-gradient-to-br from-[#00617E] to-[#004e66] px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-[#00617E]/25 transition hover:shadow-xl hover:shadow-[#00617E]/30 focus:ring-4 focus:ring-[#00617E]/30 focus:outline-none disabled:cursor-not-allowed disabled:opacity-60 disabled:shadow-none"
                                    >
                                        <svg
                                            v-if="validating"
                                            class="h-4 w-4 animate-spin"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                        >
                                            <circle
                                                class="opacity-25"
                                                cx="12"
                                                cy="12"
                                                r="10"
                                                stroke="currentColor"
                                                stroke-width="4"
                                            />
                                            <path
                                                class="opacity-75"
                                                fill="currentColor"
                                                d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 00-8 8z"
                                            />
                                        </svg>
                                        {{
                                            validating
                                                ? 'Enviant…'
                                                : 'Enviar encàrrec'
                                        }}
                                        <svg
                                            v-if="!validating"
                                            class="h-4 w-4 transition group-hover:translate-x-0.5"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        >
                                            <line
                                                x1="5"
                                                y1="12"
                                                x2="19"
                                                y2="12"
                                            />
                                            <polyline
                                                points="12 5 19 12 12 19"
                                            />
                                        </svg>
                                    </button>
                                </div>
                            </Form>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <aside class="space-y-5">
                        <!-- How it works -->
                        <div
                            class="overflow-hidden rounded-2xl bg-white shadow-xl ring-1 ring-slate-900/5"
                        >
                            <div
                                class="relative h-36 overflow-hidden bg-gradient-to-br from-[#00617E] to-[#004e66]"
                            >
                                <img
                                    src="/storage/vagaro-ckWrZO5sWOQ-unsplash.webp"
                                    alt="Farmàcia Soler"
                                    loading="lazy"
                                    class="absolute inset-0 h-full w-full object-cover opacity-60"
                                />
                                <div
                                    class="absolute inset-0 bg-gradient-to-t from-[#00617E] via-[#00617E]/40 to-transparent"
                                ></div>
                                <div class="absolute right-5 bottom-4 left-5">
                                    <p
                                        class="text-xs font-semibold tracking-wider text-white/80 uppercase"
                                    >
                                        Següent pas
                                    </p>
                                    <h3 class="text-xl font-bold text-white">
                                        Com funciona
                                    </h3>
                                </div>
                            </div>
                            <div class="p-6">
                                <div class="flex gap-4">
                                    <div class="flex flex-col items-center">
                                        <span
                                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-[#00617E] to-[#004e66] text-sm font-bold text-white shadow-md ring-4 shadow-[#00617E]/20 ring-[#00617E]/10"
                                        >
                                            2
                                        </span>
                                    </div>
                                    <div class="pt-1">
                                        <h4
                                            class="text-sm font-semibold text-slate-900"
                                        >
                                            Preparem el teu encàrrec
                                        </h4>
                                        <p
                                            class="mt-1 text-sm leading-relaxed text-slate-600"
                                        >
                                            T'avisarem amb un missatge tan aviat
                                            com estigui llest per recollir.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contact card -->
                        <div
                            class="overflow-hidden rounded-2xl bg-gradient-to-br from-[#00617E] to-[#004e66] p-6 text-white shadow-xl"
                        >
                            <div class="flex items-center gap-3">
                                <span
                                    class="flex h-10 w-10 items-center justify-center rounded-full bg-white/15 backdrop-blur-sm"
                                >
                                    <svg
                                        class="h-5 w-5"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    >
                                        <path
                                            d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"
                                        />
                                    </svg>
                                </span>
                                <h3 class="text-base font-semibold">
                                    Necessites ajuda?
                                </h3>
                            </div>
                            <p class="mt-3 text-sm text-white/80">
                                Truca'ns directament o vine a la farmàcia. Estem
                                oberts de dilluns a dissabte.
                            </p>
                            <a
                                href="tel:+34972500299"
                                class="mt-4 flex items-center justify-between rounded-lg bg-white/10 px-4 py-3 text-sm font-semibold backdrop-blur-sm transition hover:bg-white hover:text-[#00617E]"
                            >
                                <span>972 50 02 99</span>
                                <svg
                                    class="h-4 w-4"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <line x1="5" y1="12" x2="19" y2="12" />
                                    <polyline points="12 5 19 12 12 19" />
                                </svg>
                            </a>
                            <div
                                class="mt-3 flex items-center gap-2 text-xs text-white/70"
                            >
                                <svg
                                    class="h-3.5 w-3.5"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <path
                                        d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"
                                    />
                                    <circle cx="12" cy="10" r="3" />
                                </svg>
                                Carrer Nou, 22 · 17600 Figueres
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </div>

        <!-- Success modal -->
        <transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="showModal"
                class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 px-4 py-6 backdrop-blur-sm"
                @click.self="showModal = false"
            >
                <transition
                    enter-active-class="transition duration-300 ease-out"
                    enter-from-class="scale-95 opacity-0"
                    enter-to-class="scale-100 opacity-100"
                >
                    <div
                        v-if="showModal"
                        class="relative w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-2xl ring-1 ring-slate-900/10"
                    >
                        <!-- Top accent -->
                        <div
                            class="h-1.5 bg-gradient-to-r from-emerald-400 via-emerald-500 to-teal-500"
                        ></div>

                        <!-- Header -->
                        <div
                            class="flex items-start justify-between px-6 pt-6 sm:px-8"
                        >
                            <div class="flex items-center gap-4">
                                <span
                                    class="relative flex h-12 w-12 items-center justify-center rounded-full bg-emerald-100"
                                >
                                    <span
                                        class="absolute inset-0 animate-ping rounded-full bg-emerald-300 opacity-30"
                                    ></span>
                                    <svg
                                        class="relative h-6 w-6 text-emerald-600"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2.5"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    >
                                        <polyline points="20 6 9 17 4 12" />
                                    </svg>
                                </span>
                                <div>
                                    <h3
                                        class="text-lg font-bold text-slate-900"
                                    >
                                        Encàrrec rebut
                                    </h3>
                                    <p class="text-sm text-slate-500">
                                        Ja l'estem processant.
                                    </p>
                                </div>
                            </div>
                            <button
                                type="button"
                                class="rounded-lg p-1.5 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
                                @click="showModal = false"
                            >
                                <svg
                                    class="h-5 w-5"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <line x1="18" y1="6" x2="6" y2="18" />
                                    <line x1="6" y1="6" x2="18" y2="18" />
                                </svg>
                                <span class="sr-only">Tancar</span>
                            </button>
                        </div>

                        <!-- Body -->
                        <div class="px-6 py-5 sm:px-8">
                            <p class="text-sm text-slate-600">
                                Confirma que aquestes dades són correctes:
                            </p>
                            <dl
                                class="mt-4 divide-y divide-slate-100 rounded-xl bg-slate-50 ring-1 ring-slate-200/60"
                            >
                                <div class="grid grid-cols-3 gap-4 px-4 py-3">
                                    <dt
                                        class="text-xs font-semibold tracking-wide text-slate-500 uppercase"
                                    >
                                        Nom
                                    </dt>
                                    <dd
                                        class="col-span-2 text-sm font-medium text-slate-900"
                                    >
                                        {{ props.name }}
                                    </dd>
                                </div>
                                <div class="grid grid-cols-3 gap-4 px-4 py-3">
                                    <dt
                                        class="text-xs font-semibold tracking-wide text-slate-500 uppercase"
                                    >
                                        Adreça
                                    </dt>
                                    <dd
                                        class="col-span-2 text-sm text-slate-900"
                                    >
                                        {{ props.address }}
                                    </dd>
                                </div>
                                <div class="grid grid-cols-3 gap-4 px-4 py-3">
                                    <dt
                                        class="text-xs font-semibold tracking-wide text-slate-500 uppercase"
                                    >
                                        Telèfon
                                    </dt>
                                    <dd
                                        class="col-span-2 text-sm text-slate-900"
                                    >
                                        {{ props.phone_number }}
                                    </dd>
                                </div>
                                <div class="grid grid-cols-3 gap-4 px-4 py-3">
                                    <dt
                                        class="text-xs font-semibold tracking-wide text-slate-500 uppercase"
                                    >
                                        Encàrrec
                                    </dt>
                                    <dd
                                        class="col-span-2 text-sm [overflow-wrap:anywhere] whitespace-pre-line text-slate-900"
                                    >
                                        {{ props.description }}
                                    </dd>
                                </div>
                            </dl>
                            <div
                                class="mt-4 flex items-start gap-2 rounded-lg bg-blue-50 px-3 py-2.5 text-xs text-blue-800 ring-1 ring-blue-100"
                            >
                                <svg
                                    class="mt-0.5 h-4 w-4 shrink-0"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <path
                                        d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"
                                    />
                                    <polyline points="22,6 12,13 2,6" />
                                </svg>
                                Rebràs una confirmació al teu correu electrònic.
                            </div>
                        </div>

                        <!-- Footer -->
                        <div
                            class="flex items-center justify-end gap-3 border-t border-slate-100 bg-slate-50/60 px-6 py-4 sm:px-8"
                        >
                            <button
                                type="button"
                                class="inline-flex items-center justify-center rounded-lg bg-gradient-to-br from-[#00617E] to-[#004e66] px-5 py-2.5 text-sm font-semibold text-white shadow-md shadow-[#00617E]/20 transition hover:shadow-lg hover:shadow-[#00617E]/30 focus:ring-4 focus:ring-[#00617E]/30 focus:outline-none"
                                @click="showModal = false"
                            >
                                Entès, gràcies
                            </button>
                        </div>
                    </div>
                </transition>
            </div>
        </transition>
    </WebAppLayout>
</template>
