<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { Ban, CheckCheck, ClockArrowDown, Package } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import ConfirmDeleteDialog from '@/components/ConfirmDeleteDialog.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { index as assignmentsIndex, update as assignmentsUpdate } from '@/routes/adminAssignments';

type Assignment = {
    id: number;
    name: string;
    address: string;
    phone_number: number;
    description: string;
    status: string;
    created_at: string;
    updated_at: string;
};

const props = defineProps<{ assignments: Assignment[] }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: "Administració d'encàrrecs", href: assignmentsIndex().url },
];


const search = ref('');
const statusFilter = ref<StatusFilterKey>('all');

function normalize(str: string): string {
    return str.normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase();
}

const filteredAssignments = computed(() => {
    let list = props.assignments;

    if (statusFilter.value !== 'all') {
        list = list.filter((a) => a.status === statusFilter.value);
    }

    const s = normalize(search.value.trim());
    if (!s) {
        return list;
    }

    return list.filter(
        (a) =>
            normalize(a.name).includes(s) ||
            normalize(a.description).includes(s) ||
            normalize(a.address).includes(s) ||
            normalize(String(a.phone_number)).includes(s),
    );
});

function countByStatus(status: string) {
    return props.assignments.filter((a) => a.status === status).length;
}

function formatDate(dateStr: string) {
    return new Date(dateStr).toLocaleDateString('ca-ES', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
        timeZone: 'UTC',
    });
}

const statusLabels: Record<string, { label: string; classes: string }> = {
    pending:   { label: 'Pendent',    classes: 'border-amber-200 bg-amber-50 text-amber-700' },
    completed: { label: 'Completat',  classes: 'border-green-200 bg-green-50 text-green-700' },
    cancelled: { label: 'Cancel·lat', classes: 'border-red-200 bg-red-50 text-red-700' },
};

type StatusFilterKey = 'all' | 'pending' | 'completed' | 'cancelled';

const statusPills: { key: StatusFilterKey; label: string; activeClasses: string }[] = [
    { key: 'all',       label: 'Tots',        activeClasses: 'border-sidebar-border/70 bg-muted/60 text-foreground' },
    { key: 'pending',   label: 'Pendents',    activeClasses: 'border-amber-200 bg-amber-50 text-amber-700' },
    { key: 'completed', label: 'Completats',  activeClasses: 'border-green-200 bg-green-50 text-green-700' },
    { key: 'cancelled', label: 'Cancel·lats', activeClasses: 'border-red-200 bg-red-50 text-red-700' },
];

function pillCount(key: StatusFilterKey): number {
    return key === 'all' ? props.assignments.length : countByStatus(key);
}

type PendingAction = {
    assignmentId: number;
    status: 'pending' | 'completed' | 'cancelled';
};

const pendingAction = ref<PendingAction | null>(null);

const actionConfig: Record<PendingAction['status'], { title: string; description: string; confirmText: string; confirmClass: string }> = {
    completed: {
        title: 'Completar encàrrec',
        description: "Segur que vols marcar aquest encàrrec com a completat?",
        confirmText: 'Sí, completar',
        confirmClass: 'inline-flex cursor-pointer items-center rounded-xl bg-green-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-green-700',
    },
    cancelled: {
        title: "Cancel·lar encàrrec",
        description: "Segur que vols cancel·lar aquest encàrrec? El client no serà notificat automàticament.",
        confirmText: "Sí, cancel·lar",
        confirmClass: 'inline-flex cursor-pointer items-center rounded-xl bg-red-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-red-700',
    },
    pending: {
        title: 'Reobrir encàrrec',
        description: "Segur que vols tornar a marcar aquest encàrrec com a pendent?",
        confirmText: 'Sí, reobrir',
        confirmClass: 'inline-flex cursor-pointer items-center rounded-xl bg-amber-500 px-4 py-2 text-sm font-medium text-white transition hover:bg-amber-600',
    },
};

function requestAction(assignmentId: number, status: PendingAction['status']) {
    pendingAction.value = { assignmentId, status };
}

