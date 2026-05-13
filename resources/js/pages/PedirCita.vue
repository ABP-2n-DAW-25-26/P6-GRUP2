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
} from 'lucide-vue-next';
import { ref, computed, onMounted, watch, onUnmounted } from 'vue';
import WebAppLayout from '@/layouts/WebAppLayout.vue';

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

type FlashSuccess = {
    message: string;
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
        success?: FlashSuccess;
    };
}>();

// ---------------------------------------------------------------------------
// 5. UI state (modals, step, submitting flag, booked times)
// ---------------------------------------------------------------------------
const showConfirmModal = ref(false); // "Are you sure?" modal before sending
const showModal = ref(false); // "Reservation confirmed" modal after success
const isSubmitting = ref(false); // disables buttons while POST is running
const step = ref(1); // current wizard step (1, 2 or 3)
const bookedTimes = ref<string[]>([]); // slots already booked by other users

// ---------------------------------------------------------------------------
// 6. "Today" references (frozen at component creation)
//    Used to know what is past, what is today, and to enable/disable cells.
// ---------------------------------------------------------------------------
const today = new Date();
const todayYear = today.getFullYear();
const todayMonth = today.getMonth();
const todayDate = today.getDate();

// ---------------------------------------------------------------------------
// 7. User selection (service / date / time)
// ---------------------------------------------------------------------------
const selectedService = ref<number | null>(null);
const selectedDate = ref<number | null>(todayDate); // day number inside currentMonth/Year
const selectedTime = ref('');

// ---------------------------------------------------------------------------
// 8. Calendar position (month / year currently displayed)
// ---------------------------------------------------------------------------
const currentYear = ref(todayYear);
const currentMonth = ref(todayMonth);

// ---------------------------------------------------------------------------
// 9. Inertia form (sent to POST /appointments)
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
// 10. Constants (labels, headers, icon mapping)
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

// Catalan headers: Monday ---> Sunday
const dayHeaders = ['DL', 'DT', 'DC', 'DJ', 'DV', 'DS', 'DG'];

// Maps the icon name stored in DB to its lucide component
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

// ---------------------------------------------------------------------------
// 11. Computed - flash / success modal data
// ---------------------------------------------------------------------------
const successData = computed(() => page.props.flash?.success);

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
// 12. Computed - currently selected service / formatted date / month label
// ---------------------------------------------------------------------------
const selectedServiceObj = computed(() =>
    props.services.find((s) => s.id === selectedService.value),
);

// "YYYY-MM-DD" string built from currentYear/currentMonth/selectedDate
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
// 13. Computed - calendar grid (always 42 cells = 6 rows of 7 days)
//     Cells from previous / next month are marked isCurrentMonth: false.
// ---------------------------------------------------------------------------
const calendarDays = computed<CalendarCell[]>(() => {
    const year = currentYear.value;
    const month = currentMonth.value;

    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);
    const daysInMonth = lastDay.getDate();

    // JS getDay(): Sun=0..Sat=6 → shift so Monday=0..Sunday=6
    let startDow = firstDay.getDay();
    startDow = (startDow + 6) % 7;

    const prevMonthLastDay = new Date(year, month, 0).getDate();
    const cells: CalendarCell[] = [];

    // Trailing days of previous month (greyed out)
    for (let i = startDow - 1; i >= 0; i--) {
        cells.push({ day: prevMonthLastDay - i, isCurrentMonth: false });
    }

    // Days of current month
    for (let d = 1; d <= daysInMonth; d++) {
        cells.push({ day: d, isCurrentMonth: true });
    }

    // Leading days of next month to fill the 42-cell grid
    const remaining = 42 - cells.length;

    for (let d = 1; d <= remaining; d++) {
        cells.push({ day: d, isCurrentMonth: false });
    }

    return cells;
});

// ---------------------------------------------------------------------------
// 14. Schedules - fetched from backend when the selected service changes
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

// Days of week (ISO: 1=Mon..7=Sun) where the service has a schedule
const availableDaysOfWeek = computed(
    () => new Set(serviceSchedules.value.map((s) => s.day_of_week)),
);

// Slots available for the currently selected date.
// If the selected date is today, slots already past are filtered out.
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
        // Keep only slots whose start time is strictly after "now"
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

