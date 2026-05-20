<script setup lang="ts">
// ---------------------------------------------------------------------------
// 1. Imports
// ---------------------------------------------------------------------------
import { useForm, usePage, router } from '@inertiajs/vue3';
import {
    ScanFace,
    Droplet,
    FlaskConical,
    ShieldCheck,
    ChevronLeft,
    ChevronRight,
    Scissors,
    TestTube,
    Apple,
    Pill,
    HeartPulse,
    Check,
} from 'lucide-vue-next';
import { ref, computed, onMounted, watch, onUnmounted } from 'vue';
import WebAppLayout from '@/layouts/WebAppLayout.vue';
import PublicFlashToast from '@/components/PublicFlashToast.vue';
import ConfirmAppointmentModal from '@/components/ConfirmAppointmentModal.vue';

// ---------------------------------------------------------------------------
// 2. Layout
// ---------------------------------------------------------------------------
defineOptions({
    layout: WebAppLayout,
});

// ---------------------------------------------------------------------------
// 3. Types
// ---------------------------------------------------------------------------
type ServiceItem = {
    id: number;
    nom: string;
    descripció: string;
    durada: number | string;
    icon: string;
};

type FlashReservation = {
    service: number;
    date: string;
    time: string;
    name: string;
    email: string;
};

type CalendarCell = {
    day: number;
    isCurrentMonth: boolean;
};

// ---------------------------------------------------------------------------
// 4. Props & Inertia page
// ---------------------------------------------------------------------------
const props = defineProps<{
    services: ServiceItem[];
    turnstileSiteKey: string | null;
}>();

const page = usePage<{
    flash?: {
        success?: string;
        error?: string;
        reservation?: FlashReservation;
    };
}>();

// ---------------------------------------------------------------------------
// 5. UI state
// ---------------------------------------------------------------------------
const showConfirmModal = ref(false);
const showModal = ref(false);
const isSubmitting = ref(false);
const step = ref(1);
const bookedTimes = ref<string[]>([]);

// ---------------------------------------------------------------------------
// 6. "Today" references
// ---------------------------------------------------------------------------
const today = new Date();
const todayYear = today.getFullYear();
const todayMonth = today.getMonth();
const todayDate = today.getDate();

// ---------------------------------------------------------------------------
// 7. User selection
// ---------------------------------------------------------------------------
const selectedService = ref<number | null>(null);
const selectedDate = ref<number | null>(todayDate);
const selectedTime = ref('');

// ---------------------------------------------------------------------------
// 8. Calendar position
// ---------------------------------------------------------------------------
const currentYear = ref(todayYear);
const currentMonth = ref(todayMonth);

// ---------------------------------------------------------------------------
// 9. Inertia form
// ---------------------------------------------------------------------------
const form = useForm<{
    service_id: number | null;
    customer_name: string;
    customer_phone: string;
    customer_email: string;
    appointment_date: string;
    start_time: string;
    'cf-turnstile-response': string;
}>({
    service_id: null,
    customer_name: '',
    customer_phone: '',
    customer_email: '',
    appointment_date: '',
    start_time: '',
    'cf-turnstile-response': '',
});

// ---------------------------------------------------------------------------
// 10. Constants
// ---------------------------------------------------------------------------
const monthNames = [
    'Gener',
    'Febrer',
    'Març',
    'Abril',
    'Maig',
    'Juny',
    'Juliol',
    'Agost',
    'Setembre',
    'Octubre',
    'Novembre',
    'Desembre',
];

const dayHeaders = ['DL', 'DT', 'DC', 'DJ', 'DV', 'DS', 'DG'];

const iconsMap: Record<string, any> = {
    ScanFace,
    Droplet,
    FlaskConical,
    ShieldCheck,
    ChevronLeft,
    ChevronRight,
    Scissors,
    TestTube,
    Apple,
    Pill,
    HeartPulse,
};

const stepTitles: Record<number, string> = {
    1: 'Servei',
    2: 'Data i hora',
    3: 'Dades personals',
};

// ---------------------------------------------------------------------------
// 11. Computed - success modal data
// ---------------------------------------------------------------------------
const successData = computed(() => page.props.flash?.reservation);

const successServiceName = computed(() => {
    if (!successData.value?.service) {
        return '—';
    }

    return (
        props.services.find((s) => s.id === successData.value?.service)?.nom ??
        '—'
    );
});

// ---------------------------------------------------------------------------
// 12. Computed - selected service / date / month label
// ---------------------------------------------------------------------------
const selectedServiceObj = computed(() =>
    props.services.find((s) => s.id === selectedService.value),
);

const formattedDate = computed(() => {
    if (!selectedDate.value) {
        return '';
    }

    return `${currentYear.value}-${String(currentMonth.value + 1).padStart(2, '0')}-${String(selectedDate.value).padStart(2, '0')}`;
});