function confirmAction() {
    if (!pendingAction.value) {
        return;
    }

    router.patch(assignmentsUpdate(pendingAction.value.assignmentId).url, {
        status: pendingAction.value.status,
    }, { preserveScroll: true });

    pendingAction.value = null;
}

function cancelAction() {
    pendingAction.value = null;
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="relative flex h-full flex-1 flex-col gap-6 overflow-x-auto p-4 md:p-6">
            <!-- Decorative gradients -->
            <div class="pointer-events-none absolute top-0 right-8 h-56 w-56 rounded-full bg-gradient-to-br from-primary/20 to-muted/70 blur-3xl"></div>
            <div class="pointer-events-none absolute bottom-10 left-0 h-64 w-64 rounded-full bg-gradient-to-tr from-secondary/60 to-primary/10 blur-3xl"></div>

            <!-- Header -->
            <div class="relative overflow-hidden rounded-2xl border border-sidebar-border/70 bg-gradient-to-br from-background via-background to-muted/60 p-7 shadow-sm">
                <div class="pointer-events-none absolute -top-12 -right-12 h-40 w-40 rounded-full bg-primary/10 blur-2xl"></div>
                <div class="relative">
                    <p class="inline-flex items-center gap-2 rounded-full border border-sidebar-border/70 bg-background/80 px-3 py-1 text-[10px] font-semibold tracking-[0.18em] text-muted-foreground uppercase shadow-xs backdrop-blur">
                        <span class="inline-block h-1.5 w-1.5 rounded-full bg-primary"></span>
                        Farmacia Soler
                    </p>
                    <h1 class="mt-3 text-3xl font-semibold tracking-tight text-foreground sm:text-4xl">
                        Encàrrecs
                    </h1>
                    <p class="mt-2 max-w-xl text-sm text-muted-foreground">
                        Gestiona i actualitza l'estat dels encàrrecs dels clients.
                    </p>
                </div>
            </div>

            <!-- Filters -->
            <div class="relative rounded-2xl border border-sidebar-border/70 bg-background/95 p-5 shadow-sm">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <!-- Search -->
                    <div class="relative w-full sm:max-w-xs">
                        <svg class="pointer-events-none absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <circle cx="11" cy="11" r="8" />
                            <path d="m21 21-4.35-4.35" />
                        </svg>
                        <input
                            v-model="search"
                            type="search"
                            placeholder="Cercar encàrrecs..."
                            class="w-full rounded-xl border border-sidebar-border/80 bg-background py-2 pr-4 pl-9 text-sm shadow-xs transition focus-visible:border-ring focus-visible:ring-2 focus-visible:ring-ring/30 focus-visible:outline-none"
                        />
                    </div>

                    <!-- Status pills -->
                    <div class="flex flex-wrap gap-2">
                        <button
                            v-for="pill in statusPills"
                            :key="pill.key"
                            @click="statusFilter = pill.key"
                            :class="[
                                'inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-medium transition',
                                statusFilter === pill.key
                                    ? pill.activeClasses
                                    : 'border-sidebar-border/50 bg-background text-muted-foreground hover:bg-muted/50',
                            ]"
                        >
                            {{ pill.label }}
                            <span class="rounded-full bg-black/10 px-1.5 py-0.5 text-[10px] font-semibold tabular-nums">
                                {{ pillCount(pill.key) }}
                            </span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Cards -->
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
                <div
                    v-for="ass in filteredAssignments"
                    :key="ass.id"
                    class="flex flex-col rounded-2xl border border-sidebar-border/70 bg-background/95 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md"
                >
                    <!-- Card header -->
                    <div class="flex items-start justify-between gap-3 border-b border-sidebar-border/60 p-5">
                        <div class="flex min-w-0 items-start gap-3">
                            <div class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-primary">
                                <Package class="h-4 w-4" />
                            </div>
                            <div class="min-w-0">
                                <h2 class="truncate text-base font-semibold text-foreground">{{ ass.name }}</h2>
                                <p class="mt-0.5 truncate text-xs text-muted-foreground">{{ ass.address }}</p>
                            </div>
                        </div>
                        <span
                            :class="['shrink-0 rounded-full border px-2.5 py-0.5 text-[11px] font-semibold', statusLabels[ass.status]?.classes]"
                        >
                            {{ statusLabels[ass.status]?.label }}
                        </span>
                    </div>

                    <!-- Card body -->
                    <div class="flex-1 space-y-3 p-5">
                        <div>
                            <p class="mb-1 text-[10px] font-semibold tracking-widest text-muted-foreground uppercase">Telèfon</p>
                            <p class="text-sm text-foreground">{{ ass.phone_number }}</p>
                        </div>
                        <div>
                            <p class="mb-1 text-[10px] font-semibold tracking-widest text-muted-foreground uppercase">Descripció</p>
                            <p class="line-clamp-3 text-sm leading-relaxed text-muted-foreground">{{ ass.description }}</p>
                        </div>
                        <div>
                            <p class="mb-1 text-[10px] font-semibold tracking-widest text-muted-foreground uppercase">Actualitzat</p>
                            <p class="text-xs text-muted-foreground">{{ formatDate(ass.updated_at) }}</p>
                        </div>
                    </div>

                    <!-- Card actions -->
                    <div class="border-t border-sidebar-border/60 p-4">
                        <!-- pending: complete (primary) + cancel (ghost) -->
                        <div v-if="ass.status === 'pending'" class="flex gap-2">
                            <button
                                type="button"
                                @click="requestAction(ass.id, 'completed')"
                                class="inline-flex flex-1 items-center justify-center gap-1.5 rounded-xl bg-green-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-green-700"
                            >
                                <CheckCheck class="h-3.5 w-3.5" />
                                Completar
                            </button>
                            <button
                                type="button"
                                @click="requestAction(ass.id, 'cancelled')"
                                class="inline-flex items-center justify-center gap-1.5 rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-xs font-semibold text-red-600 transition hover:bg-red-100"
                            >
                                <Ban class="h-3.5 w-3.5" />
                                Cancel·lar
                            </button>
                        </div>

                        <!-- completed: reopen -->
                        <button
                            v-else-if="ass.status === 'completed'"
                            type="button"
                            @click="requestAction(ass.id, 'pending')"
                            class="inline-flex w-full items-center justify-center gap-1.5 rounded-xl border border-sidebar-border/70 bg-muted/30 px-3 py-2 text-xs font-semibold text-muted-foreground transition hover:bg-muted"
                        >
                            <ClockArrowDown class="h-3.5 w-3.5" />
                            Marcar pendent
                        </button>

                        <!-- cancelled: reopen -->
                        <button
                            v-else-if="ass.status === 'cancelled'"
                            type="button"
                            @click="requestAction(ass.id, 'pending')"
                            class="inline-flex w-full items-center justify-center gap-1.5 rounded-xl border border-amber-200 bg-amber-50 px-3 py-2 text-xs font-semibold text-amber-700 transition hover:bg-amber-100"
                        >
                            <ClockArrowDown class="h-3.5 w-3.5" />
                            Reobrir
                        </button>
                    </div>
                </div>

                <!-- Empty -->
                <div
                    v-if="filteredAssignments.length === 0"
                    class="col-span-full rounded-2xl border border-dashed border-sidebar-border/70 bg-muted/20 px-6 py-16 text-center"
                >
                    <Package class="mx-auto mb-3 h-8 w-8 text-muted-foreground/40" />
                    <p class="text-sm text-muted-foreground">No s'han trobat encàrrecs.</p>
                </div>
            </div>
        </div>

        <ConfirmDeleteDialog
            v-if="pendingAction"
            :open="pendingAction !== null"
            :title="actionConfig[pendingAction.status].title"
            :description="actionConfig[pendingAction.status].description"
            :confirm-text="actionConfig[pendingAction.status].confirmText"
            :confirm-class="actionConfig[pendingAction.status].confirmClass"
            @confirm="confirmAction"
            @cancel="cancelAction"
        />
    </AppLayout>
</template>
