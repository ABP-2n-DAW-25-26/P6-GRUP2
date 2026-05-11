<script setup lang="ts">
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { CheckCheck, ClockArrowDown, Ban } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';

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
    if (!s) return props.assignments;
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
        <div class="flex items-center justify-center px-10 pt-5">
            <div class="relative flex w-full max-w-md justify-between">
                <input
                    type="text"
                    v-model="search"
                    placeholder="Search..."
                    class="w-full rounded-2xl border border-gray-300 bg-white py-2 pr-10 pl-4 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                />
                <svg
                    class="absolute top-1/2 right-3 h-4 w-4 -translate-y-1/2 text-gray-400"
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
        </div>

        <div class="grid grid-cols-1 gap-5 pr-10 md:grid-cols-2 lg:grid-cols-3">
            <div
                v-for="ass in filteredAssignments"
                :key="ass.name"
                class="relative m-6 flex w-full flex-col rounded-lg border border-slate-200 bg-white shadow-sm"
            >
                <div
                    class="mx-3 mb-0 flex flex-col border-b border-slate-200 px-1 pt-3 pb-2"
                >
                    <span
                        v-if="ass.status === 'completed'"
                        class="text-sm text-slate-600"
                    >
                        <strong>Estat:</strong> Completat
                    </span>
                    <span
                        v-if="ass.status === 'pending'"
                        class="text-sm text-slate-600"
                    >
                        <strong>Estat:</strong> Pendent
                    </span>
                    <span
                        v-if="ass.status === 'cancelled'"
                        class="text-sm text-slate-600"
                    >
                        <strong>Estat:</strong> Cancelat
                    </span>
                    <span class="text-sm text-slate-600">
                        <strong>Correu:</strong> {{ ass.address }}
                    </span>
                    <span class="text-sm text-slate-600">
                        <strong>Tél:</strong> {{ ass.phone_number }}
                    </span>
                </div>

                <div class="p-4">
                    <h5 class="mb-2 text-xl font-semibold text-slate-800">
                        {{ ass.name }}
                    </h5>
                    <p class="leading-normal font-light text-slate-600">
                        {{ ass.description }}
                    </p>
                </div>
                <div class="mx-3 border-t border-slate-200 px-1 pt-2 pb-3">
                    <span class="text-sm font-medium text-slate-600">
                        {{ ass.updated_at }}
                    </span>
                </div>
                <div
                    class="mx-auto mb-5 grid w-full max-w-7xl grid-cols-3 gap-1 px-3 text-center"
                >
                    <div class="group relative">
                        <Link
                            :href="`/admin/adminAssignments/${ass.id}`"
                            :data="{ status: 'pending' }"
                            preserve-scroll
                            method="patch"
                            class="flex w-full items-center justify-center rounded-md bg-gray-100 p-1 hover:bg-gray-200"
                        >
                            <ClockArrowDown />
                        </Link>
                        <div
                            class="pointer-events-none absolute -top-8 left-1/2 -translate-x-1/2 rounded bg-gray-800 px-2 py-1 text-xs whitespace-nowrap text-white opacity-0 transition-opacity group-hover:opacity-100"
                        >
                            Marcar com a pendent
                        </div>
                    </div>

                    <div class="group relative">
                        <Link
                            :href="`/admin/adminAssignments/${ass.id}`"
                            :data="{ status: 'cancelled' }"
                            preserve-scroll
                            method="patch"
                            class="flex w-full items-center justify-center rounded-md bg-gray-100 p-1 hover:bg-gray-200"
                        >
                            <Ban />
                        </Link>
                        <div
                            class="pointer-events-none absolute -top-8 left-1/2 -translate-x-1/2 rounded bg-gray-800 px-2 py-1 text-xs whitespace-nowrap text-white opacity-0 transition-opacity group-hover:opacity-100"
                        >
                            Marcar com a cencel·lat
                        </div>
                    </div>

                    <div class="group relative">
                        <Link
                            :href="`/admin/adminAssignments/${ass.id}`"
                            :data="{ status: 'completed' }"
                            preserve-scroll
                            method="patch"
                            class="flex w-full items-center justify-center rounded-md bg-gray-100 p-1 hover:bg-gray-200"
                        >
                            <CheckCheck />
                        </Link>
                        <div
                            class="pointer-events-none absolute -top-8 left-1/2 -translate-x-1/2 rounded bg-gray-800 px-2 py-1 text-xs whitespace-nowrap text-white opacity-0 transition-opacity group-hover:opacity-100"
                        >
                            Marcar com a completat
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
