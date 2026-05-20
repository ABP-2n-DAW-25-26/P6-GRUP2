<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Icon } from '@iconify/vue';
import 'leaflet';
import 'leaflet/dist/leaflet.css';
import Swiper from 'swiper';
import { Pagination, Autoplay } from 'swiper/modules';
import { onMounted, ref } from 'vue';
import Brands from '@/components/Brands.vue';
import Card from '@/components/Card.vue';
import WebAppLayout from '@/layouts/WebAppLayout.vue';

const map = ref();
const marker = ref();

onMounted(async () => {
    const LModule = await import('leaflet');
    const L = LModule.default ?? LModule;
    const date = new Date();

    // Set Week
    selectedDate.week.value = date.getDay() - 1;
    for (let i = 0; i < week.length; i++) {
        week[i].id.value =
            selectedDate.week.value + i < 7
                ? selectedDate.week.value + i
                : selectedDate.week.value + i - 7;
    }

    // Set Day
    selectedDate.day.value = date.getDate();
    setDays();

    // Set Month
    selectedDate.month.value = setMonth.num = date.getMonth();
    setMonthName();

    // Set Year
    selectedDate.year.value = setYear.num = date.getFullYear();
    setYearName();

    /// Swiper
    new Swiper('.hero-swiper', {
        modules: [Pagination, Autoplay],
        autoplay: { delay: 3000 },
        pagination: { el: '.hero-swiper-pagination' },
    });

    // Setup Map
    map.value = L.map('map').setView(
        [pharmacy.lat.value, pharmacy.long.value],
        19,
    );
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
    }).addTo(map.value);
    marker.value = L.marker([pharmacy.lat.value, pharmacy.long.value]).addTo(
        map.value,
    );

    // Pharmacy Info
    getPharmacyInfo(
        selectedDate.day.value,
        selectedDate.month.value + 1,
        selectedDate.year.value,
    );

    // Leaflet accessibility
    document
        .querySelector('.leaflet-marker-pane > img')
        ?.setAttribute('aria-label', 'Ubicació de la Farmàcia');
    document
        .querySelector('.leaflet-control-attribution')
        ?.classList.add('!bg-[#015873]');
    document
        .querySelector('.leaflet-control-attribution > a')
        ?.classList.add('!text-white');
});

const pharmacy = {
    name: ref(''),
    dia: ref(''),
    description: ref(''),
    lat: ref('42.2655267'),
    long: ref('2.9631527'),
};

// ID's
const selectedDate = {
    week: ref(),
    day: ref(),
    month: ref(),
    year: ref(),
};
// Actual Month Info
const setMonth = { num: 0, name: ref(''), double: false };
// Actual Year Info
const setYear = { num: 0, name: ref(''), double: false };

