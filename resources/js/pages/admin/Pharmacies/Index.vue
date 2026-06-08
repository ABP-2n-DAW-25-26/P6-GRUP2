<script setup lang="ts">
import { Form, Head, router, usePage } from '@inertiajs/vue3';
import { Plus, Trash2 } from 'lucide-vue-next';
import { ref } from 'vue';
import { watch } from 'vue';
import ConfirmDeleteDialog from '@/components/ConfirmDeleteDialog.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import {
    destroy as pharmaciesDestroy,
    filter as pharmaciesFilter,
    index as pharmaciesIndex,
    store as pharmaciesStore,
} from '@/routes/pharmacies';

interface Pharmacy {
    id: number;
    name: string;
    latitude: number;
    longitude: number;
    created_at?: string | null;
}

const props = defineProps<{
    pharmacies: Pharmacy[];
}>();

const page = usePage<{ flash?: { message?: string } }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Farmàcies', href: pharmaciesIndex().url },
];

const searchquery = ref<string>('');
const pharmaciesData = ref<Pharmacy[]>(props.pharmacies);

watch(
    () => props.pharmacies,
    (newVal) => {
        pharmaciesData.value = newVal;
    },
);

const filterPharmacies = () => {
    const searchqueryquery = searchquery.value
        ? `?search=${encodeURIComponent(searchquery.value)}`
        : '';

    fetch(`${pharmaciesFilter().url}${searchqueryquery}`)
        .then((response) => response.json())
        .then((data) => {
            pharmaciesData.value = data.pharmacies ?? [];
        })
        .catch((error) => {
            console.error('Error', error);
        });
};

const pharmacyToDelete = ref<number | null>(null);

const removePharmacy = (pharmacyId: number) => {
    pharmacyToDelete.value = pharmacyId;
};

const confirmDelete = () => {
    if (pharmacyToDelete.value === null) {
        return;
    }

    router.delete(pharmaciesDestroy(pharmacyToDelete.value).url, {
        preserveScroll: true,
    });
    pharmacyToDelete.value = null;
};

