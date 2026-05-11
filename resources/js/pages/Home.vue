<script setup lang="ts">
import WebAppLayout from '@/layouts/WebAppLayout.vue';
import { Icon } from '@iconify/vue';
import { onMounted, ref } from 'vue';
import Card from '@/components/Card.vue';
import 'leaflet';
import 'leaflet/dist/leaflet.css';
import Swiper from 'swiper';
import { Pagination, Autoplay } from 'swiper/modules';
import Brands from '@/components/Brands.vue';

const map = ref();
const marker = ref();

onMounted(async () => {
    const LModule = await import('leaflet');
    const L = LModule.default ?? LModule;
    const date = new Date();

    selectedDay.value = date.getDate();
    const jsDay = date.getDay(); // 0..6 (domingo..sábado)
    idWeekDay.value = jsDay === 0 ? 6 : jsDay - 1; // 0..6 (lunes..domingo)
    startWeekDay.value = idWeekDay.value;
    selectedWeekIndex.value = 0;
    idMonth.value = selectedMonth.value.num = date.getMonth();
    numYear.value = selectedYear.value = date.getFullYear();

    console.log(idMonth, selectedMonth, numYear, selectedYear);

    address.value = document.getElementById('address');
    address.value?.setAttribute(
        'href',
        'https://www.google.com/maps/search@' +
            pharmacy.lat.value +
            ',' +
            pharmacy.long.value +
            ',17z',
    );

    new Swiper('.hero-swiper', {
        modules: [Pagination, Autoplay],
        autoplay: { delay: 3000 },
        pagination: { el: '.hero-swiper-pagination' },
        loop: true,
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

    setDays();
    setMonthName();
    setSelectDate(idWeekDay.value, selectedDay.value);
});

const address = ref();

const pharmacy = {
    // Test Information
    name: ref(''),
    dia: ref(''),
    description: ref(''),
    lat: ref('42.2655267'),
    long: ref('2.9631527'),
};

// ID's
const selectedDay = ref();
const selectedYear = ref();
const idWeekDay = ref();
const startWeekDay = ref();
const selectedWeekIndex = ref(0);
const idMonth = ref();
const numYear = ref();

// Names
const nameWeekDay = [
    'Dilluns',
    'Dimarts',
    'Dimecres',
    'Dijous',
    'Divendres',
    'Dissabte',
    'Diumenge',
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

// Week Days
const week = {
    mon: ref(),
    tue: ref(),
    wed: ref(),
    thu: ref(),
    fri: ref(),
    sat: ref(),
    sun: ref(),
};
// Selected Month Info
const selectedMonth = ref({
    num: ref(),
    name: ref(''),
    double: false,
});

// Function get max day from month
function getMaxDay(): number {
    return new Date(numYear.value, idMonth.value + 1, 0).getDate();
}

/// Buttons

// Button Select Day
function setSelectDate(idWeek: number, day: number) {
    selectedDay.value = day;
    selectedWeekIndex.value = idWeek;
    idWeekDay.value = (startWeekDay.value + idWeek) % 7;
    selectedMonth.value.num =
        selectedMonth.value.double && day < 7 ? getNextMonth() : idMonth.value;
    selectedYear.value =
        selectedMonth.value.double && selectedMonth.value.num == 0
            ? numYear.value + 1
            : numYear.value;
    getPharmacyInfo(
        selectedDay.value,
        selectedMonth.value.num + 1,
        selectedYear.value,
    );
}
// Button Previous Week
function setPreviousWeek() {
    if (week.mon.value < 7) {
        setPreviousMonth();
    }
    const limitDay = getMaxDay();
    for (const [, value] of Object.entries(week)) {
        if ((value.value -= 7) < 1) {
            value.value += limitDay;
        }
        value.value == 1 ? setMonthName() : null;
    }
    if (selectedMonth.value.double || week.sun.value == limitDay) {
        setMonthName();
    }
}

// Button Next Week
function setNextWeek() {
    const limitDay = getMaxDay();
    for (const [key, value] of Object.entries(week)) {
        if ((value.value += 7) > limitDay) {
            if ((value.value -= limitDay) == 1) {
                setMonthName();
            }
        }
        if (value.value == 8 && selectedMonth.value.double) {
            setNextMonth();
            setMonthName();
        }
    }
    if (week.mon.value == 1 && week.sun.value == 7) {
        setNextMonth();
        setMonthName();
    }
}

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
        console.log(data.response);

        address.value.setAttribute(
            'href',
            'https://www.google.com/maps/@' +
                pharmacy.lat.value +
                ',' +
                pharmacy.long.value +
                ',17z',
        );
        marker.value.setLatLng([pharmacy.lat.value, pharmacy.long.value]);
        map.value.flyTo([pharmacy.lat.value, pharmacy.long.value], 19);
    } else {
        pharmacy.description.value = '';
        pharmacy.name.value = "No s'ha trobat cap farmacia.";
    }
}

// Function Get Previous Month
function getPreviousMonth(): number {
    return idMonth.value - 1 < 0 ? 11 : idMonth.value - 1;
}
// Function Get Next Month
function getNextMonth(): number {
    return idMonth.value + 1 > 11 ? 0 : idMonth.value + 1;
}
// Function Set Previous Month
function setPreviousMonth() {
    idMonth.value = getPreviousMonth();
    idMonth.value == 11 ? setPreviousYear() : null;
}
// Function Set Next Month
function setNextMonth() {
    idMonth.value = getNextMonth();
    idMonth.value == 0 ? setNextYear() : null;
}
// Function Set Previous Year
function setPreviousYear() {
    numYear.value--;
}
// Function Set Next Year
function setNextYear() {
    numYear.value++;
}
// Function to Set Days
function setDays() {
    const maxDay = getMaxDay();
    let count = 0;
    for (const [, value] of Object.entries(week)) {
        value.value = selectedDay.value + count;
        if (value.value > maxDay) {
            value.value -= maxDay;
        }
        count++;
    }
}
// Function to Set Month Name
function setMonthName() {
    if (checkDoubleMonth()) {
        selectedMonth.value.name = `${nameMonth[idMonth.value]} - ${nameMonth[getNextMonth()]}`;
        selectedMonth.value.double = true;
    } else {
        selectedMonth.value.name = `${nameMonth[idMonth.value]}`;
        selectedMonth.value.double = false;
    }
}
// Check if has days from 2 months diferently set double name
function checkDoubleMonth(): boolean {
    return week.tue.value == 1 ||
        week.wed.value == 1 ||
        week.thu.value == 1 ||
        week.fri.value == 1 ||
        week.sat.value == 1 ||
        week.sun.value == 1
        ? true
        : false;
}

function getDayButtonClass(idWeek: number, day: number): string {
    const baseClass =
        'h-14 w-12 rounded-xl text-center cursor-pointer transition flex flex-col items-center justify-center';

    if (selectedWeekIndex.value === idWeek && selectedDay.value === day) {
        return `${baseClass} bg-white text-[#01617F] shadow-md`;
    }

    return `${baseClass} text-white hover:bg-white/15`;
}
</script>

<template>
    <WebAppLayout>
        <!-- Hero -->
        <section
            class="relative overflow-hidden bg-linear-to-br from-[#013F52] via-[#015873] to-[#01789E]"
        >
            <div
                class="mx-auto flex max-w-7xl flex-col items-center gap-8 px-6 py-16 md:h-130 md:flex-row md:gap-0 md:py-0"
            >
                <!-- Left: text -->
                <div class="z-10 flex flex-col gap-6 text-white md:w-1/2">
                    <p
                        class="text-xs font-semibold tracking-[0.22em] text-white/60 uppercase"
                    >
                        Figueres · Carrer Nou, 22
                    </p>
                    <h1 class="text-4xl leading-tight font-bold lg:text-5xl">
                        La teva farmàcia<br />de confiança
                    </h1>
                    <p class="max-w-sm text-base leading-relaxed text-white/75">
                        Més de 150 anys cuidant la teva salut. Vine a
                        visitar-nos o demana la teva cita en línia.
                    </p>
                    <div class="flex flex-wrap gap-3">
                        <a
                            href="/pedir-cita"
                            class="inline-flex items-center gap-2 rounded-xl bg-white px-6 py-3 text-sm font-semibold text-[#015873] shadow-lg transition hover:bg-[#E6F5FB]"
                        >
                            <Icon
                                icon="mdi:calendar-clock"
                                width="18"
                                height="18"
                            />
                            Demanar cita
                        </a>
                        <a
                            href="/#farmacies-guardia"
                            class="inline-flex items-center gap-2 rounded-xl border border-white/30 bg-white/10 px-6 py-3 text-sm font-semibold text-white backdrop-blur-sm transition hover:bg-white/20"
                        >
                            <Icon
                                icon="mdi:hospital-building"
                                width="18"
                                height="18"
                            />
                            Farmàcies de guàrdia
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
                                src="/storage/slider1.jpeg"
                                class="h-64 w-full object-cover object-[center_35%] md:h-130"
                                alt="Farmàcia Soler"
                            />
                        </div>
                        <div class="swiper-slide">
                            <img
                                src="/storage/slider2.jpeg"
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
            id="qui-som"
            class="scroll-mt-24 bg-[#F2FAFF] px-2 py-12 md:px-24"
        >
            <Card class="flex-col bg-white md:flex-row">
                <div
                    class="flex h-110 w-full items-end overflow-hidden md:relative md:h-auto md:w-1/2"
                >
                    <img
                        src="/storage/farmaciaSolerStore.jpeg"
                        alt="Farmacia Soler"
                        class="h-full w-full rounded-xl object-cover object-bottom md:absolute"
                    />
                </div>
                <div
                    class="flex flex-col gap-4 px-8 py-8 md:h-160 md:w-1/2 md:py-16"
                >
                    <h2
                        class="text-center text-2xl font-bold md:text-left md:text-4xl"
                    >
                        ¿Quí som?
                    </h2>
                    <p>
                        Som la Farmàcia Soler de Figueres, ens trobem a la Font
                        lluminosa, Carrer Nou num. 22. Som una farmacia amb 80
                        anys d’història i amb més de 150 anys oberta. Actualment
                        som la 3a generació.
                    </p>
                </div>
            </Card>
        </section>

        <section
            id="farmacies-guardia"
            class="scroll-mt-24 bg-[#F2FAFF] px-4 pb-16 md:px-24"
        >
            <!-- Section header -->
            <div class="mb-8 text-center">
                <div
                    class="mb-3 flex items-center justify-center gap-2 text-[#01617F]"
                >
                    <Icon icon="mdi:hospital-building" width="22" height="22" />
                    <span
                        class="text-xs font-semibold tracking-widest uppercase"
                        >Servei 24h</span
                    >
                </div>
                <h2 class="text-3xl font-bold text-[#124559] md:text-4xl">
                    Farmàcies de guàrdia
                </h2>
                <p class="mt-2 text-sm text-[#3B6A7A]">
                    Consulta quina farmàcia està de guàrdia avui i obre-la al
                    mapa.
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
                            {{ numYear }}
                        </p>
                        <h3 class="mt-0.5 text-2xl font-bold">
                            {{ selectedMonth.name }}
                        </h3>
                    </div>
                    <div class="mt-4 overflow-x-auto pb-1">
                        <div
                            class="mx-auto flex min-w-max items-center justify-center gap-1.5"
                        >
                            <button
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
                            <template
                                v-for="(dayKey, idx) in [
                                    'mon',
                                    'tue',
                                    'wed',
                                    'thu',
                                    'fri',
                                    'sat',
                                    'sun',
                                ]"
                                :key="idx"
                            >
                                <button
                                    @click="
                                        setSelectDate(idx, week[dayKey].value)
                                    "
                                    :class="
                                        getDayButtonClass(
                                            idx,
                                            week[dayKey].value,
                                        )
                                    "
                                >
                                    <span
                                        class="mb-0.5 block text-[10px] font-semibold uppercase opacity-70"
                                        >{{
                                            [
                                                'Dl',
                                                'Dm',
                                                'Dc',
                                                'Dj',
                                                'Dv',
                                                'Ds',
                                                'Dg',
                                            ][idx]
                                        }}</span
                                    >
                                    <span class="block text-sm font-bold">{{
                                        week[dayKey]
                                    }}</span>
                                </button>
                            </template>
                            <button
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
                        class="flex flex-col gap-4 border-[#D0EAF3] bg-[#F7FBFE] p-6 md:w-1/3 md:border-l"
                    >
                        <!-- Pharmacy name -->
                        <div class="flex items-start gap-3">
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
                                class="text-[#01617F]"
                                width="16"
                                height="16"
                            />
                            <span>
                                <span class="font-semibold">{{
                                    nameWeekDay[idWeekDay]
                                }}</span
                                >, {{ selectedDay }}
                                {{ nameMonth[selectedMonth.num] }}
                                {{ selectedYear }}
                            </span>
                        </div>

                        <!-- Description -->
                        <p
                            class="min-h-10 text-sm leading-relaxed text-[#335B69]"
                        >
                            {{
                                pharmacy.description ||
                                'Sense informació addicional per aquest dia.'
                            }}
                        </p>

                        <!-- Address button -->
                        <a
                            id="address"
                            class="mt-auto inline-flex cursor-pointer items-center justify-center gap-2 rounded-xl bg-[#01617F] px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#014F67]"
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
