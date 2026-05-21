<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { CalendarPlus, Trash2 } from 'lucide-vue-next';
import { ref, computed } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';

const daysOfWeek: Record<number, string> = {
    1: 'Dilluns',
    2: 'Dimarts',
    3: 'Dimecres',
    4: 'Dijous',
    5: 'Divendres',
    6: 'Dissabte',
    7: 'Diumenge',
};

interface Schedule {
    id: number;
    day_of_week: number;
    start_time: string;
    end_time: string;
}

interface Service {
    id: number;
    name: string;
    description: string;
    duration_minutes: number;
    icon: string | null;
    schedules: Schedule[];
}

const props = defineProps<{ service: Service }>();

// DELETE MODAL
const selectedSchedule = ref<Schedule | null>(null);
const showDeleteModal = ref(false);

function openDeleteModal(schedule: Schedule) {
    selectedSchedule.value = schedule;
    showDeleteModal.value = true;
}

function closeDeleteModal() {
    showDeleteModal.value = false;
    selectedSchedule.value = null;
}

const schedules = ref<Schedule[]>([...props.service.schedules]);
// PAGINATION
const page = ref(1);
const perPage = 10;

const visibleSchedules = computed(() => {
    const start = (page.value - 1) * perPage;
    const end = start + perPage;

    return schedules.value.slice(start, end);
});

const totalPages = computed(() => Math.ceil(schedules.value.length / perPage));

