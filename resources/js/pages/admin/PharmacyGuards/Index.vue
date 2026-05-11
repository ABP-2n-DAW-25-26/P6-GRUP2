<script setup lang="ts">
import { ref } from 'vue';
import { Form, Head, usePage, router } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import {
    destroy as destroyPharmacyGuard,
    index as pharmacyguardsIndex,
    store as storePharmacyGuard,
} from '@/routes/pharmacyguards';

interface Pharmacy {
    id: number;
    name: string;
}

interface Guard {
    id: number;
    date: string;
    pharmacy_id: number;
    pharmacy_name: string;
}

const props = defineProps<{
    pharmacies: Pharmacy[];
    guards: Guard[];
}>();

const page = usePage<{ flash?: { message?: string } }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Panell de guardies', href: pharmacyguardsIndex().url },
];

const removeGuard = (guardId: number) => {
    router.delete(destroyPharmacyGuard(guardId).url, {
        preserveScroll: true,
    });
};

const searchquery = ref<string>('');
const pharmacyguardsData = ref<Guard[]>(props.guards);
const dateFrom = ref<string>('');
const dateTo = ref<string>('');

const filterGuards = () => {
    const params = new URLSearchParams();

    if (searchquery.value.trim()) {
        params.append('search', searchquery.value.trim());
    }

    if (dateFrom.value) {
        params.append('date_from', dateFrom.value);
    }

    if (dateTo.value) {
        params.append('date_to', dateTo.value);
    }

    const queryString = params.toString();

    fetch(`/admin/pharmacyguards/filter${queryString ? `?${queryString}` : ''}`)
        .then((response) => response.json())
        .then((data) => {
            pharmacyguardsData.value = data.pharmacyguards ?? [];
        })
        .catch((error) => {
            console.error('Error', error);
        });
};

