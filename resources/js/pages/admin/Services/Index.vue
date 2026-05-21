<script setup lang="ts">
import { Link, Head, useForm } from '@inertiajs/vue3';
import { CalendarPlus, Plus, SquarePen, Trash2 } from 'lucide-vue-next';
import { ref, computed } from 'vue';
import ConfirmDeleteDialog from '@/components/ConfirmDeleteDialog.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { admindashboard as dashboard } from '@/routes';

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
// PAGINATION
const page = ref(1);
const perPage = 7;

const visibleServices = computed(() => {
    const start = (page.value - 1) * perPage;
    const end = start + perPage;

    return filteredServices.value.slice(start, end);
});

const totalPages = computed(() =>
    Math.ceil(filteredServices.value.length / perPage),
);

// const hasMore = computed(() => page.value < totalPages.value);

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
</script>
<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Gestió de serveis" />

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
                            Administració de serveis
                        </h1>
                        <p class="mt-2 max-w-xl text-sm text-muted-foreground">
                            Administra tots els serveis disponibles de la
                            farmàcia.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Main Card -->
            <div
                class="relative rounded-2xl border border-sidebar-border/70 bg-background/95 p-6 shadow-sm"
            >
                <!-- Toolbar -->
                <div
                    class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center"
                >
                    <input
                        id="search-services"
                        name="search"
                        :value="search"
                        @input="handleSearch"
                        type="text"
                        placeholder="Cercar servei..."
                        autocomplete="off"
                        class="w-full rounded-xl border border-sidebar-border/80 bg-background px-4 py-2 text-sm shadow-xs transition focus-visible:border-ring focus-visible:ring-2 focus-visible:ring-ring/30 focus-visible:outline-none sm:max-w-xs"
                    />

                    <Link
                        :href="`/admin/services/create`"
                        class="group inline-flex items-center justify-center rounded-xl bg-primary px-4 py-2 text-sm font-medium text-primary-foreground shadow-sm transition hover:bg-primary/90 sm:ml-auto"
                    >
                        <Plus
                            class="mr-2 h-4 w-4 transition-transform group-hover:rotate-90"
                        />
                        Nou servei
                    </Link>
                </div>

                <!-- Table -->
                <div
                    class="overflow-hidden rounded-2xl border border-sidebar-border/70"
                >
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-muted/80">
                                <tr>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-semibold uppercase"
                                    >
                                        Servei
                                    </th>

                                    <th
                                        class="px-6 py-4 text-left text-xs font-semibold uppercase"
                                    >
                                        Descripció
                                    </th>

                                    <th
                                        class="px-6 py-4 text-left text-xs font-semibold uppercase"
                                    >
                                        Durada
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
                                    v-for="(service, index) in visibleServices"
                                    :key="service.id"
                                    :class="[
                                        index % 2 === 0
                                            ? 'bg-background'
                                            : 'bg-muted/30',
                                        'border-t border-sidebar-border/60 transition hover:bg-muted/60',
                                    ]"
                                >
                                    <!-- Service -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <span
                                                class="font-medium text-foreground"
                                            >
                                                {{ service.name }}
                                            </span>
                                        </div>
                                    </td>

                                    <!-- Description -->
                                    <td class="px-6 py-4 text-muted-foreground">
                                        <div
                                            class="max-w-md"
                                            v-html="service.description"
                                        ></div>
                                    </td>

                                    <!-- Duration -->
                                    <td class="px-6 py-4 text-muted-foreground">
                                        {{ service.duration_minutes }}
                                        min
                                    </td>

                                    <!-- Actions -->
                                    <td class="px-6 py-4">
                                        <div class="flex justify-end gap-2">
                                            <Link
                                                :href="`/admin/services/${service.id}`"
                                                class="inline-flex items-center rounded-lg p-2 text-muted-foreground transition hover:bg-blue-50 hover:text-blue-600"
                                                aria-label="Gestionar horaris"
                                            >
                                                <CalendarPlus class="h-4 w-4" />
                                            </Link>

                                            <Link
                                                :href="`/admin/services/${service.id}/edit`"
                                                class="inline-flex items-center rounded-lg p-2 text-muted-foreground transition hover:bg-orange-50 hover:text-orange-600"
                                                aria-label="Editar servei"
                                            >
                                                <SquarePen class="h-4 w-4" />
                                            </Link>

                                            <button
                                                @click="
                                                    openDeleteModal(service)
                                                "
                                                class="inline-flex items-center rounded-lg p-2 text-muted-foreground transition hover:bg-red-50 hover:text-red-600"
                                                aria-label="Eliminar servei"
                                            >
                                                <Trash2 class="h-4 w-4" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Empty -->
                                <tr v-if="visibleServices.length === 0">
                                    <td
                                        colspan="4"
                                        class="px-6 py-12 text-center text-sm text-muted-foreground"
                                    >
                                        No hi ha serveis disponibles.
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

        <ConfirmDeleteDialog
            :open="showDeleteModal"
            title="Eliminar servei"
            :description="`Segur que vols eliminar el servei &quot;${selectedService.name}&quot;? Aquesta acció no es pot desfer.`"
            @confirm="deleteService(selectedService.id)"
            @cancel="showDeleteModal = false"
        />
    </AppLayout>
</template>
