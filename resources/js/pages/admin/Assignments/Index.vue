<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { CheckCheck, ClockArrowDown, Ban } from 'lucide-vue-next';
import { ref, computed } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Administració de Encarrecs', href: 'admin/adminAssignments' },
];

const props = defineProps<{
    assignments: {
        id: number;
        name: string;
        address: string;
        phone_number: number;
        description: string;
        status: string;
        created_at: string;
        updated_at: string;
    }[];
}>();

const search = ref('');

function normalize(str: string): string {
    return str
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .toLowerCase();
}

const filteredAssignments = computed(() => {
    const s = normalize(search.value);

    if (!s) {
        return props.assignments;
    }

    return props.assignments.filter(
        (ass) =>
            normalize(ass.name).includes(s) ||
            normalize(ass.description).includes(s) ||
            normalize(ass.address).includes(s) ||
            normalize(ass.updated_at).includes(s) ||
            normalize(ass.created_at).includes(s) ||
            normalize(ass.status).includes(s) ||
            normalize(String(ass.phone_number)).includes(s),
    );
});
</script>
<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="relative flex h-full flex-1 flex-col gap-6 overflow-x-auto p-4 md:p-6"
        >
            <!-- Decorative blurred gradients -->
            <div
                class="pointer-events-none absolute top-0 right-8 h-56 w-56 rounded-full bg-gradient-to-br from-primary/20 to-muted/70 blur-3xl"
            ></div>
            <div
                class="pointer-events-none absolute bottom-10 left-0 h-64 w-64 rounded-full bg-gradient-to-tr from-secondary/60 to-primary/10 blur-3xl"
            ></div>

            <!--  header -->
            <div
                class="relative overflow-hidden rounded-2xl border border-sidebar-border/70 bg-gradient-to-br from-background via-background to-muted/60 p-7 shadow-sm"
            >
                <div
                    class="pointer-events-none absolute -top-12 -right-12 h-40 w-40 rounded-full bg-primary/10 blur-2xl"
                ></div>

                <div
                    class="relative flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between"
                >
                    <div>
                        <p
                            class="inline-flex items-center gap-2 rounded-full border border-sidebar-border/70 bg-background/80 px-3 py-1 text-[10px] font-semibold tracking-[0.18em] text-muted-foreground uppercase shadow-xs backdrop-blur"
                        >
                            <span
                                class="inline-block h-1.5 w-1.5 rounded-full bg-primary"
                            ></span>
                            Farmacia Soler
                        </p>
                        <h1
                            class="mt-3 text-3xl font-semibold tracking-tight text-foreground sm:text-4xl"
                        >
                            Administració d'encàrrecs
                        </h1>
                        <p class="mt-2 max-w-xl text-sm text-muted-foreground">
                            Gestiona i actualitza l'estat dels encàrrecs dels
                            clients.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Search -->
            <div class="relative max-w-md">
                <input
                    type="text"
                    v-model="search"
                    placeholder="Cercar encàrrecs..."
                    class="w-full rounded-2xl border border-sidebar-border/80 bg-background py-3 pr-11 pl-4 text-sm shadow-xs transition focus-visible:border-ring focus-visible:ring-2 focus-visible:ring-ring/30 focus-visible:outline-none"
                />

                <svg
                    class="pointer-events-none absolute top-1/2 right-4 h-4 w-4 -translate-y-1/2 text-muted-foreground"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <circle cx="11" cy="11" r="8" />
                    <path d="m21 21-4.35-4.35" />
                </svg>
            </div>

            <!-- Cards -->
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
                <div
                    v-for="ass in filteredAssignments"
                    :key="ass.id"
                    class="group rounded-2xl border border-sidebar-border/70 bg-background/95 shadow-sm transition hover:-translate-y-1 hover:shadow-md"
                >
                    <!-- Top -->
                    <div
                        class="flex items-start justify-between border-b border-sidebar-border/70 p-5"
                    >
                        <div>
                            <h2 class="text-lg font-semibold text-foreground">
                                {{ ass.name }}
                            </h2>

                            <p class="mt-1 text-sm text-muted-foreground">
                                {{ ass.address }}
                            </p>
                        </div>

                        <!-- Status -->
                        <span
                            v-if="ass.status === 'completed'"
                            class="rounded-full border border-green-200 bg-green-50 px-3 py-1 text-xs font-medium text-green-700"
                        >
                            Completat
                        </span>

                        <span
                            v-else-if="ass.status === 'pending'"
                            class="rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-xs font-medium text-amber-700"
                        >
                            Pendent
                        </span>

                        <span
                            v-else-if="ass.status === 'cancelled'"
                            class="rounded-full border border-red-200 bg-red-50 px-3 py-1 text-xs font-medium text-red-700"
                        >
                            Cancel·lat
                        </span>
                    </div>

                    <!-- Content -->
                    <div class="space-y-4 p-5">
                        <!-- Phone -->
                        <div>
                            <p
                                class="text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                            >
                                Telèfon
                            </p>

                            <p class="mt-1 text-sm text-foreground">
                                {{ ass.phone_number }}
                            </p>
                        </div>

                        <!-- Description -->
                        <div>
                            <p
                                class="text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                            >
                                Descripció
                            </p>

                            <p
                                class="mt-1 text-sm leading-relaxed text-muted-foreground"
                            >
                                {{ ass.description }}
                            </p>
                        </div>

                        <!-- Date -->
                        <div>
                            <p
                                class="text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                            >
                                Actualitzat
                            </p>

                            <p class="mt-1 text-sm text-muted-foreground">
                                {{ ass.updated_at }}
                            </p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div
                        class="flex items-center gap-2 border-t border-sidebar-border/70 p-4"
                    >
                        <!-- Pending -->
                        <div class="group/action relative flex-1">
                            <Link
                                :href="`/admin/adminAssignments/${ass.id}`"
                                :data="{ status: 'pending' }"
                                preserve-scroll
                                method="patch"
                                class="flex items-center justify-center rounded-xl border border-sidebar-border/70 bg-muted/30 px-4 py-2 transition hover:bg-muted"
                            >
                                <ClockArrowDown class="h-4 w-4" />
                            </Link>

                            <div
                                class="pointer-events-none absolute -top-9 left-1/2 -translate-x-1/2 rounded-lg bg-foreground px-2 py-1 text-xs whitespace-nowrap text-background opacity-0 transition group-hover/action:opacity-100"
                            >
                                Marcar pendent
                            </div>
                        </div>

                        <!-- Cancel -->
                        <div class="group/action relative flex-1">
                            <Link
                                :href="`/admin/adminAssignments/${ass.id}`"
                                :data="{ status: 'cancelled' }"
                                preserve-scroll
                                method="patch"
                                class="flex items-center justify-center rounded-xl border border-red-200 bg-red-50 px-4 py-2 text-red-600 transition hover:bg-red-100"
                            >
                                <Ban class="h-4 w-4" />
                            </Link>

                            <div
                                class="pointer-events-none absolute -top-9 left-1/2 -translate-x-1/2 rounded-lg bg-foreground px-2 py-1 text-xs whitespace-nowrap text-background opacity-0 transition group-hover/action:opacity-100"
                            >
                                Cancel·lar
                            </div>
                        </div>

                        <!-- Complete -->
                        <div class="group/action relative flex-1">
                            <Link
                                :href="`/admin/adminAssignments/${ass.id}`"
                                :data="{ status: 'completed' }"
                                preserve-scroll
                                method="patch"
                                class="flex items-center justify-center rounded-xl border border-green-200 bg-green-50 px-4 py-2 text-green-600 transition hover:bg-green-100"
                            >
                                <CheckCheck class="h-4 w-4" />
                            </Link>

                            <div
                                class="pointer-events-none absolute -top-9 left-1/2 -translate-x-1/2 rounded-lg bg-foreground px-2 py-1 text-xs whitespace-nowrap text-background opacity-0 transition group-hover/action:opacity-100"
                            >
                                Completar
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty -->
                <div
                    v-if="filteredAssignments.length === 0"
                    class="col-span-full rounded-2xl border border-dashed border-sidebar-border/70 bg-muted/20 px-6 py-14 text-center text-sm text-muted-foreground"
                >
                    No s'han trobat encàrrecs.
                </div>
            </div>
        </div>
    </AppLayout>
</template>