const resetDateFilter = () => {
    dateFrom.value = '';
    dateTo.value = '';
    filterGuards();
};
</script>
<template>
    <Head title="Panell de guardies" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="relative flex h-full flex-1 flex-col gap-4 overflow-x-auto p-4 md:p-6"
        >
            <div
                class="relative rounded-2xl border border-sidebar-border/70 bg-gradient-to-br from-background to-muted/70 p-7 shadow-sm"
            >
                <p
                    class="text-xs font-semibold tracking-[0.18em] text-muted-foreground uppercase"
                >
                    Farmacia Soler
                </p>
                <h1
                    class="mt-2 text-3xl font-semibold tracking-tight text-foreground"
                >
                    Panell de guardies
                </h1>
                <p class="mt-2 max-w-2xl text-sm text-muted-foreground">
                    Aquí podràs gestionar les guardies de la farmàcia, i
                    assegurar-te que tot estigui cobert de manera eficient.
                </p>
            </div>

            <div
                v-if="page.props.flash?.message"
                class="rounded-xl border border-green-200 bg-green-50/90 px-4 py-3 text-sm text-green-700 shadow-sm"
                role="alert"
            >
                {{ page.props.flash?.message }}
            </div>

            <div
                class="relative rounded-2xl border border-sidebar-border/70 bg-background/95 p-6 shadow-sm"
            >
                <h2 class="text-lg font-semibold text-foreground">
                    Crear guàrdia
                </h2>

                <Form
                    v-bind="storePharmacyGuard.form()"
                    :reset-on-success="['date', 'pharmacy_id']"
                    @success="filterGuards"
                    v-slot="{ errors, processing }"
                    class="mt-4 grid gap-5"
                >
                    <div class="grid gap-2">
                        <Label for="date">Data</Label>
                        <Input id="date" type="date" name="date" required />
                        <InputError :message="errors.date" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="pharmacy_id">Farmàcia</Label>
                        <select
                            id="pharmacy_id"
                            name="pharmacy_id"
                            required
                            class="w-full rounded-xl border border-sidebar-border/80 bg-background px-3 py-2 text-sm shadow-xs transition focus-visible:border-ring focus-visible:ring-2 focus-visible:ring-ring/30 focus-visible:outline-none"
                        >
                            <option value="">Selecciona una farmàcia</option>
                            <option
                                v-for="pharmacy in props.pharmacies"
                                :key="pharmacy.id"
                                :value="pharmacy.id.toString()"
                            >
                                {{ pharmacy.name }}
                            </option>
                        </select>
                        <InputError :message="errors.pharmacy_id" />
                    </div>

                    <div class="mt-2 flex items-center justify-end gap-3">
                        <Button
                            type="submit"
                            :disabled="processing"
                            class="bg-primary text-primary-foreground hover:bg-primary/90"
                        >
                            {{ processing ? 'Creant...' : 'Afegir guàrdia' }}
                        </Button>
                    </div>
                </Form>
            </div>

            <div
                class="relative rounded-2xl border border-sidebar-border/70 bg-background/95 p-5 shadow-sm"
            >
                <h2 class="text-lg font-semibold text-foreground">
                    Guàrdies programades
                </h2>

                <div
                    class="mt-3 rounded-xl border border-sidebar-border/70 bg-muted/25 p-4"
                >
                    <p
                        class="mb-3 text-xs font-semibold tracking-[0.14em] text-muted-foreground uppercase"
                    >
                        Filtres
                    </p>

                    <div class="grid gap-3 md:grid-cols-3">
                        <div class="grid gap-1.5">
                            <Label for="date_from">Des de</Label>
                            <Input
                                id="date_from"
                                v-model="dateFrom"
                                type="date"
                                @change="filterGuards"
                            />
                        </div>

                        <div class="grid gap-1.5">
                            <Label for="date_to">Fins a</Label>
                            <Input
                                id="date_to"
                                v-model="dateTo"
                                type="date"
                                @change="filterGuards"
                            />
                        </div>

                        <div class="flex items-end">
                            <Button
                                type="button"
                                variant="outline"
                                class="w-full"
                                @click="resetDateFilter"
                            >
                                Netejar dates
                            </Button>
                        </div>
                    </div>
                </div>

                <div class="relative mt-2">
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
                        @keyup="filterGuards()"
                        aria-labelledby="search"
                        type="search"
                        placeholder="Filtra pel nom d'una farmàcia"
                        class="w-full rounded-xl border border-sidebar-border/80 bg-background py-2.5 pr-4 pl-10 text-sm text-foreground shadow-xs transition focus-visible:border-ring focus-visible:ring-2 focus-visible:ring-ring/30 focus-visible:outline-none"
                        aria-label="Cercar farmàcies de guàrdia"
                    />
                </div>

                <div class="mt-4 overflow-x-auto">
                    <table
                        class="min-w-full divide-y divide-sidebar-border/70 text-sm"
                    >
                        <thead class="bg-muted/40">
                            <tr>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold tracking-wide text-foreground/80 uppercase"
                                >
                                    Data
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold tracking-wide text-foreground/80 uppercase"
                                >
                                    Farmàcia
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold tracking-wide text-foreground/80 uppercase"
                                >
                                    Accions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-sidebar-border/70">
                            <tr
                                v-for="guard in pharmacyguardsData"
                                :key="guard.id"
                                class="transition-colors hover:bg-muted/30"
                            >
                                <td class="px-4 py-3 text-muted-foreground">
                                    {{
                                        new Date(guard.date).toLocaleDateString(
                                            'ca-ES',
                                        )
                                    }}
                                </td>
                                <td class="px-4 py-3 font-medium">
                                    {{ guard.pharmacy_name }}
                                </td>
                                <td class="px-4 py-3">
                                    <button
                                        type="button"
                                        class="cursor-pointer rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700 transition hover:bg-red-100"
                                        @click="removeGuard(guard.id)"
                                    >
                                        Eliminar
                                    </button>
                                </td>
                            </tr>

                            <tr v-if="pharmacyguardsData.length === 0">
                                <td
                                    colspan="3"
                                    class="px-4 py-8 text-center text-muted-foreground"
                                >
                                    Encara no hi ha guàrdies creades.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