const currentMonthLabel = computed(
    () => `${monthNames[currentMonth.value]} ${currentYear.value}`,
);

// ---------------------------------------------------------------------------
// 13. Computed - calendar grid
// ---------------------------------------------------------------------------
const calendarDays = computed<CalendarCell[]>(() => {
    const year = currentYear.value;
    const month = currentMonth.value;

    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);
    const daysInMonth = lastDay.getDate();

    let startDow = firstDay.getDay();
    startDow = (startDow + 6) % 7;

    const prevMonthLastDay = new Date(year, month, 0).getDate();
    const cells: CalendarCell[] = [];

    for (let i = startDow - 1; i >= 0; i--) {
        cells.push({ day: prevMonthLastDay - i, isCurrentMonth: false });
    }

    for (let d = 1; d <= daysInMonth; d++) {
        cells.push({ day: d, isCurrentMonth: true });
    }

    const remaining = 42 - cells.length;

    for (let d = 1; d <= remaining; d++) {
        cells.push({ day: d, isCurrentMonth: false });
    }

    return cells;
});

// ---------------------------------------------------------------------------
// 14. Schedules
// ---------------------------------------------------------------------------
const serviceSchedules = ref<{ day_of_week: number; slots: string[] }[]>([]);

async function fetchSchedule() {
    if (!selectedService.value) {
        serviceSchedules.value = [];

        return;
    }

    const res = await fetch(
        `/appointments/schedule?service_id=${selectedService.value}`,
    );
    serviceSchedules.value = await res.json();
}

const availableDaysOfWeek = computed(
    () => new Set(serviceSchedules.value.map((s) => s.day_of_week)),
);

const availableTimes = computed(() => {
    if (!selectedDate.value) {
        return [];
    }

    const date = new Date(
        currentYear.value,
        currentMonth.value,
        selectedDate.value,
    );
    const iso = date.getDay() === 0 ? 7 : date.getDay();

    const schedule = serviceSchedules.value.find((s) => s.day_of_week === iso);

    if (!schedule) {
        return [];
    }

    let slots = schedule.slots;

    const isToday =
        currentYear.value === todayYear &&
        currentMonth.value === todayMonth &&
        selectedDate.value === todayDate;

    if (isToday) {
        const now = new Date();
        const currentMinutes = now.getHours() * 60 + now.getMinutes();

        slots = slots.filter((time: string) => {
            const [h, m] = time.split(':').map(Number);

            return h * 60 + m > currentMinutes;
        });
    }

    return slots;
});

// ---------------------------------------------------------------------------
// 15. Calendar - day availability & navigation
// ---------------------------------------------------------------------------
function isDayAvailable(cell: CalendarCell): boolean {
    if (!cell.isCurrentMonth) {
        return false;
    }

    const isPast =
        currentYear.value === todayYear &&
        currentMonth.value === todayMonth &&
        cell.day < todayDate;

    if (isPast) {
        return false;
    }

    const date = new Date(currentYear.value, currentMonth.value, cell.day);
    const iso = date.getDay() === 0 ? 7 : date.getDay();

    if (!availableDaysOfWeek.value.has(iso)) {
        return false;
    }

    const isToday =
        currentYear.value === todayYear &&
        currentMonth.value === todayMonth &&
        cell.day === todayDate;

    if (isToday) {
        const schedule = serviceSchedules.value.find(
            (s) => s.day_of_week === iso,
        );

        if (!schedule || schedule.slots.length === 0) {
            return false;
        }

        const now = new Date();
        const currentMinutes = now.getHours() * 60 + now.getMinutes();

        return schedule.slots.some((time: string) => {
            const [h, m] = time.split(':').map(Number);

            return h * 60 + m > currentMinutes;
        });
    }

    return true;
}

function selectDay(cell: CalendarCell) {
    if (!isDayAvailable(cell)) {
        return;
    }

    selectedDate.value = cell.day;
    selectedTime.value = '';
}

function prevMonth() {
    const isCurrentRealMonth =
        currentYear.value === todayYear && currentMonth.value === todayMonth;

    if (isCurrentRealMonth) {
        return;
    }

    if (currentMonth.value === 0) {
        currentMonth.value = 11;
        currentYear.value--;
    } else {
        currentMonth.value--;
    }

    selectedDate.value = null;
    selectedTime.value = '';
}

function nextMonth() {
    if (currentMonth.value === 11) {
        currentMonth.value = 0;
        currentYear.value++;
    } else {
        currentMonth.value++;
    }

    selectedDate.value = null;
    selectedTime.value = '';
}

const isAtCurrentMonth = computed(
    () => currentYear.value === todayYear && currentMonth.value === todayMonth,
);

