<script setup lang="ts">
import { Icon } from '@iconify/vue';
import { Head, InfiniteScroll, Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { show as workshopShow } from '@/actions/App/Http/Controllers/public_workshops_controller';
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
    is_active: boolean;
};

const props = defineProps<{
    workshops: { data: Workshop[] };
}>();

const searchQuery = ref('');

const filteredWorkshops = computed(() => {
    const q = searchQuery.value.trim().toLowerCase();
    if (!q) return props.workshops.data;
    return props.workshops.data.filter(
        (w) =>
            w.name.toLowerCase().includes(q) ||
            w.description.toLowerCase().includes(q),
    );
});

const weekDays = [
    'Diumenge',
    'Dilluns',
    'Dimarts',
    'Dimecres',
    'Dijous',
    'Divendres',
    'Dissabte',
];
const months = [
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

function formatDate(dateStr: string): string {
    const d = new Date(dateStr + 'T00:00:00');
    return `${weekDays[d.getDay()]} ${d.getDate()} ${months[d.getMonth()]}`;
}
</script>

<template>
    <Head>
        <title>Tallers</title>
        <meta
            name="description"
            content="Descobreix els propers tallers i activitats de Farmàcia Soler. Reserva la teva plaça i aprèn sobre salut i benestar."
        />
    </Head>
    <WebAppLayout>
        <!-- Hero -->
        <section
            class="relative overflow-hidden bg-linear-to-br from-[#013F52] via-[#015873] to-[#01789E]"
        >
            <div
                class="pointer-events-none absolute -top-20 -right-20 h-72 w-72 rounded-full bg-white/5 blur-3xl"
            ></div>
            <div
                class="pointer-events-none absolute -bottom-12 -left-12 h-56 w-56 rounded-full bg-white/5 blur-3xl"
            ></div>

            <div
                class="relative mx-auto flex max-w-7xl flex-col items-center gap-4 px-6 py-16 text-center"
            >
                <div
                    class="flex items-center justify-center gap-2 text-white/60"
                >
                    <Icon icon="mdi:school" width="18" height="18" />
                    <span
                        class="text-xs font-semibold tracking-widest uppercase"
                        >Farmàcia Soler</span
                    >
                </div>
                <h1 class="text-4xl font-bold text-white lg:text-5xl">
                    Tallers i activitats
                </h1>
                <p class="max-w-lg text-base leading-relaxed text-white/70">
                    Descobreix els propers tallers i reserva la teva plaça.
                </p>

                <div class="relative mt-2 w-full max-w-sm">
                    <Icon
                        icon="mdi:magnify"
                        class="pointer-events-none absolute top-1/2 left-4 -translate-y-1/2 text-white/40"
                        width="17"
                        height="17"
                    />
                    <input
                        v-model="searchQuery"
                        type="search"
                        placeholder="Cerca un taller..."
                        class="w-full rounded-xl border border-white/20 bg-white/10 py-2.5 pr-4 pl-10 text-sm text-white placeholder-white/40 backdrop-blur-sm transition focus:border-white/40 focus:bg-white/15 focus:outline-none"
                    />
                </div>
            </div>
        </section>

        <!-- Posters grid -->
        <section class="bg-[#F2FAFF] px-4 py-12 sm:px-8 lg:px-16 xl:px-24">
            <p v-if="searchQuery" class="mb-6 text-sm text-[#335B69]">
                {{ filteredWorkshops.length }}
                {{ filteredWorkshops.length === 1 ? 'resultat' : 'resultats' }}
                per
                <span class="font-semibold">"{{ searchQuery }}"</span>
            </p>

            <InfiniteScroll
                v-if="filteredWorkshops.length > 0"
                data="workshops"
                manual
                only-next
            >
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <Link
                        v-for="workshop in filteredWorkshops"
                        :key="workshop.id"
                        :href="workshopShow(workshop).url"
                        class="group flex flex-col overflow-hidden rounded-2xl border border-[#D0EAF3] bg-white shadow-md shadow-[#01617F]/8 transition duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-[#01617F]/15"
                        as="article"
                    >
                        <!-- Poster -->
                        <div
                            class="relative aspect-[3/4] overflow-hidden bg-white"
                        >
                            <img
                                v-if="workshop.photo_url"
                                :src="workshop.photo_url"
                                :alt="workshop.name"
                                loading="lazy"
                                class="h-full w-full object-contain transition duration-500 group-hover:scale-[1.02]"
                            />
                            <div
                                v-else
                                class="flex h-full w-full flex-col items-center justify-center gap-3 bg-linear-to-br from-[#015873] to-[#01789E]"
                            >
                                <Icon
                                    icon="mdi:school"
                                    class="text-white/30"
                                    width="56"
                                    height="56"
                                />
                                <p class="text-sm font-medium text-white/50">
                                    {{ workshop.name }}
                                </p>
                            </div>
                        </div>

                        <!-- Info strip -->
                        <div
                            class="flex flex-col gap-1.5 border-t border-[#D0EAF3] bg-white px-4 py-3"
                        >
                            <p
                                class="truncate text-sm font-semibold text-[#0E3C4D]"
                            >
                                {{ workshop.name }}
                            </p>
                            <div
                                class="flex flex-wrap items-center gap-x-3 gap-y-1"
                            >
                                <span
                                    class="flex items-center gap-1 text-xs text-[#335B69]"
                                >
                                    <Icon
                                        icon="mdi:calendar"
                                        class="text-[#01617F]"
                                        width="13"
                                        height="13"
                                    />
                                    {{ formatDate(workshop.workshop_date) }}
                                </span>
                                <span
                                    class="flex items-center gap-1 text-xs text-[#335B69]"
                                >
                                    >
                                    <Icon
                                        icon="mdi:clock-outline"
                                        class="text-[#01617F]"
                                        width="13"
                                        height="13"
                                    />
                                    {{ workshop.start_time }}
                                </span>
                                <span
                                    class="flex items-center gap-1 text-xs text-[#335B69]"
                                >
                                    >
                                    <Icon
                                        icon="mdi:account-group"
                                        class="text-[#01617F]"
                                        width="13"
                                        height="13"
                                    />
                                    {{ workshop.max_attendees ?? '∞' }}
                                </span>
                                <span
                                    v-if="
                                        new Date(
                                            `${workshop.workshop_date}T${workshop.end_time}`,
                                        ) < new Date()
                                    "
                                    class="mt-0.5 ml-auto flex w-full items-center gap-1 rounded-full bg-red-50 px-2 py-0.5 text-xs font-semibold text-red-500"
                                >
                                    Finalitzat
                                </span>
                                <span
                                    v-else
                                    class="mt-0.5 ml-auto flex w-full items-center gap-1 rounded-full bg-green-50 px-2 py-0.5 text-xs font-semibold text-green-500"
                                    >Properament</span
                                >
                            </div>
                        </div>
                    </Link>
                </div>

                <template #next="{ loading, fetch, hasMore }">
                    <div v-if="hasMore" class="mt-10 flex justify-center">
                        <button
                            :disabled="loading"
                            class="inline-flex items-center gap-2 rounded-xl border border-[#D0EAF3] bg-white px-6 py-2.5 text-sm font-semibold text-[#01617F] shadow-sm transition hover:bg-[#E6F5FB] disabled:opacity-50"
                            @click="fetch"
                        >
                            <Icon
                                v-if="loading"
                                icon="mdi:loading"
                                class="animate-spin"
                                width="16"
                                height="16"
                            />
                            {{
                                loading
                                    ? 'Carregant...'
                                    : 'Carregar més tallers'
                            }}
                        </button>
                    </div>
                </template>
            </InfiniteScroll>

            <!-- Empty state -->
            <div
                v-else
                class="flex flex-col items-center justify-center gap-4 py-24 text-center"
            >
                <div
                    class="flex h-16 w-16 items-center justify-center rounded-full bg-[#D0EAF3] text-[#01617F]"
                >
                    <Icon
                        icon="mdi:calendar-blank-outline"
                        width="32"
                        height="32"
                    />
                </div>
                <p class="text-lg font-semibold text-[#0E3C4D]">
                    {{
                        searchQuery
                            ? "No s'han trobat tallers"
                            : 'Aviat hi haurà nous tallers'
                    }}
                </p>
                <p class="max-w-xs text-sm text-[#335B69]">
                    {{
                        searchQuery
                            ? 'Prova amb altres paraules clau.'
                            : 'Torna a visitar-nos pròximament per descobrir les activitats programades.'
                    }}
                </p>
                <button
                    v-if="searchQuery"
                    class="mt-1 rounded-xl border border-[#D0EAF3] px-4 py-2 text-sm font-medium text-[#01617F] transition hover:bg-[#E6F5FB]"
                    @click="searchQuery = ''"
                >
                    Esborrar cerca
                </button>
            </div>
        </section>
    </WebAppLayout>
</template>