const cancelDelete = () => {
    pharmacyToDelete.value = null;
};
</script>
<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Farmàcies" />

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
                            Farmàcies
                        </h1>
                        <p class="mt-2 max-w-xl text-sm text-muted-foreground">
                            Gestiona les farmàcies associades a la teva
                            aplicació.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Flash -->
            <div
                v-if="page.props.flash?.message"
                class="rounded-xl border border-green-200 bg-green-50/90 px-4 py-3 text-sm text-green-700 shadow-sm"
                role="alert"
            >
                {{ page.props.flash?.message }}
            </div>

            <!-- Form Card -->
            <div
                class="relative rounded-2xl border border-sidebar-border/70 bg-background/95 p-6 shadow-sm"
            >
                <Form
                    v-bind="pharmaciesStore.form()"
                    :reset-on-success="['name', 'latitude', 'longitude']"
                    v-slot="{ errors, processing }"
                    class="grid gap-5 md:grid-cols-2"
                >
                    <!-- Name -->
                    <div class="grid gap-2 md:col-span-2">
                        <Label for="name"> Nom </Label>

                        <Input
                            id="name"
                            type="text"
                            name="name"
                            required
                            autofocus
                            placeholder="Nom de la farmàcia"
                        />

                        <InputError :message="errors.name" />
                    </div>

                    <!-- Latitude -->
                    <div class="grid gap-2">
                        <Label for="latitude"> Latitud </Label>

                        <Input
                            id="latitude"
                            type="number"
                            step="0.0000001"
                            name="latitude"
                            required
                            placeholder="42.2655337"
                        />

                        <InputError :message="errors.latitude" />
                    </div>

                    <!-- Longitude -->
                    <div class="grid gap-2">
                        <Label for="longitude"> Longitud </Label>

                        <Input
                            id="longitude"
                            type="number"
                            step="0.0000001"
                            name="longitude"
                            required
                            placeholder="2.9631538"
                        />

                        <InputError :message="errors.longitude" />
                    </div>

                    <!-- Submit -->
                    <div class="mt-2 flex justify-end md:col-span-2">
                        <Button
                            type="submit"
                            :disabled="processing"
                            class="group bg-primary text-primary-foreground hover:bg-primary/90"
                        >
                            <Plus
                                class="mr-2 h-4 w-4 transition-transform group-hover:rotate-90"
                            />
                            {{ processing ? 'Creant...' : 'Crear farmàcia' }}
                        </Button>
                    </div>
                </Form>
            </div>

            <!-- Table Card -->
            <div
                class="relative rounded-2xl border border-sidebar-border/70 bg-background/95 p-6 shadow-sm"
            >
                <!-- Header -->
                <div
                    class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center"
                >
                    <div>
                        <h2 class="text-lg font-semibold text-foreground">
                            Farmàcies registrades
                        </h2>

                        <p class="text-sm text-muted-foreground">
                            Consulta i elimina farmàcies existents.
                        </p>
                    </div>

                    <!-- Search -->
                    <div class="relative w-full sm:ml-auto sm:max-w-xs">
                        <svg
                            class="pointer-events-none absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="2"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="m21 21-4.35-4.35m1.85-5.15a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"
                            />
                        </svg>

                        <input
                            v-model="searchquery"
                            @keyup="filterPharmacies()"
                            type="search"
                            placeholder="Cercar farmàcia..."
                            class="w-full rounded-xl border border-sidebar-border/80 bg-background py-2 pr-4 pl-10 text-sm shadow-xs transition focus-visible:border-ring focus-visible:ring-2 focus-visible:ring-ring/30 focus-visible:outline-none"
                        />
                    </div>
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
                                        Farmàcia
                                    </th>

                                    <th
                                        class="px-6 py-4 text-left text-xs font-semibold uppercase"
                                    >
                                        Latitud
                                    </th>

                                    <th
                                        class="px-6 py-4 text-left text-xs font-semibold uppercase"
                                    >
                                        Longitud
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
                                    v-for="(pharmacy, index) in pharmaciesData"
                                    :key="pharmacy.id"
                                    :class="[
                                        index % 2 === 0
                                            ? 'bg-background'
                                            : 'bg-muted/30',
                                        'border-t border-sidebar-border/60 transition hover:bg-muted/60',
                                    ]"
                                >
                                    <!-- Name -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="flex h-10 w-10 items-center justify-center rounded-full bg-primary/10 text-xs font-semibold text-primary"
                                            >
                                                {{
                                                    pharmacy.name
                                                        .charAt(0)
                                                        .toUpperCase()
                                                }}
                                            </div>

                                            <div
                                                class="font-medium text-foreground"
                                            >
                                                {{ pharmacy.name }}
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Latitude -->
                                    <td class="px-6 py-4 text-muted-foreground">
                                        {{ pharmacy.latitude }}
                                    </td>

                                    <!-- Longitude -->
                                    <td class="px-6 py-4 text-muted-foreground">
                                        {{ pharmacy.longitude }}
                                    </td>

                                    <!-- Actions -->
                                    <td class="px-6 py-4">
                                        <div class="flex justify-end">
                                            <button
                                                type="button"
                                                class="inline-flex items-center rounded-lg p-2 text-muted-foreground transition hover:bg-red-50 hover:text-red-600"
                                                aria-label="Eliminar farmàcia"
                                                @click="
                                                    removePharmacy(pharmacy.id)
                                                "
                                            >
                                                <Trash2 class="h-4 w-4" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Empty -->
                                <tr v-if="pharmaciesData.length === 0">
                                    <td
                                        colspan="4"
                                        class="px-6 py-12 text-center text-sm text-muted-foreground"
                                    >
                                        Encara no hi ha farmàcies.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <ConfirmDeleteDialog
            :open="pharmacyToDelete !== null"
            title="Eliminar farmàcia"
            description="Segur que vols eliminar aquesta farmàcia? Aquesta acció no es pot desfer."
            @confirm="confirmDelete"
            @cancel="cancelDelete"
        />
    </AppLayout>
</template>