function changePage(newPage: number) {
    if (newPage < 1 || newPage > totalPages.value) {
return;
}

    page.value = newPage;

    window.scrollTo({
        top: 0,
        behavior: 'smooth',
    });
}
// DELETE SCHEDULE
function deleteSchedule() {
    if (!selectedSchedule.value) {
        return;
    }

    const id = selectedSchedule.value.id;

    router.delete(`/admin/service-schedules/${id}`, {
        preserveScroll: true,
        onSuccess: () => {
            closeDeleteModal();

            // props.service.schedules = props.service.schedules.filter(
            //     s => s.id !== id
            // )

            schedules.value = schedules.value.filter((s) => s.id !== id);
        },
    });
}
</script>
<template>
    <AppLayout>
        <Head :title="props.service.name" />

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

            <!-- Header -->
            <div
                class="relative rounded-2xl border border-sidebar-border/70 bg-gradient-to-br from-background to-muted/70 p-7 shadow-sm"
            >
                <div
                    class="flex flex-col gap-5 md:flex-row md:items-start md:justify-between"
                >
                    <div class="flex items-start gap-4">
                        <div>
                            <p
                                class="text-xs font-semibold tracking-[0.18em] text-muted-foreground uppercase"
                            >
                                Farmacia Soler
                            </p>

                            <h1
                                class="mt-1 text-3xl font-semibold tracking-tight text-foreground"
                            >
                                {{ props.service.name }}
                            </h1>

                            <div
                                class="mt-2 max-w-2xl text-sm text-muted-foreground"
                                v-html="props.service.description"
                            ></div>

                            <p
                                class="mt-3 inline-flex rounded-full bg-primary/10 px-3 py-1 text-xs font-medium text-primary"
                            >
                                {{ props.service.duration_minutes }} minuts
                            </p>
                        </div>
                    </div>

                    <Link
                        :href="`/admin/services/${props.service.id}/edit`"
                        class="inline-flex items-center rounded-xl border border-sidebar-border bg-background px-4 py-2 text-sm transition hover:bg-muted"
                    >
                        Editar servei
                    </Link>
                </div>
            </div>

            <!-- Schedules -->
            <div
                class="relative rounded-2xl border border-sidebar-border/70 bg-background/95 p-6 shadow-sm"
            >
                <!-- Toolbar -->
                <div
                    class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center"
                >
                    <div>
                        <h2 class="text-lg font-semibold text-foreground">
                            Horaris
                        </h2>

                        <p class="text-sm text-muted-foreground">
                            Gestiona els horaris disponibles del servei.
                        </p>
                    </div>

                    <Link
                        :href="`/admin/service-schedules/create?service_id=${props.service.id}`"
                        class="inline-flex items-center justify-center rounded-xl bg-primary px-4 py-2 text-sm font-medium text-primary-foreground shadow-sm transition hover:bg-primary/90 sm:ml-auto"
                    >
                        <CalendarPlus class="mr-2 h-4 w-4" />
                        Nou horari
                    </Link>
                </div>

                <!-- Empty -->
                <div
                    v-if="schedules.length === 0"
                    class="rounded-2xl border border-dashed border-sidebar-border/70 bg-muted/20 px-6 py-12 text-center text-sm text-muted-foreground"
                >
                    No hi ha horaris encara.
                </div>

                <!-- Table -->
                <div
                    v-else
                    class="overflow-hidden rounded-2xl border border-sidebar-border/70"
                >
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-muted/80">
                                <tr>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-semibold uppercase"
                                    >
                                        Dia
                                    </th>

                                    <th
                                        class="px-6 py-4 text-left text-xs font-semibold uppercase"
                                    >
                                        Inici
                                    </th>

                                    <th
                                        class="px-6 py-4 text-left text-xs font-semibold uppercase"
                                    >
                                        Fi
                                    </th>

                                    <th
                                        class="px-6 py-4 text-right text-xs font-semibold uppercase"
                                    >
                                        Accions
                                    </th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr
                                    v-for="(s, index) in visibleSchedules"
                                    :key="s.id"
                                    :class="[
                                        index % 2 === 0
                                            ? 'bg-background'
                                            : 'bg-muted/30',
                                        'border-t border-sidebar-border/60 transition hover:bg-muted/60',
                                    ]"
                                >
                                    <!-- Day -->
                                    <td
                                        class="px-6 py-4 font-medium text-foreground"
                                    >
                                        {{ daysOfWeek[s.day_of_week] }}
                                    </td>

                                    <!-- Start -->
                                    <td class="px-6 py-4 text-muted-foreground">
                                        {{ s.start_time }}
                                    </td>

                                    <!-- End -->
                                    <td class="px-6 py-4 text-muted-foreground">
                                        {{ s.end_time }}
                                    </td>

                                    <!-- Actions -->
                                    <td class="px-6 py-4">
                                        <div class="flex justify-end">
                                            <button
                                                @click="openDeleteModal(s)"
                                                aria-label="Eliminar horari"
                                                class="inline-flex items-center rounded-lg p-2 text-muted-foreground transition hover:bg-red-50 hover:text-red-600"
                                            >
                                                <Trash2 class="h-4 w-4" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination -->
                <div
                    v-if="totalPages > 1"
                    class="mt-5 flex items-center justify-between text-sm"
                >
                    <span class="text-muted-foreground">
                        Pàgina {{ page }} de {{ totalPages }}
                    </span>

                    <div class="flex gap-2">
                        <button
                            type="button"
                            @click="changePage(page - 1)"
                            :disabled="page <= 1"
                            class="rounded-lg border border-sidebar-border bg-background px-3 py-1.5 transition hover:bg-muted disabled:opacity-40"
                        >
                            Anterior
                        </button>

                        <button
                            type="button"
                            @click="changePage(page + 1)"
                            :disabled="page >= totalPages"
                            class="rounded-lg border border-sidebar-border bg-background px-3 py-1.5 transition hover:bg-muted disabled:opacity-40"
                        >
                            Següent
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Modal -->
        <div
            v-if="showDeleteModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
        >
            <div
                class="w-full max-w-md rounded-2xl border border-sidebar-border/70 bg-background p-6 shadow-xl"
            >
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-foreground">
                        Eliminar horari
                    </h2>

                    <button
                        @click="closeDeleteModal"
                        class="rounded-lg p-1 text-muted-foreground transition hover:bg-muted"
                    >
                        ✕
                    </button>
                </div>

                <p class="mt-4 text-sm text-muted-foreground">
                    Estàs segur que vols eliminar aquest horari?
                </p>

                <div
                    v-if="selectedSchedule"
                    class="mt-4 rounded-xl border border-sidebar-border/70 bg-muted/20 p-4"
                >
                    <p class="font-medium text-foreground">
                        {{ daysOfWeek[selectedSchedule.day_of_week] }}
                    </p>

                    <p class="mt-1 text-sm text-muted-foreground">
                        {{ selectedSchedule.start_time }}
                        →
                        {{ selectedSchedule.end_time }}
                    </p>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button
                        @click="closeDeleteModal"
                        class="rounded-xl border border-sidebar-border px-4 py-2 text-sm transition hover:bg-muted"
                    >
                        Cancel·lar
                    </button>

                    <button
                        @click="deleteSchedule"
                        class="rounded-xl bg-red-600 px-4 py-2 text-sm text-white transition hover:bg-red-700"
                    >
                        Eliminar
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
