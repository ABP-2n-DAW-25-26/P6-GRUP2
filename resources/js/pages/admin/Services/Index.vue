<script setup lang="ts">
import { Link, Head, useForm } from '@inertiajs/vue3';
import { SquarePen, Trash2, Plus, CalendarPlus } from 'lucide-vue-next';
import {
    Pill,
    HeartPulse,
    FlaskConical,
    ShieldCheck,
    ScanFace,
    Droplet,
    Activity,
    Stethoscope,
    Syringe,
    Microscope,
    Apple,
    Brain,
    Bandage,
    Thermometer,
} from 'lucide-vue-next';
import { ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { admindashboard as dashboard } from '@/routes';

// ICON MAP
const iconMap: Record<string, any> = {
    pill: Pill,
    heart: HeartPulse,
    flask: FlaskConical,
    shield: ShieldCheck,
    scan: ScanFace,
    droplet: Droplet,
    activity: Activity,
    stethoscope: Stethoscope,
    syringe: Syringe,
    microscope: Microscope,
    apple: Apple,
    brain: Brain,
    bandage: Bandage,
    thermometer: Thermometer,
};

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Administració de serveis', href: dashboard().url },
];

// TYPES
interface Service {
    id: number;
    name: string;
    description: string;
    duration_minutes: number;
    icon: string | null;
}

const props = defineProps<{
    services: Service[];
}>();

const search = ref('');

const searchResults = ref<Service[]>(props.services);

const filteredServices = ref<Service[]>(props.services);

function filter() {
    let result = searchResults.value;

    if (search.value.trim() !== '') {
        result = result.filter((service) =>
            service.name.toLowerCase().includes(search.value.toLowerCase()),
        );
    }

    filteredServices.value = result;
}

function handleSearch(event: Event) {
    const input = event.target as HTMLInputElement;
    search.value = input.value;
    filter();
}

// DELETE

const showDeleteModal = ref(false);

const selectedService = ref({
    id: 0,
    name: '',
});

function openDeleteModal(service: Service) {
    selectedService.value = {
        id: service.id,
        name: service.name,
    };
    showDeleteModal.value = true;
}

function deleteService(id: number) {
    const form = useForm({});
    const serviceId = selectedService.value.id;

    form.delete('/admin/services/' + id, {
        onSuccess: () => {
            filteredServices.value = filteredServices.value.filter(
                (s) => s.id !== serviceId,
            );

            searchResults.value = searchResults.value.filter(
                (s) => s.id !== serviceId,
            );

            showDeleteModal.value = false;
        },
        onFinish: () => {
            selectedService.value = { id: 0, name: '' };
        },
    });
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Gestió de serveis" />

        <div class="space-y-6 p-6">
            <!-- HEADER -->
            <div
                class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between"
            >
                <div>
                    <h1 class="text-2xl font-bold">Gestió de Serveis</h1>
                    <p class="text-sm text-gray-500">
                        Administra tots els serveis disponibles
                    </p>
                    <div
                        v-if="$page.flash.message"
                        class="mx-auto mt-4 mb-4 w-full rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700"
                        role="alert"
                    >
                        <p>{{ $page.flash.message }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <label for="search-services" class="sr-only">
                        Cercar servei
                    </label>

                    <input
                        id="search-services"
                        name="search"
                        :value="search"
                        @input="handleSearch"
                        type="text"
                        placeholder="Cercar servei..."
                        autocomplete="off"
                        class="rounded-lg border px-4 py-2 focus:ring focus:ring-blue-200"
                    />

                    <Link
                        :href="`/admin/services/create`"
                        class="flex items-center gap-2 rounded-lg px-4 py-2 text-white"
                        style="background-color: #2563eb"
                        aria-label="Crear nou servei"
                    >
                        <Plus class="h-4 w-4" />
                        Nou servei
                    </Link>
                </div>
            </div>

            <!-- TABLE -->
            <div class="overflow-hidden rounded-xl border bg-white shadow">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100 text-gray-600">
                        <tr>
                            <th class="p-3 text-left">Servei</th>
                            <th class="p-3 text-left">Descripció</th>
                            <th class="p-3 text-left">Durada</th>
                            <th class="p-3 text-right">Accions</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr
                            v-for="service in filteredServices"
                            :key="service.id"
                            class="border-t hover:bg-gray-50"
                        >
                            <td class="p-3">
                                <div class="flex items-center gap-2">
                                    <component
                                        v-if="
                                            service.icon &&
                                            iconMap[service.icon]
                                        "
                                        :is="iconMap[service.icon]"
                                        class="h-5 w-5 text-blue-600"
                                    />
                                    <span class="font-medium">
                                        {{ service.name }}
                                    </span>
                                </div>
                            </td>

                            <td
                                class="p-3 text-gray-600"
                                v-html="service.description"
                            ></td>

                            <td class="p-3">
                                {{ service.duration_minutes }} min
                            </td>

                            <td class="p-3">
                                <div class="flex flex-wrap justify-end gap-2">
                                    <Link
                                        :href="`/admin/services/${service.id}`"
                                        class="rounded p-2 hover:bg-green-100"
                                        aria-label="Gestionar horaris del servei"
                                    >
                                        <CalendarPlus
                                            class="h-4 w-4 text-blue-600"
                                        />
                                    </Link>

                                    <Link
                                        :href="`/admin/services/${service.id}/edit`"
                                        class="rounded p-2 hover:bg-orange-100"
                                        aria-label="Editar servei"
                                    >
                                        <SquarePen
                                            class="h-4 w-4 text-orange-600"
                                        />
                                    </Link>

                                    <!---   <Link :href="`/admin/service-schedules/create?service_id=${service.id}`"
                                        class="p-2 rounded hover:bg-blue-100">
                                        <CalendarPlus class="w-4 h-4 text-blue-600" />
                                    </Link>--->

                                    <button
                                        @click="openDeleteModal(service)"
                                        class="rounded p-2 hover:bg-red-100"
                                        aria-label="Eliminar servei"
                                    >
                                        <Trash2 class="h-4 w-4 text-red-600" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- DELETE MODAL -->
        <div
            v-show="showDeleteModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
        >
            <div class="w-full max-w-md space-y-4 rounded-xl bg-white p-6">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-bold">Eliminar servei</h2>

                    <button
                        @click="showDeleteModal = false"
                        aria-label="Tancar finestra"
                        type="button"
                    >
                        <span aria-hidden="true">✕</span>
                    </button>
                </div>

                <p class="text-gray-600">
                    Estàs segur que vols eliminar el servei:
                </p>

                <p class="font-semibold break-words">
                    {{ selectedService.name }}
                </p>

                <div class="flex justify-end gap-3 pt-4">
                    <button
                        @click="showDeleteModal = false"
                        class="rounded border px-4 py-2"
                    >
                        Cancel·lar
                    </button>

                    <button
                        @click="deleteService(selectedService.id)"
                        class="rounded bg-red-600 px-4 py-2 text-white hover:bg-red-700"
                    >
                        Eliminar definitivament
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
