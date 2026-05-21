<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Trash2, Users } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import ConfirmDeleteDialog from '@/components/ConfirmDeleteDialog.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { index as workshopsIndex } from '@/routes/workshops';

type Workshop = {
    id: number;
    name: string;
    workshop_date: string;
    start_time: string;
    end_time: string;
    max_attendees: number | null;
};

type Inscription = {
    id: number;
    workshop_id: number;
    name: string;
    email: string;
    phone: string | null;
    created_at: string;
};

const props = defineProps<{
    workshop: Workshop;
    inscriptions: Inscription[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Tallers', href: workshopsIndex().url },
    {
        title: `Inscripcions: ${props.workshop.name}`,
        href: `/admin/workshops/${props.workshop.id}/inscriptions`,
    },
];

const searchQuery = ref('');
const inscriptionToDelete = ref<number | null>(null);
// PAGINATION
const currentPage = ref(1);
const perPage = 10;

const visibleInscriptions = computed(() => {
    const start = (currentPage.value - 1) * perPage;
    const end = start + perPage;

    return filteredInscriptions.value.slice(start, end);
});

const totalPages = computed(() =>
    Math.ceil(filteredInscriptions.value.length / perPage),
);

function changePage(newPage: number) {
    if (newPage < 1 || newPage > totalPages.value) return;

    currentPage.value = newPage;

    window.scrollTo({
        top: 0,
        behavior: 'smooth',
    });
}

const filteredInscriptions = computed(() => {
    const query = searchQuery.value.trim().toLowerCase();

    if (!query) {
        return props.inscriptions;
    }

    return props.inscriptions.filter((inscription) => {
        return (
            inscription.name.toLowerCase().includes(query) ||
            inscription.email.toLowerCase().includes(query) ||
            (inscription.phone ?? '').toLowerCase().includes(query)
        );
    });
});
watch(searchQuery, () => {
    currentPage.value = 1;
});
function askDelete(id: number) {
    inscriptionToDelete.value = id;
}

function cancelDelete() {
    inscriptionToDelete.value = null;
}

function confirmDelete() {
    if (inscriptionToDelete.value === null) {
        return;
    }

    const url = `/admin/workshops/${props.workshop.id}/inscriptions/${inscriptionToDelete.value}`;

    useForm({}).delete(url, {
        preserveScroll: true,
        onFinish: () => {
            inscriptionToDelete.value = null;
        },
    });
}

const formatTime = (time: string) => (time ? time.slice(0, 5) : '');

const formatDate = (date: string) =>
    new Date(date).toLocaleDateString('ca-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
    });

