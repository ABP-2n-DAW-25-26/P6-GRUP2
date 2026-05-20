<script setup lang="ts">
import { Icon } from '@iconify/vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed, onMounted, watch, nextTick, ref } from 'vue';
import WebAppLayout from '@/layouts/WebAppLayout.vue';


type Workshop = {
    id: number;
    name: string;
    description: string;
    photo_url: string | null;
    workshop_date: string;
    start_time: string;
    end_time: string;
    max_attendees: number | null;
    inscriptions_count: number;
    is_full: boolean;
};

type FlashSuccess = {
    message: string;
    name: string;
    email: string;
    workshop_name: string;
};

const props = defineProps<{
    workshop: Workshop;
    turnstileSiteKey: string | null;
}>();

const page = usePage<{
    flash?: { success?: FlashSuccess };
}>();

const flashSuccess = computed<FlashSuccess | null>(
    () => page.props.flash?.success ?? null,
);

// ---------------------------------------------------------------------------
// Date formatting
// ---------------------------------------------------------------------------
const weekDays = [
    'Diumenge', 'Dilluns', 'Dimarts', 'Dimecres',
    'Dijous', 'Divendres', 'Dissabte',
];
const months = [
    'Gener', 'Febrer', 'Març', 'Abril', 'Maig', 'Juny',
    'Juliol', 'Agost', 'Setembre', 'Octubre', 'Novembre', 'Desembre',
];

function formatDate(dateStr: string): string {
    const d = new Date(dateStr + 'T00:00:00');
    return `${weekDays[d.getDay()]}, ${d.getDate()} de ${months[d.getMonth()]}`;
}

// ---------------------------------------------------------------------------
// Inscription form
// ---------------------------------------------------------------------------
const form = useForm<{
    name: string;
    email: string;
    phone: string;
    'cf-turnstile-response': string;
}>({
    name: '',
    email: '',
    phone: '',
    'cf-turnstile-response': '',
});

function submit() {
    // Pick up the turnstile token from the hidden input rendered by the widget
    const tokenInput = document.querySelector<HTMLInputElement>(
        'input[name="cf-turnstile-response"]',
    );
    if (tokenInput) {
        form['cf-turnstile-response'] = tokenInput.value;
    }

    form.post(`/workshops/${props.workshop.id}/inscribe`, {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            // Re-render turnstile so the user gets a fresh token if they try again
            renderTurnstile();
        },
    });
}

// ---------------------------------------------------------------------------
// Turnstile loading & rendering
// ---------------------------------------------------------------------------
function renderTurnstile() {
    const el = document.querySelector('.cf-turnstile') as HTMLElement | null;
    const t = (window as { turnstile?: { render: (el: HTMLElement, opts: Record<string, unknown>) => void } }).turnstile;
    if (!el || !props.turnstileSiteKey || !t) return;
    el.innerHTML = '';
    t.render(el, { sitekey: props.turnstileSiteKey, language: 'ca' });
}

onMounted(() => {
    if (!props.turnstileSiteKey) return;

    if (document.getElementById('cf-turnstile-api')) {
        renderTurnstile();
        return;
    }

    const s = document.createElement('script');
    s.id = 'cf-turnstile-api';
    s.src = 'https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit';
    s.async = true;
    s.defer = true;
    s.onload = () => renderTurnstile();
    document.head.appendChild(s);
});

// If the flash arrives later (after the redirect), re-render the widget so
// the next submission has a fresh token
watch(flashSuccess, (v) => {
    if (v) renderTurnstile();
});


const showForm = ref(false);

function openForm() {
    showForm.value = true;
    // Wait for the form to be visible before rendering turnstile, otherwise it might not render correctly
    nextTick(() => renderTurnstile());
}

function closeForm() {
    showForm.value = false;
}
</script>

