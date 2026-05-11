<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { CalendarPlus, Trash2 } from 'lucide-vue-next';
import {
    Pill,
    HeartPulse,
    FlaskConical,
    ShieldCheck,
    ScanFace,
} from 'lucide-vue-next';

// ICON MAP
const iconMap: Record<string, any> = {
    pill: Pill,
    heart: HeartPulse,
    flask: FlaskConical,
    shield: ShieldCheck,
    scan: ScanFace,
};

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

function deleteSchedule() {
    if (!selectedSchedule.value) return;

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

        <div class="space-y-6 p-6">
            <!-- HEADER -->
            <div
                class="flex items-start gap-4 rounded-xl border bg-white p-6 shadow"
            >
                <component
                    v-if="props.service.icon && iconMap[props.service.icon]"
                    :is="iconMap[props.service.icon]"
                    class="h-6 w-6 text-blue-600"
                />

                <div class="flex-1">
                    <h1 class="text-2xl font-bold">{{ props.service.name }}</h1>
                    <p class="text-sm text-gray-500">
                        {{ props.service.description }}
                    </p>
                    <p class="text-xs text-gray-400">
                        Durada: {{ props.service.duration_minutes }} min
                    </p>
                </div>

                <Link
                    :href="`/admin/services/${props.service.id}/edit`"
                    class="rounded border px-4 py-2"
                >
                    Editar
                </Link>
            </div>

            <!-- SCHEDULES -->
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold">Horaris</h2>

                <Link
                    :href="`/admin/service-schedules/create?service_id=${props.service.id}`"
                    class="flex items-center gap-2 rounded bg-blue-400 px-4 py-2 text-white"
                >
                    <CalendarPlus class="h-4 w-4" />
                    Nou horari
                </Link>
            </div>

            <!-- EMPTY -->
            <div v-if="schedules.length === 0" class="text-gray-400">
                No hi ha horaris encara
            </div>

            <!-- TABLE -->
            <table v-else class="w-full rounded border bg-white">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-3 text-left">Dia</th>
                        <th class="p-3 text-left">Inici</th>
                        <th class="p-3 text-left">Fi</th>
                        <th></th>
                    </tr>
                </thead>

                <tbody>
                    <tr v-for="s in schedules" :key="s.id" class="border-t">
                        <td class="p-3">{{ daysOfWeek[s.day_of_week] }}</td>
                        <td class="p-3">{{ s.start_time }}</td>
                        <td class="p-3">{{ s.end_time }}</td>
                        <td class="p-3 text-right">
                            <button @click="openDeleteModal(s)">
                                <Trash2 class="h-4 w-4 text-red-500" />
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <!-- DELETE MODAL -->
            <div
                v-if="showDeleteModal"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
            >
                <div class="w-full max-w-md space-y-4 rounded-xl bg-white p-6">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-bold">Eliminar horari</h2>
                        <button @click="closeDeleteModal">✕</button>
                    </div>

                    <p class="text-gray-600">
                        Estàs segur que vols eliminar aquest horari?
                    </p>

                    <p class="font-semibold" v-if="selectedSchedule">
                        {{ daysOfWeek[selectedSchedule.day_of_week] }} —
                        {{ selectedSchedule.start_time }} →
                        {{ selectedSchedule.end_time }}
                    </p>

                    <div class="flex justify-end gap-3 pt-4">
                        <button
                            @click="closeDeleteModal"
                            class="rounded border px-4 py-2"
                        >
                            Cancel·lar
                        </button>

                        <button
                            @click="deleteSchedule"
                            class="rounded bg-red-600 px-4 py-2 text-white hover:bg-red-700"
                        >
                            Eliminar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