// Names
const nameWeekDay = [
    { key: 'DL', name: 'Dilluns' },
    { key: 'DM', name: 'Dimarts' },
    { key: 'DC', name: 'Dimecres' },
    { key: 'DJ', name: 'Dijous' },
    { key: 'DV', name: 'Divendres' },
    { key: 'DS', name: 'Dissabte' },
    { key: 'DG', name: 'Diumenge' },
];
const nameMonth = [
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

// Set Week Days
const week = [
    { id: ref(), num: ref() },
    { id: ref(), num: ref() },
    { id: ref(), num: ref() },
    { id: ref(), num: ref() },
    { id: ref(), num: ref() },
    { id: ref(), num: ref() },
    { id: ref(), num: ref() },
];

/// Functions

// Function Get Pharmacy Info
async function getPharmacyInfo(day: number, month: number, year: number) {
    const reponse = await fetch(`/pharmacyguard/${year}-${month}-${day}`);
    const data = await reponse.json();
    if (data.success) {
        pharmacy.name.value = data.response.name;
        pharmacy.description.value = data.response.description;
        pharmacy.lat.value = data.response.latitude;
        pharmacy.long.value = data.response.longitude;

        marker.value.setLatLng([pharmacy.lat.value, pharmacy.long.value]);
        map.value.flyTo([pharmacy.lat.value, pharmacy.long.value], 19);
    } else {
        pharmacy.description.value = '';
        pharmacy.name.value = "No s'ha trobat cap farmacia.";
        pharmacy.lat.value = '';
        pharmacy.long.value = '';
    }
}

/// Day

// Function Set Days
function setDays() {
    const maxDay = getMaxDay();
    let count = 0;
    for (let i = 0; i < week.length; i++) {
        week[i].num.value = selectedDate.day.value + count;
        if (week[i].num.value > maxDay) {
            week[i].num.value -= maxDay;
        }
        count++;
    }
}

// Function get max day from month
function getMaxDay(): number {
    return new Date(setYear.num, setMonth.num + 1, 0).getDate();
}

/// Month

// Function Get Selected Month
function getSelectedMonth(day: number): number {
    return setMonth.double && day < 7 ? getNextMonth() : setMonth.num;
}

// Function Set Month Name
function setMonthName() {
    if (checkDoubleMonth()) {
        setMonth.name.value = `${nameMonth[setMonth.num]} - ${nameMonth[getNextMonth()]}`;
        setMonth.double = true;
    } else {
        setMonth.name.value = `${nameMonth[setMonth.num]}`;
        setMonth.double = false;
    }
}

// Function Get Previous Month
function getPreviousMonth(): number {
    return setMonth.num - 1 < 0 ? 11 : setMonth.num - 1;
}

// Function Get Next Month
function getNextMonth(): number {
    return setMonth.num + 1 > 11 ? 0 : setMonth.num + 1;
}

// Function Set Previous Month
function setPreviousMonth() {
    setMonth.num = getPreviousMonth();
    if (setMonth.num == 11) setPreviousYear();
}

// Function Set Next Month
function setNextMonth() {
    setMonth.num = getNextMonth();
    if (setMonth.num == 0) setNextYear();
}

/// Year

// Function Get Selected Year
function getSelectedYear(day: number, month: number): number {
    return setMonth.double && day < 7 && month == 0
        ? setYear.num + 1
        : setYear.num;
}

// Function Set Year Name
function setYearName() {
    if (checkDoubleYear()) {
        setYear.name.value = `${setYear.num} - ${setYear.num + 1}`;
        setYear.double = true;
    } else {
        setYear.name.value = `${setYear.num}`;
        setYear.double = false;
    }
}

// Function Set Previous Year
function setPreviousYear() {
    setYear.num--;
    setYearName();
}

// Function Set Next Year
function setNextYear() {
    setYear.num++;
    setYearName();
}

/// Checks

// Check if week has date selected
function checkDateSelected(id: number, num: number): boolean {
    return selectedDate.week.value == id &&
        selectedDate.day.value == num &&
        selectedDate.month.value ==
            (checkDoubleMonth() && num < 7 ? setMonth.num + 1 : setMonth.num) &&
        selectedDate.year.value ==
            (checkDoubleYear() && num < 7 ? setYear.num + 1 : setYear.num)
        ? true
        : false;
}

// Check if has days from 2 months diferently set double name
function checkDoubleMonth(): boolean {
    for (let i = 0; i < week.length; i++) {
        if (week[i].num.value == 1 && i != 0) {
            return true;
        }
    }
    return false;
}

// Check if has months from 2 years diferently set double name
function checkDoubleYear(): boolean {
    return checkDoubleMonth() && setMonth.num == 11 ? true : false;
}

/// Buttons

// Button Set Select Day
function setSelectDate(idWeek: number, day: number) {
    selectedDate.week.value = idWeek;
    selectedDate.day.value = day;
    selectedDate.month.value = getSelectedMonth(day);
    selectedDate.year.value = getSelectedYear(day, selectedDate.month.value);

    getPharmacyInfo(
        selectedDate.day.value,
        selectedDate.month.value + 1,
        selectedDate.year.value,
    );
}

// Button Set Previous Week
function setPreviousWeek() {
    if (week[0].num.value < 8) {
        setPreviousMonth();
    }
    const limitDay = getMaxDay();
    for (let i = 0; i < week.length; i++) {
        if ((week[i].num.value -= 7) < 1) {
            week[i].num.value += limitDay;
        }
        if (week[i].num.value == 1) {
            setMonthName();
            if (checkDoubleYear()) {
                setYearName();
            }
        }
    }
    if (setMonth.double || week[6].num.value == limitDay) {
        setMonthName();
        if (setYear.double) setYearName();
    }
}

// Button Set Next Week
function setNextWeek() {
    const limitDay = getMaxDay();
    for (let i = 0; i < week.length; i++) {
        if ((week[i].num.value += 7) > limitDay) {
            if ((week[i].num.value -= limitDay) == 1) {
                setMonthName();
                setYearName();
            }
        }
        if (week[i].num.value == 8 && setMonth.double) {
            setNextMonth();
            setMonthName();
        }
    }
    if (week[0].num.value == 1) {
        setNextMonth();
        setMonthName();
    }
}
</script>

<template>
    <Head>
        <title>Farmàcia Soler</title>
        <meta
            name="description"
            content="Farmàcia Soler — La teva farmàcia de confiança. Tallers de salut, demanar cita, encàrrecs i farmàcies de guàrdia."
        />
    </Head>
    <WebAppLayout>
        <!-- Hero -->
        <section
            class="relative overflow-hidden bg-linear-to-br from-[#013F52] via-[#015873] to-[#01789E]"
        >
            <div
                class="mx-auto flex max-w-7xl flex-col items-center gap-8 px-6 py-16 md:h-130 md:flex-row md:gap-0 md:py-0"
            >
                <!-- Left: text -->
                <div class="z-10 flex flex-col gap-6 text-white md:w-1/2 md:pr-12">
                    <p
                        class="text-xs font-semibold tracking-[0.22em] text-white/60 uppercase"
                    >
                        Figueres · Carrer Nou, 22
                    </p>
                    <h1 class="text-4xl leading-tight font-bold lg:text-5xl">
                        Som la teva farmàcia<br />de confiança
                    </h1>
                    <p class="max-w-sm text-base leading-relaxed text-white/75">
                        Amb més de vuit dècades d'història, la farmàcia continua
                        essent un referent de proximitat i servei a Figueres i a
                        la comarca, combinant experiència i atenció personalitzada.
                    </p>
                    <div class="grid grid-cols-2 gap-3">
                        <a
                            href="/appointments/create"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-white px-4 py-3 text-sm font-semibold text-[#015873] shadow-lg transition hover:bg-[#E6F5FB]"
                        >
                            📅 Demana la teva cita
                        </a>
                        <a
                            href="/workshops"
                            class="inline-flex items-center justify-center gap-2 rounded-xl border border-white/30 bg-white/10 px-4 py-3 text-sm font-semibold text-white backdrop-blur-sm transition hover:bg-white/20"
                        >
                            🎓 Tallers i esdeveniments
                        </a>
                        <a
                            href="/assignments/create"
                            class="inline-flex items-center justify-center gap-2 rounded-xl border border-white/30 bg-white/10 px-4 py-3 text-sm font-semibold text-white backdrop-blur-sm transition hover:bg-white/20"
                        >
                            📦 Fes un encàrrec
                        </a>
                        <a
                            href="/#pharmacy-guards"
                            class="inline-flex items-center justify-center gap-2 rounded-xl border border-white/30 bg-white/10 px-4 py-3 text-sm font-semibold text-white backdrop-blur-sm transition hover:bg-white/20"
                        >
                            🏥 Guàrdies avui
                        </a>
                    </div>
                </div>
                <!-- Right: slider -->
                <div
                    class="hero-swiper relative w-full overflow-hidden rounded-2xl shadow-2xl md:absolute md:right-0 md:bottom-0 md:h-130 md:w-1/2 md:rounded-none md:rounded-tl-xl"
                >
                    <div class="swiper-wrapper">
                        <div class="swiper-slide">
                            <img
                                src="/storage/slider1.webp"
                                class="h-64 w-full object-cover object-[center_35%] md:h-130"
                                alt="Farmàcia Soler"
                            />
                        </div>
                        <div class="swiper-slide">
                            <img
                                src="/storage/slider2.webp"
                                class="h-64 w-full object-cover object-[center_35%] md:h-130"
                                alt="Farmàcia Soler"
                            />
                        </div>
                    </div>
                    <div
                        class="hero-swiper-pagination absolute right-0 bottom-3 left-0 z-10 flex justify-center"
                    ></div>
                </div>
            </div>
        </section>

        <Brands />

        <section
            id="who_we_are"
            class="scroll-mt-24 bg-[#F2FAFF] px-2 py-12 md:px-24"
        >
            <Card class="flex-col bg-white md:flex-row">
                <div
                    class="flex h-110 w-full items-end overflow-hidden md:relative md:h-auto md:w-1/2"
                >
                    <img
                        src="/storage/farmaciaSolerStore.webp"
                        alt="Farmacia Soler"
                        loading="lazy"
                        class="h-full w-full rounded-xl object-cover object-bottom md:absolute"
                    />
                </div>
                <div
                    class="flex flex-col gap-4 px-8 py-8 md:h-160 md:w-1/2 md:py-16"
                >
                    <h2
                        class="text-center text-2xl font-bold md:text-left md:text-4xl"
                    >
                        Qui som?
                    </h2>
                    <p class="text-sm leading-relaxed text-[#335B69]">
                        Benvinguts a la Farmàcia Soler, un espai dedicat a la
                        salut, el benestar i l’atenció personalitzada. Som una
                        farmàcia amb més de 80 anys donant servei a Figueres i
                        l’Alt Empordà, amb tres generacions de la família Soler
                        al vostre costat.
                    </p>
                    <p class="text-sm leading-relaxed text-[#335B69]">
                        El nostre equip de farmacèutics, tècnics i auxiliars
                        treballa cada dia amb vocació de servei i compromís
                        professional per oferir un assessorament farmacèutic
                        rigorós, proper i de confiança, adaptat a les necessitats
                        de cada persona.
                    </p>
                    <p class="mt-2 text-xs font-semibold tracking-widest text-[#01617F] uppercase">
                        Equip Farmàcia Soler
                    </p>
                </div>
            </Card>
        </section>

        <section
            id="pharmacy-guards"
            class="scroll-mt-24 bg-[#F2FAFF] px-4 pb-16 md:px-24"
        >
            <!-- Section header -->
            <div class="mb-8 text-center">
                <div
                    class="mb-3 flex items-center justify-center gap-2 text-[#005b77]"
                >
                    <Icon icon="mdi:hospital-building" width="22" height="22" />
                    <span
                        class="text-xs font-semibold tracking-widest uppercase"
                    >
                        Servei 24h a figueres
                    </span>
                </div>
                <h2 class="text-3xl font-bold text-[#124559] md:text-4xl">
                    Farmàcies de guàrdia
                </h2>
                <p class="mt-2 text-sm text-[#124559]">
                    Consulta quina farmàcia està de guàrdia avui a Figueres i
                    obre-la al mapa.
                </p>
            </div>
            <div
                class="overflow-hidden rounded-2xl border border-[#D0EAF3] shadow-xl shadow-[#01617F]/10"
            >
                <!-- Calendar strip -->
                <div
                    class="bg-linear-to-r from-[#015873] to-[#01789E] px-4 py-5"
                >
                    <div class="text-center text-white">
                        <p
                            class="text-xs font-semibold tracking-[0.2em] uppercase opacity-70"
                        >
                            {{ setYear.name }}
                        </p>
                        <h3 class="mt-0.5 text-2xl font-bold">
                            {{ setMonth.name }}
                        </h3>
                    </div>
                    <div class="mt-4 overflow-x-auto pb-1">
                        <div
                            class="mx-auto flex min-w-max items-center justify-center gap-1.5"
                        >
                            <button
                                aria-label="1setmanaabans"
                                @click="setPreviousWeek()"
                                class="flex h-14 w-8 cursor-pointer items-center justify-center rounded-lg transition hover:bg-white/10"
                            >
                                <Icon
                                    icon="iconamoon:player-play"
                                    class="rotate-180 text-white/70"
                                    width="20"
                                    height="20"
                                />
                            </button>
                            <template v-for="(value, key) in week" :key="key">
                                <button
                                    @click="
                                        setSelectDate(
                                            value.id.value,
                                            value.num.value,
                                        )
                                    "
                                    :id="'week' + value.id.value"
                                    :class="
                                        checkDateSelected(
                                            value.id.value,
                                            value.num.value,
                                        )
                                            ? 'bg-white text-[#005b77]'
                                            : 'text-white hover:bg-white/15'
                                    "
                                    class="flex h-14 w-12 cursor-pointer flex-col items-center justify-center rounded-xl text-center transition"
                                >
                                    <span
                                        class="mb-0.5 block text-[10px] font-semibold uppercase opacity-70"
                                    >
                                        {{ nameWeekDay[value.id.value]?.key }}
                                    </span>
                                    <span class="block text-sm font-bold">
                                        {{ value.num.value }}
                                    </span>
                                </button>
                            </template>
                            <button
                                aria-label="1setmanadespres"
                                @click="setNextWeek()"
                                class="flex h-14 w-8 cursor-pointer items-center justify-center rounded-lg transition hover:bg-white/10"
                            >
                                <Icon
                                    icon="iconamoon:player-play"
                                    class="text-white/70"
                                    width="20"
                                    height="20"
                                />
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Map + info -->
                <div class="flex w-full flex-col bg-white md:flex-row">
                    <!-- Map -->
                    <div class="md:w-2/3">
                        <div id="map" class="z-0 h-80 w-full md:h-full"></div>
                    </div>

                    <!-- Info panel -->
                    <div
                        class="flex flex-col gap-4 border-[#D0EAF3] bg-[#F7FBFE] px-6 py-6 md:w-1/3 md:border-l"
                    >
                        <!-- Pharmacy name -->
                        <div class="flex items-center gap-3">
                            <div
                                class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#01617F]/10 text-[#01617F]"
                            >
                                <Icon
                                    icon="mdi:pharmacy"
                                    width="18"
                                    height="18"
                                />
                            </div>
                            <h4
                                class="text-lg leading-snug font-semibold text-[#0E3C4D]"
                            >
                                {{ pharmacy.name || 'Carregant farmàcia...' }}
                            </h4>
                        </div>

                        <!-- Date pill -->
                        <div
                            class="flex items-center gap-2 rounded-xl border border-[#D8EEF6] bg-white px-4 py-2.5 text-sm text-[#335B69]"
                        >
                            <Icon
                                icon="mdi:calendar"
                                class="text-[#015a75]"
                                width="16"
                                height="16"
                            />
                            <span>
                                <span class="font-semibold">{{
                                    nameWeekDay[selectedDate.week.value]?.name
                                }}</span
                                >, {{ selectedDate.day.value }}
                                {{ nameMonth[selectedDate.month.value] }}
                                {{ selectedDate.year.value }}
                            </span>
                        </div>

                        <!-- Description -->
                        <p
                            class="min-h-10 text-sm leading-relaxed text-[#335B69]"
                        >
                            {{
                                pharmacy.description.value ||
                                'Sense informació addicional per aquest dia.'
                            }}
                        </p>

                        <!-- Address button -->
                        <a
                            id="address"
                            class="mt-auto inline-flex items-center justify-center gap-2 rounded-xl px-5 py-3 text-sm font-semibold text-white shadow-sm transition"
                            :class="
                                pharmacy.lat.value === '' &&
                                pharmacy.long.value === ''
                                    ? 'cursor-not-allowed bg-gray-400 hover:bg-gray-500'
                                    : 'cursor-pointer bg-[#015a75] hover:bg-[#014F67]'
                            "
                            :href="
                                pharmacy.lat.value === '' &&
                                pharmacy.long.value === ''
                                    ? ''
                                    : 'https://www.google.com/maps/search/' +
                                      pharmacy.name.value +
                                      '/@' +
                                      pharmacy.lat.value +
                                      ',' +
                                      pharmacy.long.value +
                                      ',21z'
                            "
                        >
                            <Icon
                                icon="mdi:map-marker"
                                width="18"
                                height="18"
                            />
                            Obrir al mapa
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </WebAppLayout>
</template>
