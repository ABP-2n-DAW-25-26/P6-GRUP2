<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Pencil, Plus, Trash2 } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import type { BreadcrumbItem } from '@/types';
import {
    create as workshopsCreate,
    destroy as workshopsDestroy,
    edit as workshopsEdit,
    index as workshopsIndex,
} from '@/routes/workshops';

const workshopToDelete = ref<number | null>(null);

function deleteWorkshop(id: number) {
    workshopToDelete.value = id;
}

function confirmDelete() {
    if (workshopToDelete.value === null) {
        return;
    }

    useForm({}).delete(workshopsDestroy(workshopToDelete.value).url);
    workshopToDelete.value = null;
}

function cancelDelete() {
    workshopToDelete.value = null;
}

type Workshop = {
    id: number;
    name: string;
    description: string;
    photo_path: string | null;
    workshop_date: string;
    start_time: string;
    end_time: string;
    max_attendees: number | null;
    is_active: boolean;
};

const props = defineProps<{
    workshops: Workshop[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Tallers', href: workshopsIndex().url },
];

const searchQuery = ref('');

const filteredWorkshops = computed(() => {
    const query = searchQuery.value.trim().toLowerCase();

    if (!query) {
        return props.workshops;
    }

    return props.workshops.filter((workshop) => {
        return (
            workshop.name.toLowerCase().includes(query) ||
            workshop.description.toLowerCase().includes(query)
        );
    });
});

const formatTime = (time: string) => time.slice(0, 5);
</script>
<template>
    <AppLayout :breadcrumbs="breadcrumbs">

        <Head title="Tallers" />

        <div class="relative flex h-full flex-1 flex-col gap-6 overflow-x-auto p-4 md:p-6">
            <!-- Decorative blurred gradients -->
            <div
                class="pointer-events-none absolute top-0 right-8 h-56 w-56 rounded-full bg-gradient-to-br from-primary/20 to-muted/70 blur-3xl">
            </div>
            <div
                class="pointer-events-none absolute bottom-10 left-0 h-64 w-64 rounded-full bg-gradient-to-tr from-secondary/60 to-primary/10 blur-3xl">
            </div>

            <!--  header -->
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
                            Administració de tallers
                        </h1>
                        <p class="mt-2 max-w-xl text-sm text-muted-foreground">
                            Gestió dels tallers programats de la farmàcia.

                        </p>
                    </div>


                </div>
            </div>

            <!-- Main Card -->
            <div class="relative rounded-2xl border border-sidebar-border/70 bg-background/95 p-6 shadow-sm">
                <!-- Toolbar -->
                <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center">
                    <input v-model="searchQuery" type="search" placeholder="Cerca per nom o descripció"
                        class="w-full rounded-xl border border-sidebar-border/80 bg-background px-4 py-2 text-sm shadow-xs transition focus-visible:border-ring focus-visible:ring-2 focus-visible:ring-ring/30 focus-visible:outline-none sm:max-w-xs" />

                    <Link :href="workshopsCreate().url"
                        class="inline-flex items-center justify-center rounded-xl bg-primary px-4 py-2 text-sm font-medium text-primary-foreground shadow-sm transition hover:bg-primary/90 sm:ml-auto">
                        <Plus class="mr-2 h-4 w-4" />
                        Crear taller
                    </Link>
                </div>

                <!-- Table -->
                <div class="overflow-hidden rounded-2xl border border-sidebar-border/70">
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-muted/80">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase">
                                        Taller
                                    </th>

                                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase">
                                        Data
                                    </th>

                                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase">
                                        Horari
                                    </th>

                                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase">
                                        Places
                                    </th>

                                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase">
                                        Estat
                                    </th>

                                    <th class="px-6 py-4 text-right text-xs font-semibold uppercase">
                                        Accions
                                    </th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr v-for="(workshop, index) in filteredWorkshops" :key="workshop.id" :class="[
                                    index % 2 === 0
                                        ? 'bg-background'
                                        : 'bg-muted/30',
                                    'border-t border-sidebar-border/60 transition hover:bg-muted/60',
                                ]">
                                    <!-- Workshop -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-start gap-3">
                                            <div
                                                class="flex h-10 w-10 items-center justify-center rounded-full bg-primary/10 text-xs font-semibold text-primary">
                                                {{
                                                    workshop.name
                                                        .charAt(0)
                                                        .toUpperCase()
                                                }}
                                            </div>

                                            <div>
                                                <div class="font-medium text-foreground">
                                                    {{ workshop.name }}
                                                </div>

                                                <div class="mt-1 max-w-xs text-xs text-muted-foreground" v-html="workshop.description
                                                    "></div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Date -->
                                    <td class="px-6 py-4 text-muted-foreground">
                                        {{
                                            new Date(
                                                workshop.workshop_date,
                                            ).toLocaleDateString('ca-ES')
                                        }}
                                    </td>

                                    <!-- Time -->
                                    <td class="px-6 py-4 text-muted-foreground">
                                        {{ formatTime(workshop.start_time) }}
                                        -
                                        {{ formatTime(workshop.end_time) }}
                                    </td>

                                    <!-- Max -->
                                    <td class="px-6 py-4 text-muted-foreground">
                                        {{
                                            workshop.max_attendees ??
                                            'Sense límit'
                                        }}
                                    </td>

                                    <!-- Status -->
                                    <td class="px-6 py-4">
                                        <span :class="workshop.is_active
                                                ? 'border-green-200 bg-green-50 text-green-700'
                                                : 'border-zinc-200 bg-zinc-100 text-zinc-600'
                                            " class="inline-flex rounded-full border px-3 py-1 text-xs font-semibold">
                                            {{
                                                workshop.is_active
                                                    ? 'Actiu'
                                                    : 'Inactiu'
                                            }}
                                        </span>
                                    </td>

                                    <!-- Actions -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-end gap-2">
                                            <Link :href="workshopsEdit(workshop.id)
                                                    .url
                                                "
                                                class="inline-flex items-center rounded-lg p-2 text-muted-foreground transition hover:bg-muted hover:text-foreground">
                                                <Pencil class="h-4 w-4" />
                                            </Link>

                                            <button type="button"
                                                class="inline-flex cursor-pointer items-center rounded-lg p-2 text-muted-foreground transition hover:bg-red-50 hover:text-red-600"
                                                @click="
                                                    deleteWorkshop(workshop.id)
                                                    ">
                                                <Trash2 class="h-4 w-4" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Empty -->
                                <tr v-if="filteredWorkshops.length === 0">
                                    <td colspan="6" class="px-6 py-12 text-center text-sm text-muted-foreground">
                                        Encara no hi ha tallers registrats.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Dialog -->
        <Dialog :open="workshopToDelete !== null" @update:open="cancelDelete">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>
                        Eliminar taller
                    </DialogTitle>

                    <DialogDescription>
                        Segur que vols eliminar aquest taller?
                        Aquesta acció no es pot desfer.
                    </DialogDescription>
                </DialogHeader>

                <DialogFooter>
                    <button type="button"
                        class="inline-flex cursor-pointer items-center rounded-xl border border-sidebar-border/80 bg-background px-4 py-2 text-sm font-medium transition hover:bg-muted"
                        @click="cancelDelete">
                        Cancel·lar
                    </button>

                    <button type="button"
                        class="inline-flex cursor-pointer items-center rounded-xl bg-red-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-red-700"
                        @click="confirmDelete">
                        Eliminar
                    </button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>