const formatDateTime = (date: string) =>
    new Date(date).toLocaleString('ca-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">

        <Head :title="`Inscripcions · ${workshop.name}`" />

        <div class="relative flex h-full flex-1 flex-col gap-6 overflow-x-auto p-4 md:p-6">
            <!-- Decorative blurred gradients -->
            <div
                class="pointer-events-none absolute top-0 right-8 h-56 w-56 rounded-full bg-gradient-to-br from-primary/20 to-muted/70 blur-3xl">
            </div>
            <div
                class="pointer-events-none absolute bottom-10 left-0 h-64 w-64 rounded-full bg-gradient-to-tr from-secondary/60 to-primary/10 blur-3xl">
            </div>

            <!-- Header -->
            <div
                class="relative overflow-hidden rounded-2xl border border-sidebar-border/70 bg-gradient-to-br from-background via-background to-muted/60 p-7 shadow-sm">
                <div
                    class="pointer-events-none absolute -top-12 -right-12 h-40 w-40 rounded-full bg-primary/10 blur-2xl">
                </div>

                <div class="relative flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p
                            class="inline-flex items-center gap-2 rounded-full border border-sidebar-border/70 bg-background/80 px-3 py-1 text-[10px] font-semibold tracking-[0.18em] text-muted-foreground uppercase shadow-xs backdrop-blur">
                            <span class="inline-block h-1.5 w-1.5 rounded-full bg-primary"></span>
                            Farmacia Soler
                        </p>
                        <h1 class="mt-3 text-3xl font-semibold tracking-tight text-foreground sm:text-4xl">
                            Inscripcions del taller
                        </h1>
                        <p class="mt-2 max-w-xl text-sm text-muted-foreground">
                            <span class="font-medium text-foreground">{{ workshop.name }}</span>
                            · {{ formatDate(workshop.workshop_date) }}
                            · {{ formatTime(workshop.start_time) }} - {{ formatTime(workshop.end_time) }}
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <div
                            class="inline-flex items-center gap-2 rounded-xl border border-sidebar-border/70 bg-background/80 px-4 py-2 text-sm font-medium shadow-xs">
                            <Users class="h-4 w-4 text-primary" />
                            <span>
                                {{ inscriptions.length }}
                                <span class="text-muted-foreground">
                                    / {{ workshop.max_attendees ?? '∞' }}
                                </span>
                            </span>
                        </div>

                        <Link :href="workshopsIndex().url"
                            class="inline-flex items-center gap-2 rounded-xl border border-sidebar-border/70 bg-background px-4 py-2 text-sm font-medium shadow-xs transition hover:bg-muted">
                            <ArrowLeft class="h-4 w-4" />
                            Tornar
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Main Card -->
            <div class="relative rounded-2xl border border-sidebar-border/70 bg-background/95 p-6 shadow-sm">
                <!-- Toolbar -->
                <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center">
                    <input v-model="searchQuery" type="search" placeholder="Cerca per nom, email o telèfon"
                        class="w-full rounded-xl border border-sidebar-border/80 bg-background px-4 py-2 text-sm shadow-xs transition focus-visible:border-ring focus-visible:ring-2 focus-visible:ring-ring/30 focus-visible:outline-none sm:max-w-xs" />
                </div>

                <!-- Table -->
                <div class="overflow-hidden rounded-2xl border border-sidebar-border/70">
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-muted/80">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase">
                                        Nom
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase">
                                        Email
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase">
                                        Telèfon
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase">
                                        Inscrit el
                                    </th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold uppercase">
                                        Accions
                                    </th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr v-for="(inscription, index) in visibleInscriptions" :key="inscription.id" :class="[
                                    index % 2 === 0
                                        ? 'bg-background'
                                        : 'bg-muted/30',
                                    'border-t border-sidebar-border/60 transition hover:bg-muted/60',
                                ]">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="flex h-9 w-9 items-center justify-center rounded-full bg-primary/10 text-xs font-semibold text-primary">
                                                {{ inscription.name.charAt(0).toUpperCase() }}
                                            </div>
                                            <div class="font-medium text-foreground">
                                                {{ inscription.name }}
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 text-muted-foreground">
                                        <a :href="`mailto:${inscription.email}`"
                                            class="hover:text-primary hover:underline">
                                            {{ inscription.email }}
                                        </a>
                                    </td>

                                    <td class="px-6 py-4 text-muted-foreground">
                                        <span v-if="inscription.phone">
                                            {{ inscription.phone }}
                                        </span>
                                        <span v-else class="text-xs italic">—</span>
                                    </td>

                                    <td class="px-6 py-4 text-muted-foreground">
                                        {{ formatDateTime(inscription.created_at) }}
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-end gap-2">
                                            <button type="button"
                                                class="inline-flex cursor-pointer items-center rounded-lg p-2 text-muted-foreground transition hover:bg-red-50 hover:text-red-600"
                                                aria-label="Eliminar inscripció" @click="askDelete(inscription.id)">
                                                <Trash2 class="h-4 w-4" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Empty -->
                                <tr v-if="filteredInscriptions.length === 0">
                                    <td colspan="5" class="px-6 py-12 text-center text-sm text-muted-foreground">
                                        Encara no hi ha inscripcions per a aquest taller.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
                <!-- Pagination -->
                <div v-if="totalPages > 1" class="mt-5 flex items-center justify-between text-sm">
                    <span class="text-muted-foreground">
                        Pàgina {{ currentPage }} de {{ totalPages }}
                    </span>

                    <div class="flex gap-2">
                        <button type="button" @click="changePage(currentPage - 1)" :disabled="currentPage <= 1"
                            class="rounded-lg border border-sidebar-border bg-background px-3 py-1.5 transition hover:bg-muted disabled:opacity-40">
                            Anterior
                        </button>

                        <button type="button" @click="changePage(currentPage + 1)" :disabled="currentPage >= totalPages"
                            class="rounded-lg border border-sidebar-border bg-background px-3 py-1.5 transition hover:bg-muted disabled:opacity-40">
                            Següent
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <ConfirmDeleteDialog :open="inscriptionToDelete !== null" title="Eliminar inscripció"
            description="Segur que vols eliminar aquesta inscripció? Aquesta acció no es pot desfer."
            @confirm="confirmDelete" @cancel="cancelDelete" />
    </AppLayout>
</template>