// ---------------------------------------------------------------------------
// 16. PDF download URL
// ---------------------------------------------------------------------------
// PDF link is only valid when the backend actually sent every field we need.
const hasPdfData = computed(() => {
    const d = successData.value;
    return !!(d && d.service && d.date && d.time && d.name && d.email);
});

const pdfDownloadUrl = computed(() => {
    if (!hasPdfData.value) {
        return '#';
    }

    const d = successData.value!;
    const params = new URLSearchParams({
        service: String(d.service),
        date: d.date,
        time: d.time,
        name: d.name,
        email: d.email,
    });

    return `/appointments/pdf?${params.toString()}`;
});

// ---------------------------------------------------------------------------
// 17. Booked times
// ---------------------------------------------------------------------------
async function fetchBookedTimes() {
    if (!selectedService.value || !selectedDate.value) {
        bookedTimes.value = [];

        return;
    }

    const res = await fetch(
        `/appointments/booked-times?date=${formattedDate.value}&service_id=${selectedService.value}`,
    );
    bookedTimes.value = await res.json();
}

// ---------------------------------------------------------------------------
// 18. Turnstile
// ---------------------------------------------------------------------------

const turnstileWidgetId = ref<string | null>(null);

function renderTurnstile() {
    const el = document.querySelector('.cf-turnstile') as HTMLElement | null;
    const t = (window as any).turnstile;

    if (!el || !props.turnstileSiteKey || !t) {
        return;
    }

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

// ---------------------------------------------------------------------------
// 19. Step navigation
// ---------------------------------------------------------------------------
function nextStep() {
    if (step.value < 3) {
        step.value++;
    }

    if (step.value === 3) {
        if (!props.turnstileSiteKey) {
            return;
        }

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
    }
}

function prevStep() {
    if (step.value > 1) {
        step.value--;
    }
}

/**
 * Step 1 service click.
 *
 *   1st click on a card     → just select that service
 *   2nd click on the SAME   → also advance to step 2
 *   Click on another card   → switch selection (no advance)
 */
function pickService(id: number) {
    if (selectedService.value === id) {
        nextStep();
        return;
    }

    selectedService.value = id;
}

// ---------------------------------------------------------------------------
// 20. Submit
// ---------------------------------------------------------------------------
function submitReservation() {
    form.service_id = selectedService.value;
    form.appointment_date = formattedDate.value;
    form.start_time = selectedTime.value;
    form['cf-turnstile-response'] =
        (
            document.querySelector(
                'input[name="cf-turnstile-response"]',
            ) as HTMLInputElement | null
        )?.value ?? '';

    form.post('/appointments', {
        preserveScroll: true,
        onSuccess: () => {
            isSubmitting.value = false;
            showConfirmModal.value = false;
            showModal.value = true;
        },
        onError: () => {
            isSubmitting.value = false;
            showConfirmModal.value = false;
            onError();
        },
    });
}

function confirmReservation() {
    isSubmitting.value = true;
    submitReservation();
}

function closeSuccessModal() {
    showModal.value = false;
    router.visit('/');
}

// ---------------------------------------------------------------------------
// 21. Watchers
// ---------------------------------------------------------------------------
watch(selectedService, async () => {
    selectedDate.value = null;
    selectedTime.value = '';
    bookedTimes.value = [];
    await fetchSchedule();
});

watch([selectedDate, currentMonth, currentYear], async () => {
    selectedTime.value = '';
    await fetchBookedTimes();
});

watch(
    () => page.props.flash?.reservation,
    (val) => {
        if (!val) {
            return;
        }

        showModal.value = true;
    },
);

// ---------------------------------------------------------------------------
// 22. Lifecycle
// ---------------------------------------------------------------------------
onMounted(async () => {
    if (props.services.length > 0) {
        selectedService.value = props.services[0].id;
        await fetchSchedule();
    }
});

onUnmounted(() => {
    document.getElementById('cf-turnstile-api')?.remove();
});
</script>

<template>
    <div class="relative min-h-screen overflow-hidden bg-slate-50">
        <!-- Decorative background -->
        <div
            class="pointer-events-none absolute inset-x-0 top-0 -z-0 h-[460px] bg-gradient-to-br from-[#00617E] via-[#0a7494] to-[#003d50]"
        ></div>
        <div
            class="pointer-events-none absolute inset-x-0 top-0 -z-0 h-[460px] opacity-[0.06]"
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
            class="relative z-10 mx-auto max-w-6xl px-4 pt-10 pb-16 sm:px-6 lg:px-8"
        >
            <!-- Flash message -->
            <PublicFlashToast />
            <!-- Hero -->
            <div class="mb-10 grid items-center gap-8 md:grid-cols-5">
                <div class="md:col-span-3">
                    <span
                        class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-xs font-semibold tracking-wider text-white uppercase backdrop-blur-sm"
                    >
                        <span
                            class="h-1.5 w-1.5 animate-pulse rounded-full bg-emerald-400"
                        ></span>
                        Cita prèvia en línia · Farmàcia Soler
                    </span>
                    <h1
                        class="mt-4 text-4xl font-bold tracking-tight text-white sm:text-5xl"
                    >
                        Demana la teva cita
                    </h1>
                    <p class="mt-3 text-base text-white/80 sm:text-lg">
                        Reserva els nostres serveis clínics especialitzats en
                        tres passos senzills. Atenció farmacèutica d'avantguarda
                        a Figueres.
                    </p>
                </div>
                <div class="md:col-span-2">
                    <div
                        class="relative overflow-hidden rounded-2xl shadow-2xl ring-1 ring-white/20"
                    >
                        <img
                            src="https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?q=80&w=1400&auto=format&fit=crop"
                            alt="Consulta farmacèutica"
                            class="h-44 w-full object-cover sm:h-56"
                        />
                        <div
                            class="absolute inset-0 bg-gradient-to-tr from-[#00617E]/60 via-transparent to-transparent"
                        ></div>
                        <div class="absolute right-4 bottom-4 left-4">
                            <p
                                class="text-[10px] font-semibold tracking-wider text-white/80 uppercase"
                            >
                                Centenària tradició
                            </p>
                            <p class="text-sm font-bold text-white">
                                Atenció farmacèutica a Figueres
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Wizard card -->
            <div
                class="overflow-hidden rounded-2xl bg-white shadow-2xl ring-1 ring-slate-900/5"
            >
                <!-- Progress stepper -->
                <div
                    class="border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white px-6 py-5 sm:px-10"
                >
                    <ol
                        class="flex items-center justify-between gap-2 sm:gap-4"
                    >
                        <li
                            v-for="n in [1, 2, 3]"
                            :key="n"
                            class="flex flex-1 items-center"
                        >
                            <div class="flex items-center gap-3">
                                <span
                                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-sm font-bold ring-4 transition"
                                    :class="
                                        step > n
                                            ? 'bg-emerald-500 text-white ring-emerald-100'
                                            : step === n
                                              ? 'bg-gradient-to-br from-[#00617E] to-[#004e66] text-white shadow-md shadow-[#00617E]/20 ring-[#00617E]/10'
                                              : 'bg-slate-100 text-slate-400 ring-transparent'
                                    "
                                >
                                    <Check v-if="step > n" class="h-4 w-4" />
                                    <span v-else>{{ n }}</span>
                                </span>
                                <div class="hidden sm:block">
                                    <p
                                        class="text-[10px] font-semibold tracking-wider uppercase"
                                        :class="
                                            step >= n
                                                ? 'text-[#00617E]'
                                                : 'text-slate-400'
                                        "
                                    >
                                        Pas {{ n }}
                                    </p>
                                    <p
                                        class="text-sm font-semibold"
                                        :class="
                                            step >= n
                                                ? 'text-slate-900'
                                                : 'text-slate-400'
                                        "
                                    >
                                        {{ stepTitles[n] }}
                                    </p>
                                </div>
                            </div>
                            <div
                                v-if="n < 3"
                                class="mx-3 h-px flex-1 sm:mx-4"
                                :class="
                                    step > n ? 'bg-emerald-400' : 'bg-slate-200'
                                "
                            ></div>
                        </li>
                    </ol>
                </div>

                <div class="p-6 sm:p-10">
                    <!-- ===== STEP 1 ===== -->
                    <div v-if="step === 1">
                        <h2 class="text-xl font-bold text-slate-900">
                            Quin servei necessites?
                        </h2>
                        <p class="mt-1 text-sm text-slate-500">
                            Tria un dels nostres serveis clínics per continuar.
                            <span class="text-slate-400">
                                Consell: torna a clicar el servei seleccionat
                                per passar al següent pas.
                            </span>
                        </p>

                        <div
                            class="mt-6 grid gap-4 md:grid-cols-2 lg:grid-cols-3"
                        >
                            <button
                                v-for="service in props.services"
                                :key="service.id"
                                type="button"
                                @click="pickService(service.id)"
                                :title="
                                    selectedService === service.id
                                        ? 'Torna a clicar per continuar'
                                        : 'Tria aquest servei'
                                "
                                :class="[
                                    'group relative flex flex-col items-start gap-3 rounded-xl border-2 p-5 text-left transition',
                                    selectedService === service.id
                                        ? 'border-[#00617E] bg-[#00617E]/5 shadow-md shadow-[#00617E]/10'
                                        : 'border-slate-200 bg-white hover:border-[#00617E]/40 hover:bg-slate-50',
                                ]"
                            >
                                <span
                                    v-if="selectedService === service.id"
                                    class="absolute top-3 right-3 flex h-6 w-6 items-center justify-center rounded-full bg-[#00617E] text-white"
                                >
                                    <Check class="h-3.5 w-3.5" />
                                </span>
                                <span
                                    class="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br from-[#00617E] to-[#004e66] text-white shadow-md shadow-[#00617E]/20"
                                >
                                    <component
                                        :is="iconsMap[service.icon] || 'div'"
                                        class="h-5 w-5"
                                    />
                                </span>
                                <div>
                                    <h3
                                        class="text-base font-semibold text-slate-900"
                                        v-html="service.nom"
                                    ></h3>
                                    <p
                                        class="mt-1 text-sm text-slate-500"
                                        v-html="service.descripció"
                                    ></p>
                                </div>
                                <span
                                    class="mt-auto inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium"
                                    :class="
                                        selectedService === service.id
                                            ? 'bg-[#00617E]/10 text-[#00617E]'
                                            : 'bg-slate-100 text-slate-600'
                                    "
                                >
                                    <svg
                                        class="h-3 w-3"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    >
                                        <circle cx="12" cy="12" r="10" />
                                        <polyline points="12 6 12 12 16 14" />
                                    </svg>
                                    {{ service.durada }}
                                </span>
                            </button>
                        </div>
                    </div>

                    <!-- ===== STEP 2 ===== -->
                    <div v-show="step === 2">
                        <h2 class="text-xl font-bold text-slate-900">
                            Quan et va bé?
                        </h2>
                        <p class="mt-1 text-sm text-slate-500">
                            Selecciona el dia i l'hora que prefereixis.
                        </p>

                        <div class="mt-6 grid gap-6 lg:grid-cols-2">
                            <!-- Calendar -->
                            <div
                                class="rounded-xl border border-slate-200 bg-white p-5"
                            >
                                <div
                                    class="mb-4 flex items-center justify-between"
                                >
                                    <button
                                        @click="prevMonth"
                                        :disabled="isAtCurrentMonth"
                                        type="button"
                                        aria-label="Mes anterior"
                                        class="rounded-lg p-2 text-slate-500 transition hover:bg-slate-100 hover:text-[#00617E] disabled:cursor-not-allowed disabled:opacity-30"
                                    >
                                        <ChevronLeft class="h-5 w-5" />
                                    </button>
                                    <span
                                        class="text-sm font-semibold text-slate-900"
                                        >{{ currentMonthLabel }}</span
                                    >
                                    <button
                                        @click="nextMonth"
                                        type="button"
                                        aria-label="Mes següent"
                                        class="rounded-lg p-2 text-slate-500 transition hover:bg-slate-100 hover:text-[#00617E]"
                                    >
                                        <ChevronRight class="h-5 w-5" />
                                    </button>
                                </div>

                                <div class="mb-2 grid grid-cols-7">
                                    <div
                                        v-for="h in dayHeaders"
                                        :key="h"
                                        class="py-2 text-center text-[10px] font-semibold tracking-wider text-slate-400 uppercase"
                                    >
                                        {{ h }}
                                    </div>
                                </div>

                                <div class="grid grid-cols-7 gap-1">
                                    <button
                                        v-for="(cell, idx) in calendarDays"
                                        :key="idx"
                                        @click="selectDay(cell)"
                                        :disabled="!isDayAvailable(cell)"
                                        type="button"
                                        :class="[
                                            'flex h-10 items-center justify-center rounded-lg text-sm font-medium transition',
                                            !cell.isCurrentMonth ||
                                            !isDayAvailable(cell)
                                                ? 'cursor-not-allowed text-slate-300'
                                                : selectedDate === cell.day
                                                  ? 'bg-gradient-to-br from-[#00617E] to-[#004e66] font-semibold text-white shadow-md shadow-[#00617E]/30'
                                                  : 'text-slate-700 hover:bg-[#00617E]/10 hover:text-[#00617E]',
                                        ]"
                                    >
                                        {{ cell.day }}
                                    </button>
                                </div>
                            </div>

                            <!-- Time slots -->
                            <div>
                                <div
                                    class="mb-3 flex items-center justify-between"
                                >
                                    <h3
                                        class="text-xs font-bold tracking-widest text-slate-500 uppercase"
                                    >
                                        Hores disponibles
                                    </h3>
                                    <span
                                        v-if="availableTimes.length"
                                        class="text-xs text-slate-400"
                                    >
                                        {{
                                            availableTimes.length -
                                            bookedTimes.filter((t) =>
                                                availableTimes.includes(t),
                                            ).length
                                        }}
                                        lliures
                                    </span>
                                </div>

                                <div
                                    v-if="!selectedDate"
                                    class="flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-slate-200 px-4 py-12 text-center"
                                >
                                    <svg
                                        class="h-10 w-10 text-slate-300"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.5"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    >
                                        <rect
                                            x="3"
                                            y="4"
                                            width="18"
                                            height="18"
                                            rx="2"
                                            ry="2"
                                        />
                                        <line x1="16" y1="2" x2="16" y2="6" />
                                        <line x1="8" y1="2" x2="8" y2="6" />
                                        <line x1="3" y1="10" x2="21" y2="10" />
                                    </svg>
                                    <p class="mt-3 text-sm text-slate-500">
                                        Tria un dia al calendari
                                    </p>
                                </div>

                                <div
                                    v-else-if="availableTimes.length === 0"
                                    class="flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-slate-200 px-4 py-12 text-center"
                                >
                                    <svg
                                        class="h-10 w-10 text-slate-300"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.5"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    >
                                        <circle cx="12" cy="12" r="10" />
                                        <line
                                            x1="4.93"
                                            y1="4.93"
                                            x2="19.07"
                                            y2="19.07"
                                        />
                                    </svg>
                                    <p class="mt-3 text-sm text-slate-500">
                                        Sense hores per a aquest dia
                                    </p>
                                </div>

                                <div
                                    v-else
                                    class="grid grid-cols-3 gap-2 sm:grid-cols-4"
                                >
                                    <button
                                        v-for="time in availableTimes"
                                        :key="time"
                                        @click="selectedTime = time"
                                        :disabled="bookedTimes.includes(time)"
                                        type="button"
                                        :class="[
                                            'rounded-lg border py-2.5 text-sm font-medium transition',
                                            bookedTimes.includes(time)
                                                ? 'cursor-not-allowed border-rose-200 bg-rose-50 text-rose-400 line-through'
                                                : selectedTime === time
                                                  ? 'border-[#00617E] bg-gradient-to-br from-[#00617E] to-[#004e66] text-white shadow-md shadow-[#00617E]/20'
                                                  : 'border-slate-200 bg-white text-slate-700 hover:border-[#00617E]/40 hover:bg-[#00617E]/5',
                                        ]"
                                    >
                                        {{ time }}
                                    </button>
                                </div>

                                <p
                                    v-if="form.errors.start_time"
                                    class="mt-2 flex items-center gap-1 text-xs text-rose-600"
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
                                        <line x1="12" y1="8" x2="12" y2="12" />
                                        <line
                                            x1="12"
                                            y1="16"
                                            x2="12.01"
                                            y2="16"
                                        />
                                    </svg>
                                    {{ form.errors.start_time }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- ===== STEP 3 ===== -->
                    <div v-show="step === 3">
                        <h2 class="text-xl font-bold text-slate-900">
                            Les teves dades
                        </h2>
                        <p class="mt-1 text-sm text-slate-500">
                            Necessitem aquesta informació per confirmar la teva
                            cita.
                        </p>

                        <div class="mt-6 grid gap-8 lg:grid-cols-5">
                            <!-- Form -->
                            <div class="space-y-5 lg:col-span-3">
                                <div>
                                    <label
                                        for="customer_name"
                                        class="mb-2 block text-sm font-semibold text-slate-700"
                                    >
                                        Nom i cognoms
                                        <span class="text-rose-500">*</span>
                                    </label>
                                    <input
                                        id="customer_name"
                                        v-model="form.customer_name"
                                        type="text"
                                        name="customer_name"
                                        autocomplete="name"
                                        required
                                        placeholder="Joan Pérez"
                                        class="block w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 shadow-sm transition focus:border-[#00617E] focus:ring-4 focus:ring-[#00617E]/10 focus:outline-none"
                                        :class="{
                                            'border-rose-400 ring-4 ring-rose-100':
                                                form.errors.customer_name,
                                        }"
                                    />
                                    <p
                                        v-if="form.errors.customer_name"
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
                                        {{ form.errors.customer_name }}
                                    </p>
                                </div>

                                <div
                                    class="grid grid-cols-1 gap-5 md:grid-cols-2"
                                >
                                    <div>
                                        <label
                                            for="customer_phone"
                                            class="mb-2 block text-sm font-semibold text-slate-700"
                                        >
                                            Telèfon
                                            <span class="text-rose-500">*</span>
                                        </label>
                                        <input
                                            id="customer_phone"
                                            v-model="form.customer_phone"
                                            type="tel"
                                            name="customer_phone"
                                            autocomplete="tel"
                                            pattern="[0-9]{9}"
                                            maxlength="9"
                                            required
                                            placeholder="600 000 000"
                                            class="block w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 shadow-sm transition focus:border-[#00617E] focus:ring-4 focus:ring-[#00617E]/10 focus:outline-none"
                                            :class="{
                                                'border-rose-400 ring-4 ring-rose-100':
                                                    form.errors.customer_phone,
                                            }"
                                        />
                                        <p
                                            v-if="form.errors.customer_phone"
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
                                            {{ form.errors.customer_phone }}
                                        </p>
                                    </div>

                                    <div>
                                        <label
                                            for="customer_email"
                                            class="mb-2 block text-sm font-semibold text-slate-700"
                                        >
                                            Correu electrònic
                                            <span class="text-rose-500">*</span>
                                        </label>
                                        <input
                                            id="customer_email"
                                            v-model="form.customer_email"
                                            type="email"
                                            name="customer_email"
                                            autocomplete="email"
                                            required
                                            placeholder="exemple@mail.com"
                                            class="block w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 shadow-sm transition focus:border-[#00617E] focus:ring-4 focus:ring-[#00617E]/10 focus:outline-none"
                                            :class="{
                                                'border-rose-400 ring-4 ring-rose-100':
                                                    form.errors.customer_email,
                                            }"
                                        />
                                        <p
                                            v-if="form.errors.customer_email"
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
                                            {{ form.errors.customer_email }}
                                        </p>
                                    </div>
                                </div>

                                <div v-if="props.turnstileSiteKey">
                                    <div
                                        class="cf-turnstile"
                                        :data-sitekey="props.turnstileSiteKey"
                                        data-language="es"
                                    ></div>
                                    <p
                                        v-if="
                                            form.errors['cf-turnstile-response']
                                        "
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
                                        {{
                                            form.errors['cf-turnstile-response']
                                        }}
                                    </p>
                                </div>
                            </div>

                            <!-- Summary -->
                            <aside class="lg:col-span-2">
                                <div
                                    class="overflow-hidden rounded-2xl bg-gradient-to-br from-[#00617E] to-[#004e66] text-white shadow-xl"
                                >
                                    <div class="px-5 py-4">
                                        <p
                                            class="text-xs font-semibold tracking-wider text-white/70 uppercase"
                                        >
                                            Resum de la reserva
                                        </p>
                                    </div>
                                    <dl
                                        class="space-y-3 border-t border-white/10 px-5 py-4 text-sm"
                                    >
                                        <div>
                                            <dt class="text-xs text-white/60">
                                                Servei
                                            </dt>
                                            <dd class="mt-0.5 font-semibold">
                                                {{
                                                    selectedServiceObj?.nom ||
                                                    '—'
                                                }}
                                            </dd>
                                        </div>
                                        <div>
                                            <dt class="text-xs text-white/60">
                                                Data
                                            </dt>
                                            <dd class="mt-0.5 font-semibold">
                                                {{
                                                    selectedDate
                                                        ? `${selectedDate} ${monthNames[currentMonth]} ${currentYear}`
                                                        : '—'
                                                }}
                                            </dd>
                                        </div>
                                        <div>
                                            <dt class="text-xs text-white/60">
                                                Hora
                                            </dt>
                                            <dd class="mt-0.5 font-semibold">
                                                {{ selectedTime || '—' }}
                                            </dd>
                                        </div>
                                    </dl>

                                    <div
                                        class="border-t border-white/10 px-5 py-4"
                                    >
                                        <button
                                            @click="showConfirmModal = true"
                                            :disabled="
                                                !selectedDate ||
                                                !selectedTime ||
                                                form.processing
                                            "
                                            type="button"
                                            class="w-full rounded-lg bg-white px-4 py-2.5 text-sm font-semibold text-[#00617E] shadow-md transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50"
                                        >
                                            {{
                                                form.processing
                                                    ? 'Confirmant…'
                                                    : 'Confirmar reserva'
                                            }}
                                        </button>
                                    </div>
                                </div>

                                <div
                                    class="mt-4 flex items-center gap-2 text-xs text-slate-500"
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
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                    </svg>
                                    Les teves dades estan protegides
                                </div>
                            </aside>
                        </div>
                    </div>

                    <!-- Navigation -->
                    <div
                        class="mt-10 flex items-center justify-between border-t border-slate-100 pt-6"
                    >
                        <button
                            v-if="step > 1"
                            @click="prevStep"
                            type="button"
                            class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-slate-400 hover:bg-slate-50"
                        >
                            <ChevronLeft class="h-4 w-4" />
                            Enrere
                        </button>
                        <div v-else></div>

                        <button
                            v-if="step < 3"
                            @click="nextStep"
                            :disabled="
                                (step === 1 && !selectedService) ||
                                (step === 2 && (!selectedDate || !selectedTime))
                            "
                            type="button"
                            class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-br from-[#00617E] to-[#004e66] px-6 py-2.5 text-sm font-semibold text-white shadow-lg shadow-[#00617E]/25 transition hover:shadow-xl hover:shadow-[#00617E]/30 disabled:cursor-not-allowed disabled:opacity-50 disabled:shadow-none"
                        >
                            Següent
                            <ChevronRight class="h-4 w-4" />
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== Confirm modal ===== -->
        <ConfirmAppointmentModal
            :show="showConfirmModal"
            :service-name="selectedServiceObj?.nom || '—'"
            :date="
                selectedDate
                    ? `${selectedDate} ${monthNames[currentMonth]} ${currentYear}`
                    : '—'
            "
            :time="selectedTime || '—'"
            :is-submitting="isSubmitting"
            @confirm="confirmReservation"
            @cancel="showConfirmModal = false"
        />

        <!-- ===== Success modal ===== -->
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
                <div
                    class="relative w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-2xl ring-1 ring-slate-900/10"
                >
                    <div
                        class="h-1.5 bg-gradient-to-r from-emerald-400 via-emerald-500 to-teal-500"
                    ></div>

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
                                <Check
                                    class="relative h-6 w-6 text-emerald-600"
                                />
                            </span>
                            <div>
                                <h3 class="text-lg font-bold text-slate-900">
                                    Reserva confirmada
                                </h3>
                                <p class="text-sm text-slate-500">
                                    Cita reservada correctament.
                                </p>
                            </div>
                        </div>
                        <button
                            @click="showModal = false"
                            type="button"
                            aria-label="Tancar"
                            class="rounded-lg p-1.5 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
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
                        </button>
                    </div>

                    <div class="px-6 py-5 sm:px-8">
                        <dl
                            class="divide-y divide-slate-100 rounded-xl bg-slate-50 ring-1 ring-slate-200/60"
                        >
                            <div class="grid grid-cols-3 gap-4 px-4 py-3">
                                <dt
                                    class="text-xs font-semibold tracking-wide text-slate-500 uppercase"
                                >
                                    Servei
                                </dt>
                                <dd
                                    class="col-span-2 text-sm font-medium text-slate-900"
                                >
                                    {{ successServiceName }}
                                </dd>
                            </div>
                            <div class="grid grid-cols-3 gap-4 px-4 py-3">
                                <dt
                                    class="text-xs font-semibold tracking-wide text-slate-500 uppercase"
                                >
                                    Data
                                </dt>
                                <dd class="col-span-2 text-sm text-slate-900">
                                    {{ successData?.date }}
                                </dd>
                            </div>
                            <div class="grid grid-cols-3 gap-4 px-4 py-3">
                                <dt
                                    class="text-xs font-semibold tracking-wide text-slate-500 uppercase"
                                >
                                    Hora
                                </dt>
                                <dd class="col-span-2 text-sm text-slate-900">
                                    {{ successData?.time }}
                                </dd>
                            </div>
                            <div class="grid grid-cols-3 gap-4 px-4 py-3">
                                <dt
                                    class="text-xs font-semibold tracking-wide text-slate-500 uppercase"
                                >
                                    Nom
                                </dt>
                                <dd class="col-span-2 text-sm text-slate-900">
                                    {{ successData?.name }}
                                </dd>
                            </div>
                            <div class="grid grid-cols-3 gap-4 px-4 py-3">
                                <dt
                                    class="text-xs font-semibold tracking-wide text-slate-500 uppercase"
                                >
                                    Correu
                                </dt>
                                <dd
                                    class="col-span-2 truncate text-sm text-slate-900"
                                >
                                    {{ successData?.email }}
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

                    <div
                        class="flex flex-col-reverse items-stretch gap-3 border-t border-slate-100 bg-slate-50/60 px-6 py-4 sm:flex-row sm:items-center sm:justify-end sm:px-8"
                    >
                        <button
                            @click="closeSuccessModal"
                            type="button"
                            class="rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                        >
                            Tancar
                        </button>
                        <a
                            :href="hasPdfData ? pdfDownloadUrl : undefined"
                            :target="hasPdfData ? '_blank' : undefined"
                            rel="noopener"
                            :aria-disabled="!hasPdfData"
                            :tabindex="hasPdfData ? 0 : -1"
                            :class="[
                                'inline-flex items-center justify-center gap-2 rounded-lg px-5 py-2.5 text-sm font-semibold shadow-md transition',
                                hasPdfData
                                    ? 'bg-gradient-to-br from-[#00617E] to-[#004e66] text-white shadow-[#00617E]/20 hover:shadow-lg hover:shadow-[#00617E]/30'
                                    : 'pointer-events-none cursor-not-allowed bg-slate-200 text-slate-400 shadow-none',
                            ]"
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
                                    d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"
                                />
                                <polyline points="7 10 12 15 17 10" />
                                <line x1="12" y1="15" x2="12" y2="3" />
                            </svg>
                            Descarregar PDF
                        </a>
                    </div>
                </div>
            </div>
        </transition>
    </div>
</template>