<template>

    <Head>
        <title>{{ workshop.name }}</title>
        <meta name="description"
            :content="`${workshop.name} — Taller de Farmàcia Soler. ${workshop.workshop_date}, ${workshop.start_time} – ${workshop.end_time}.`" />
    </Head>
    <WebAppLayout>
        <!-- Hero -->
        <section class="relative overflow-hidden bg-linear-to-br from-[#013F52] via-[#015873] to-[#01789E]">
            <div class="pointer-events-none absolute -top-20 -right-20 h-72 w-72 rounded-full bg-white/5 blur-3xl">
            </div>
            <div class="pointer-events-none absolute -bottom-12 -left-12 h-56 w-56 rounded-full bg-white/5 blur-3xl">
            </div>

            <div class="relative mx-auto flex max-w-7xl flex-col items-start gap-3 px-6 py-12">
                <Link href="/workshops"
                    class="flex items-center gap-1.5 text-sm text-white/60 transition hover:text-white">
                    <Icon icon="mdi:arrow-left" width="16" height="16" />
                    Tornar als tallers
                </Link>

                <div class="mt-2 flex items-center gap-2 text-white/60">
                    <Icon icon="mdi:school" width="16" height="16" />
                    <span class="text-xs font-semibold tracking-widest uppercase">Farmàcia Soler</span>
                </div>

                <h1 class="text-3xl font-bold text-white lg:text-4xl">{{ workshop.name }}</h1>

                <div class="mt-1 flex flex-wrap gap-3">
                    <span
                        class="flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1 text-sm text-white/80 backdrop-blur-sm">
                        <Icon icon="mdi:calendar" width="15" height="15" />
                        {{ formatDate(workshop.workshop_date) }}
                    </span>
                    <span
                        class="flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1 text-sm text-white/80 backdrop-blur-sm">
                        <Icon icon="mdi:clock-outline" width="15" height="15" />
                        {{ workshop.start_time }} – {{ workshop.end_time }}
                    </span>
                    <span
                        class="flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1 text-sm text-white/80 backdrop-blur-sm">
                        <Icon icon="mdi:account-group" width="15" height="15" />
                        {{
                            workshop.max_attendees
                                ? `${workshop.inscriptions_count} / ${workshop.max_attendees} places`
                                : 'Places il·limitades'
                        }}
                    </span>
                </div>
            </div>
        </section>

        <!-- Body -->
        <section class="bg-[#F2FAFF] px-4 py-12 md:px-24">
            <div class="mx-auto flex max-w-5xl flex-col gap-10 lg:flex-row">
                <!-- Photo -->
                <div class="flex-shrink-0 lg:w-72 xl:w-80">
                    <div
                        class="overflow-hidden rounded-2xl border border-[#D0EAF3] bg-white shadow-md shadow-[#01617F]/8">
                        <img v-if="workshop.photo_url" :src="workshop.photo_url" :alt="workshop.name"
                            class="h-full w-full object-contain" />
                        <div v-else
                            class="flex aspect-[3/4] flex-col items-center justify-center gap-3 bg-linear-to-br from-[#015873] to-[#01789E]">
                            <Icon icon="mdi:school" class="text-white/30" width="56" height="56" />
                            <p class="text-sm font-medium text-white/50">{{ workshop.name }}</p>
                        </div>
                    </div>
                </div>

                <!-- Info -->
                <div class="flex flex-1 flex-col gap-6">
                    <!-- Details -->
                    <div class="rounded-2xl border border-[#D0EAF3] bg-white p-6 shadow-sm">
                        <h2
                            class="mb-4 flex items-center gap-2 text-sm font-semibold tracking-widest text-[#01617F] uppercase">
                            <Icon icon="mdi:information-outline" width="16" height="16" />
                            Detalls
                        </h2>
                        <dl class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                            <div class="flex items-center gap-3">
                                <Icon icon="mdi:calendar" class="shrink-0 text-[#01617F]" width="18" height="18" />
                                <div>
                                    <dt class="text-xs text-[#335B69]/60">Data</dt>
                                    <dd class="text-sm font-medium text-[#0E3C4D]">{{ formatDate(workshop.workshop_date)
                                    }}</dd>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <Icon icon="mdi:clock-outline" class="shrink-0 text-[#01617F]" width="18" height="18" />
                                <div>
                                    <dt class="text-xs text-[#335B69]/60">Horari</dt>
                                    <dd class="text-sm font-medium text-[#0E3C4D]">{{ workshop.start_time }} – {{
                                        workshop.end_time }}</dd>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <Icon icon="mdi:account-group" class="shrink-0 text-[#01617F]" width="18" height="18" />
                                <div>
                                    <dt class="text-xs text-[#335B69]/60">Places</dt>
                                    <dd class="text-sm font-medium text-[#0E3C4D]">
                                        {{
                                            workshop.max_attendees
                                                ? `${workshop.inscriptions_count} / ${workshop.max_attendees}`
                                                : 'Il·limitades'
                                        }}
                                    </dd>
                                </div>
                            </div>
                        </dl>
                    </div>

                    <!-- Description -->
                    <div class="rounded-2xl border border-[#D0EAF3] bg-white p-6 shadow-sm">
                        <h2
                            class="mb-3 flex items-center gap-2 text-sm font-semibold tracking-widest text-[#01617F] uppercase">
                            <Icon icon="mdi:text" width="16" height="16" />
                            Descripció
                        </h2>
                        <div class="text-sm leading-relaxed text-[#335B69]" v-html="workshop.description"></div>
                    </div>

                    
                    <!-- Inscription form                                              -->
                   
                    <div class="rounded-2xl border border-[#D0EAF3] bg-white p-6 shadow-sm">
                        <h2
                            class="mb-1 flex items-center gap-2 text-sm font-semibold tracking-widest text-[#01617F] uppercase">
                            <Icon icon="mdi:account-plus" width="16" height="16" />
                            Inscriu-te al taller
                        </h2>
                        <p class="mb-4 text-xs text-[#335B69]/70">
                            {{
                                showForm
                                    ? "Omple les teves dades i et confirmarem la inscripció per correu electrònic."
                                    : "Reserva la teva plaça en un sol clic."
                            }}
                        </p>

                        <!-- Success message  -->
                        <div v-if="flashSuccess"
                            class="mb-4 flex items-start gap-3 rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-800">
                            <Icon icon="mdi:check-circle" class="mt-0.5 shrink-0 text-green-600" width="20"
                                height="20" />
                            <div>
                                <p class="font-semibold">{{ flashSuccess.message }}</p>
                                <p class="mt-1 text-xs text-green-700">
                                    {{ flashSuccess.name }} · {{ flashSuccess.email }} · {{ flashSuccess.workshop_name
                                    }}
                                </p>
                            </div>
                        </div>

                        <!-- Full notice -->
                        <div v-if="workshop.is_full"
                            class="flex items-start gap-3 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                            <Icon icon="mdi:alert-circle" class="mt-0.5 shrink-0 text-amber-600" width="20"
                                height="20" />
                            <p>Aquest taller ja està complet. Si vols, pots contactar amb nosaltres per a properes
                                edicions.</p>
                        </div>

                        <!-- STATUS 1: button to open the form-->
                        <button v-else-if="!showForm" type="button" @click="openForm"
                            class="flex w-full items-center justify-center gap-2 rounded-lg bg-[#01617F] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#01789E]">
                            <Icon icon="mdi:account-plus" width="18" height="18" />
                            Inscriu-te al taller
                        </button>

                        <!-- STATUS 2: open form -->
                        <form v-else @submit.prevent="submit" class="space-y-4" novalidate>
                            <!-- Name -->
                            <div>
                                <label for="name" class="mb-1 block text-xs font-medium text-[#0E3C4D]">
                                    Nom complet <span class="text-red-500">*</span>
                                </label>
                                <input id="name" v-model="form.name" type="text" autocomplete="name" required
                                    class="w-full rounded-lg border border-[#D0EAF3] bg-white px-3 py-2 text-sm text-[#0E3C4D] outline-none transition focus:border-[#01617F] focus:ring-2 focus:ring-[#01617F]/20"
                                    :class="{ 'border-red-400 focus:border-red-500 focus:ring-red-200': form.errors.name }" />
                                <p v-if="form.errors.name" class="mt-1 text-xs text-red-600">{{ form.errors.name }}</p>
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="mb-1 block text-xs font-medium text-[#0E3C4D]">
                                    Adreça electrònica <span class="text-red-500">*</span>
                                </label>
                                <input id="email" v-model="form.email" type="email" autocomplete="email" required
                                    class="w-full rounded-lg border border-[#D0EAF3] bg-white px-3 py-2 text-sm text-[#0E3C4D] outline-none transition focus:border-[#01617F] focus:ring-2 focus:ring-[#01617F]/20"
                                    :class="{ 'border-red-400 focus:border-red-500 focus:ring-red-200': form.errors.email }" />
                                <p v-if="form.errors.email" class="mt-1 text-xs text-red-600">{{ form.errors.email }}
                                </p>
                            </div>

                            <!-- Phone -->
                            <div>
                                <label for="phone" class="mb-1 block text-xs font-medium text-[#0E3C4D]">
                                    Telèfon (9 dígits) <span class="text-red-500">*</span>
                                </label>
                                <input id="phone" v-model="form.phone" type="tel" inputmode="numeric" pattern="[0-9]{9}"
                                    autocomplete="tel" required
                                    class="w-full rounded-lg border border-[#D0EAF3] bg-white px-3 py-2 text-sm text-[#0E3C4D] outline-none transition focus:border-[#01617F] focus:ring-2 focus:ring-[#01617F]/20"
                                    :class="{ 'border-red-400 focus:border-red-500 focus:ring-red-200': form.errors.phone }" />
                                <p v-if="form.errors.phone" class="mt-1 text-xs text-red-600">{{ form.errors.phone }}
                                </p>
                            </div>

                            <!-- Turnstile -->
                            <div v-if="turnstileSiteKey">
                                <div class="cf-turnstile" :data-sitekey="turnstileSiteKey"></div>
                                <p v-if="form.errors['cf-turnstile-response']" class="mt-1 text-xs text-red-600">
                                    {{ form.errors['cf-turnstile-response'] }}
                                </p>
                            </div>

                            <!-- Buttons: submit + cancel -->
                            <div class="flex flex-col gap-2 sm:flex-row">
                                <button type="submit" :disabled="form.processing"
                                    class="flex flex-1 items-center justify-center gap-2 rounded-lg bg-[#01617F] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#01789E] disabled:cursor-not-allowed disabled:opacity-50">
                                    <Icon :icon="form.processing ? 'mdi:loading' : 'mdi:check'"
                                        :class="{ 'animate-spin': form.processing }" width="18" height="18" />
                                    {{ form.processing ? 'Enviant…' : "Confirmar inscripció" }}
                                </button>
                                <button type="button" @click="closeForm" :disabled="form.processing"
                                    class="rounded-lg border border-[#D0EAF3] bg-white px-4 py-2.5 text-sm font-semibold text-[#01617F] transition hover:bg-[#F2FAFF] disabled:opacity-50">
                                    Cancel·lar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </WebAppLayout>
</template>