/**
 * Decides if a calendar cell can be clicked.
 *
 * Rules:
 *   - Cells outside the current month  -> not available.
 *   - Cells with a date already past   -> not available.
 *   - Cell's weekday must be scheduled -> otherwise not available.
 *   - If the cell IS today, it must still have at least one slot
 *     in the future, otherwise it is disabled too.
 *     (Next week's same weekday stays enabled normally.)
 */
function isDayAvailable(cell: CalendarCell): boolean {
    if (!cell.isCurrentMonth) {
        return false;
    }

    // Past date (same month) -> always disabled
    const isPast =
        currentYear.value === todayYear &&
        currentMonth.value === todayMonth &&
        cell.day < todayDate;

    if (isPast) {
        return false;
    }

    // Weekday must be part of the service schedule
    const date = new Date(currentYear.value, currentMonth.value, cell.day);
    const iso = date.getDay() === 0 ? 7 : date.getDay();

    if (!availableDaysOfWeek.value.has(iso)) {
        return false;
    }

    // Today-specific check: disable when all of today's slots are past
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

        // Any slot still in the future? ---> today stays enabled
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

// Go to previous month, but never before the real current month
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

// ---------------------------------------------------------------------------
// 16. PDF download URL (built from flash data after a successful reservation)
// ---------------------------------------------------------------------------
const pdfDownloadUrl = computed(() => {
    if (!successData.value) {
        return '#';
    }

    const params = new URLSearchParams({
        service: String(successData.value.service),
        date: successData.value.date,
        time: successData.value.time,
        name: successData.value.name,
        email: successData.value.email,
    });

    return `/appointments/pdf?${params.toString()}`;
});

// ---------------------------------------------------------------------------
// 17. Already booked times for the chosen (service, date)
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
// 18. Cloudflare Turnstile (captcha) - rendered on step 3
// ---------------------------------------------------------------------------
function renderTurnstile() {
    const el = document.querySelector('.cf-turnstile') as HTMLElement | null;
    const t = (window as any).turnstile;

    if (!el || !props.turnstileSiteKey || !t) {
        return;
    }

    el.innerHTML = '';
    t.render(el, { sitekey: props.turnstileSiteKey, language: 'es' });
}

// ---------------------------------------------------------------------------
// 19. Step navigation (Next / Back)
// ---------------------------------------------------------------------------
function nextStep() {
    if (step.value < 3) {
        step.value++;
    }

    // Lazy-load the Turnstile script the first time we enter step 3
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

// ---------------------------------------------------------------------------
// 20. Submit / confirm reservation
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
// 21. Watchers - keep UI state coherent when selection changes
// ---------------------------------------------------------------------------

// Service changed ---> reload its schedule and reset date/time
watch(selectedService, async () => {
    selectedDate.value = null;
    selectedTime.value = '';
    bookedTimes.value = [];
    await fetchSchedule();
});

// Date / month / year changed ---> reset time and refresh booked times
watch([selectedDate, currentMonth, currentYear], async () => {
    selectedTime.value = '';
    await fetchBookedTimes();
});

// Flash success arrived from the server ---> open the confirmation modal
watch(
    () => page.props.flash?.success,
    (val) => {
        if (!val) {
            return;
        }

        showModal.value = true;
    },
);

// ---------------------------------------------------------------------------
// 22. Lifecycle hooks
// ---------------------------------------------------------------------------
onMounted(async () => {
    // Auto-select the first service so the user sees the calendar populated
    if (props.services.length > 0) {
        selectedService.value = props.services[0].id;
        await fetchSchedule();
    }
});

onUnmounted(() => {
    // Clean up the Turnstile script when leaving the page
    const script = document.getElementById('cf-turnstile-api');

    if (script) {
        script.remove();
    }
});
</script>

<template>
    <div class="bg-[#f3f4f6] text-slate-900">
        <!-- =====================================================
             HEADER (title + hero image)
             ===================================================== -->
        <section class="py-10">
            <div
                class="mx-auto grid max-w-6xl items-center gap-8 px-6 md:grid-cols-2"
            >
                <div>
                    <span
                        class="mb-3 inline-block rounded-full bg-[#e5e7eb] px-3 py-1 text-[10px] font-semibold tracking-widest text-[#604700] uppercase"
                    >
                        CITA PRÈVIA A FIGUERES
                    </span>

                    <h1
                        class="mb-3 text-2xl font-bold text-[#0f5f7f] md:text-3xl"
                    >
                        Demanar Cita
                    </h1>

                    <p class="max-w-md text-sm leading-relaxed text-slate-500">
                        Reserva els nostres serveis clínics especialitzats amb
                        la comoditat del sistema online. La nostra centenària
                        tradició a Figueres s'uneix a la tecnologia per
                        oferir-te una atenció farmacèutica d'avantguarda.
                    </p>
                </div>

                <div>
                    <div
                        class="relative h-[180px] overflow-hidden rounded-xl shadow-md md:h-[220px]"
                    >
                        <img
                            class="h-full w-full object-cover"
                            src="https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?q=80&w=1400&auto=format&fit=crop"
                            alt="Consulta farmacèutica"
                        />
                        <div class="absolute inset-0 bg-[#0f5f7f]/10"></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- =====================================================
             WIZARD (3 steps)
             ===================================================== -->
        <section class="mx-auto max-w-7xl px-6 pb-20">
            <div class="rounded-3xl bg-white p-10 shadow-lg">
                <div>
                    <!-- ===== STEP 1 - choose service ===== -->
                    <div v-if="step === 1">
                        <!-- <div
                            v-if="$page.flash.message"
                            class="mx-auto mt-4 mb-4 w-full rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-3xl text-black"
                            role="alert"
                        >
                            <p>
                                <span class="font-medium">Creat: </span>
                                {{ $page.flash.message }}
                            </p>
                        </div> -->
                        <div class="mb-8 flex items-center gap-3">
                            <span
                                class="flex h-8 w-8 items-center justify-center rounded-full bg-[#0f5f7f] text-sm font-bold text-white"
                                >1</span
                            >
                            <h2 class="text-lg font-bold">
                                Seleccionar Servei
                            </h2>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                            <div
                                v-for="service in props.services"
                                :key="service.id"
                                @click="selectedService = service.id"
                                :class="[
                                    'cursor-pointer rounded-xl border p-4 transition',
                                    selectedService === service.id
                                        ? 'border-[#0f5f7f] bg-[#dbeaf4]'
                                        : 'border-transparent bg-[#f3f4f6] hover:border-[#9fbcd3]',
                                ]"
                            >
                                <div
                                    class="mb-2 flex items-center justify-between"
                                >
                                    <component
                                        :is="iconsMap[service.icon] || 'div'"
                                        class="h-5 w-5 text-[#0f5f7f]"
                                    />
                                    <span
                                        class="text-xs font-semibold text-[#604700]"
                                    >
                                        {{ service.durada }}
                                    </span>
                                </div>
                                <h3
                                    class="mb-1 text-base font-bold"
                                    v-html="service.nom"
                                ></h3>
                                <div
                                    class="text-sm text-slate-600"
                                    v-html="service.descripció"
                                ></div>
                            </div>
                        </div>
                    </div>

                    <!-- ===== STEP 2 - choose date & time =====
                         v-show is used so the calendar's reactive state
                         stays mounted when navigating back and forth. -->
                    <div v-show="step === 2">
                        <div class="mb-8 flex items-center gap-3">
                            <span
                                class="flex h-8 w-8 items-center justify-center rounded-full bg-[#0f5f7f] text-sm font-bold text-white"
                                >2</span
                            >
                            <h2 class="text-lg font-bold">Data i Hora</h2>
                        </div>

                        <div class="grid items-start gap-6 md:grid-cols-2">
                            <!-- Calendar -->
                            <div class="rounded-xl bg-[#f3f4f6] p-4">
                                <!-- Month navigation -->
                                <div
                                    class="mb-3 flex items-center justify-between"
                                >
                                    <button
                                        @click="prevMonth"
                                        aria-label="Mes anterior"
                                        type="button"
                                        class="rounded-lg p-1.5 text-slate-500 transition hover:bg-white hover:text-[#0f5f7f]"
                                    >
                                        <ChevronLeft class="h-5 w-5" />
                                    </button>
                                    <span
                                        class="text-sm font-semibold text-slate-700"
                                        >{{ currentMonthLabel }}</span
                                    >
                                    <button
                                        @click="nextMonth"
                                        aria-label="Mes següent"
                                        type="button"
                                        class="rounded-lg p-1.5 text-slate-500 transition hover:bg-white hover:text-[#0f5f7f]"
                                    >
                                        <ChevronRight class="h-5 w-5" />
                                    </button>
                                </div>

                                <!-- Weekday headers (DL..DG) -->
                                <div class="mb-2 grid grid-cols-7">
                                    <div
                                        v-for="h in dayHeaders"
                                        :key="h"
                                        class="py-1 text-center text-xs font-semibold text-slate-400"
                                    >
                                        {{ h }}
                                    </div>
                                </div>

                                <!-- Day cells -->
                                <div class="grid grid-cols-7 gap-1">
                                    <button
                                        v-for="(cell, idx) in calendarDays"
                                        :key="idx"
                                        @click="selectDay(cell)"
                                        :disabled="!isDayAvailable(cell)"
                                        :class="[
                                            'flex h-8 items-center justify-center rounded-lg text-xs transition',
                                            !cell.isCurrentMonth ||
                                            !isDayAvailable(cell)
                                                ? 'cursor-not-allowed text-slate-300'
                                                : selectedDate === cell.day
                                                  ? 'bg-[#0f5f7f] font-semibold text-white shadow-sm'
                                                  : 'text-slate-700 hover:bg-[#dbeaf4] hover:text-[#0f5f7f]',
                                        ]"
                                    >
                                        {{ cell.day }}
                                    </button>
                                </div>
                            </div>

                            <!-- Time-slot list -->
                            <div>
                                <h3
                                    class="mb-3 text-xs font-bold tracking-widest text-slate-500 uppercase"
                                >
                                    Hores Disponibles
                                </h3>
                                <div
                                    class="grid grid-cols-2 gap-2 sm:grid-cols-3"
                                >
                                    <button
                                        v-for="time in availableTimes"
                                        :key="time"
                                        @click="selectedTime = time"
                                        :disabled="
                                            !selectedDate ||
                                            bookedTimes.includes(time)
                                        "
                                        :class="[
                                            'rounded-lg border py-2 text-xs font-medium transition',
                                            !selectedDate
                                                ? 'cursor-not-allowed bg-gray-100 text-gray-400'
                                                : bookedTimes.includes(time)
                                                  ? 'cursor-not-allowed border-red-300 bg-red-100 text-red-500'
                                                  : selectedTime === time
                                                    ? 'border-[#0f5f7f] bg-[#0f5f7f] text-white'
                                                    : 'border-gray-200 bg-white text-slate-700 hover:border-[#0f5f7f]',
                                        ]"
                                    >
                                        {{ time }}
                                    </button>
                                </div>

                                <p
                                    v-if="form.errors.start_time"
                                    class="mt-2 text-xs text-red-500"
                                >
                                    {{ form.errors.start_time }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- ===== STEP 3 - personal data + summary + captcha ===== -->
                    <div v-show="step === 3">
                        <div class="mb-8 flex items-center gap-3">
                            <span
                                class="flex h-8 w-8 items-center justify-center rounded-full bg-[#0f5f7f] text-sm font-bold text-white"
                                >3</span
                            >
                            <h2 class="text-lg font-bold">Dades Personals</h2>
                        </div>

                        <div class="grid gap-6 md:grid-cols-2">
                            <!-- Form fields -->
                            <div class="space-y-4">
                                <div>
                                    <label
                                        for="customer_name"
                                        class="mb-1 block text-xs font-semibold text-slate-500"
                                    >
                                        Nom i Cognoms
                                    </label>
                                    <input
                                        id="customer_name"
                                        v-model="form.customer_name"
                                        type="text"
                                        name="customer_name"
                                        autocomplete="name"
                                        required
                                        class="w-full rounded-lg border border-transparent bg-[#f3f4f6] px-3 py-2.5 text-sm transition outline-none focus:bg-white focus:ring-2 focus:ring-[#0f5f7f]"
                                        placeholder="Nom i Cognoms"
                                    />
                                    <p
                                        v-if="form.errors.customer_name"
                                        class="mt-1 text-xs text-red-500"
                                    >
                                        {{ form.errors.customer_name }}
                                    </p>
                                </div>

                                <div>
                                    <label
                                        for="customer_phone"
                                        class="mb-1 block text-xs font-semibold text-slate-500"
                                    >
                                        Telèfon
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
                                        class="w-full rounded-lg border border-transparent bg-[#f3f4f6] px-3 py-2.5 text-sm transition outline-none focus:bg-white focus:ring-2 focus:ring-[#0f5f7f]"
                                        placeholder="600 000 000"
                                    />
                                    <p
                                        v-if="form.errors.customer_phone"
                                        class="mt-1 text-xs text-red-500"
                                    >
                                        {{ form.errors.customer_phone }}
                                    </p>
                                </div>

                                <div>
                                    <label
                                        for="customer_email"
                                        class="mb-1 block text-xs font-semibold text-slate-500"
                                    >
                                        Correu Electrònic
                                    </label>
                                    <input
                                        id="customer_email"
                                        v-model="form.customer_email"
                                        type="email"
                                        name="customer_email"
                                        autocomplete="email"
                                        required
                                        class="w-full rounded-lg border border-transparent bg-[#f3f4f6] px-3 py-2.5 text-sm transition outline-none focus:bg-white focus:ring-2 focus:ring-[#0f5f7f]"
                                        placeholder="exemple@mail.com"
                                    />
                                    <p
                                        v-if="form.errors.customer_email"
                                        class="mt-1 text-xs text-red-500"
                                    >
                                        {{ form.errors.customer_email }}
                                    </p>
                                </div>
                            </div>

                            <!-- Summary + submit + captcha -->
                            <div class="space-y-4">
                                <div
                                    class="rounded-xl border border-[#e5e7eb] bg-[#f3f4f6] p-4"
                                >
                                    <p
                                        class="mb-3 text-sm font-semibold text-slate-800"
                                    >
                                        Resum de la reserva
                                    </p>
                                    <div
                                        class="space-y-2 text-sm text-slate-600"
                                    >
                                        <div class="flex justify-between">
                                            <span>Servei</span>
                                            <span class="font-medium">{{
                                                selectedServiceObj?.nom || '—'
                                            }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span>Data</span>
                                            <span class="font-medium">
                                                {{
                                                    selectedDate
                                                        ? `${selectedDate} ${monthNames[currentMonth]} ${currentYear}`
                                                        : '—'
                                                }}
                                            </span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span>Hora</span>
                                            <span class="font-medium">{{
                                                selectedTime || '—'
                                            }}</span>
                                        </div>
                                    </div>
                                </div>

                                <button
                                    @click="showConfirmModal = true"
                                    :disabled="
                                        !selectedDate ||
                                        !selectedTime ||
                                        form.processing
                                    "
                                    class="w-full rounded-lg bg-[#0f5f7f] py-3 text-sm font-semibold text-white shadow-md transition-all hover:bg-[#0c4a63] disabled:opacity-50"
                                >
                                    {{
                                        form.processing
                                            ? 'Confirmant...'
                                            : 'Confirmar Reserva'
                                    }}
                                </button>

                                <!-- Turnstile widget container -->
                                <div v-if="props.turnstileSiteKey" class="pt-2">
                                    <div
                                        class="cf-turnstile"
                                        :data-sitekey="props.turnstileSiteKey"
                                        data-language="es"
                                    ></div>
                                    <p
                                        v-if="
                                            form.errors['cf-turnstile-response']
                                        "
                                        class="mt-2 text-sm text-red-600"
                                    >
                                        {{
                                            form.errors['cf-turnstile-response']
                                        }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ===== Wizard navigation buttons ===== -->
                    <div class="mt-8 flex items-center justify-between">
                        <button
                            v-if="step > 1"
                            @click="prevStep"
                            aria-label="Enrere"
                            type="button"
                            class="rounded-lg border border-[#0f5f7f] px-5 py-2.5 text-sm font-medium text-[#0f5f7f] transition hover:bg-[#e3f2f9]"
                        >
                            ← Enrere
                        </button>

                        <button
                            v-if="step < 3"
                            @click="nextStep"
                            :disabled="
                                (step === 2 && !selectedDate) ||
                                (step === 2 && !selectedTime)
                            "
                            aria-label="Següent"
                            type="button"
                            class="ml-auto rounded-lg bg-[#0f5f7f] px-5 py-2.5 text-sm font-medium text-white transition hover:bg-[#0c4a63] disabled:opacity-50"
                        >
                            Següent →
                        </button>
                    </div>
                </div>
            </div>

            <!-- =====================================================
                 MODAL - "Are you sure?" before sending
                 ===================================================== -->
            <div
                v-if="showConfirmModal"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
            >
                <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-lg">
                    <h3 class="mb-3 text-lg font-bold text-[#0f5f7f]">
                        Confirmar reserva
                    </h3>

                    <p class="mb-4 text-sm text-gray-600">
                        Estàs segur que vols confirmar aquesta reserva?
                    </p>

                    <div
                        class="mb-4 space-y-2 rounded-lg bg-gray-100 p-3 text-sm"
                    >
                        <div class="flex justify-between">
                            <span>Servei</span>
                            <span class="font-semibold">{{
                                selectedServiceObj?.nom || '—'
                            }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Data</span>
                            <span class="font-semibold">
                                {{
                                    selectedDate
                                        ? `${selectedDate} ${monthNames[currentMonth]} ${currentYear}`
                                        : '—'
                                }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span>Hora</span>
                            <span class="font-semibold">{{
                                selectedTime || '—'
                            }}</span>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <button
                            @click="showConfirmModal = false"
                            :disabled="isSubmitting"
                            class="flex-1 rounded-lg border border-gray-300 py-2 text-sm"
                        >
                            Cancel·lar
                        </button>
                        <button
                            @click="confirmReservation"
                            :disabled="isSubmitting"
                            class="flex-1 rounded-lg bg-[#0f5f7f] py-2 text-sm text-white disabled:opacity-50"
                        >
                            {{
                                isSubmitting ? 'Confirmant...' : 'Sí, confirmar'
                            }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- =====================================================
                 MODAL - Success (after server responded with flash)
                 ===================================================== -->
            <div
                v-if="showModal"
                class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60"
            >
                <div class="relative w-full max-w-md p-4">
                    <div class="rounded-lg bg-white p-6 shadow-xl">
                        <div
                            class="flex items-center justify-between border-b pb-4"
                        >
                            <h3 class="text-lg font-semibold text-[#0f5f7f]">
                                Reserva confirmada
                            </h3>
                            <button
                                @click="showModal = false"
                                aria-label="Tancar finestra"
                                type="button"
                                class="text-gray-400 transition hover:text-black"
                            >
                                ✕
                            </button>
                        </div>

                        <div class="pt-4 pb-3">
                            <p class="text-sm font-medium text-green-600">
                                {{ successData?.message }}
                            </p>
                        </div>

                        <div
                            class="mb-4 space-y-2 rounded-lg bg-gray-100 p-3 text-xs"
                        >
                            <div class="flex justify-between">
                                <span class="text-gray-500">Servei</span>
                                <span class="font-semibold text-gray-800">{{
                                    successServiceName
                                }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Data</span>
                                <span class="font-semibold text-gray-800">{{
                                    successData?.date
                                }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Hora</span>
                                <span class="font-semibold text-gray-800">{{
                                    successData?.time
                                }}</span>
                            </div>

                            <div
                                class="mt-1 space-y-2 border-t border-gray-300 pt-2"
                            >
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Nom</span>
                                    <span class="font-semibold text-gray-800">{{
                                        successData?.name
                                    }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Correu</span>
                                    <span
                                        class="max-w-[200px] truncate font-semibold text-gray-800"
                                        >{{ successData?.email }}</span
                                    >
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <a
                                :href="pdfDownloadUrl"
                                target="_blank"
                                class="flex flex-1 items-center justify-center gap-2 rounded-lg bg-[#0f5f7f] px-4 py-2.5 text-sm font-medium text-white transition hover:bg-[#0c4a63]"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-4 w-4 shrink-0"
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

                            <button
                                @click="closeSuccessModal"
                                aria-label="Tancar finestra"
                                type="button"
                                class="flex-1 rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-600 transition hover:border-gray-400 hover:bg-gray-50"
                            >
                                Tancar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>
