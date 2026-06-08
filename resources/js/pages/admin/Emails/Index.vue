<script setup lang="ts">
import { useForm, Link, router } from '@inertiajs/vue3';
import { ListFilter, Plus, Trash2 } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import ConfirmDeleteDialog from '@/components/ConfirmDeleteDialog.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { create as emailsCreate, destroy as emailsDestroy, index as emailsIndex, update as emailsUpdate } from '@/routes/emails';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Administració dels Correus', href: emailsIndex().url },
];

const props = defineProps<{
    emails?: {
        id: number;
        email: string;
        active: number;
    }[];
}>();

const displayedEmails = ref(props.emails ?? []);
const search = ref('');
const fetchedEmails = ref(props.emails ?? []);

watch(
    () => props.emails,
    (newEmails) => {
        fetchedEmails.value = newEmails ?? [];
        doSearch();
    },
);

function normalize(str: string) {
    return str
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .toLowerCase();
}

function doSearch() {
    const s = normalize(search.value);
    displayedEmails.value = fetchedEmails.value.filter((email) =>
        normalize(email.email).includes(s),
    );
}

const handleFilterChange = async (event: Event) => {
    const filter = (event.target as HTMLSelectElement).value;
    const url =
        {
            Tot: emailsIndex().url,
            Actiu: emailsIndex({ query: { filter: 'active' } }).url,
            Inactiu: emailsIndex({ query: { filter: 'inactive' } }).url,
        }[filter] ?? emailsIndex().url;
    const response = await fetch(url, {
        headers: { Accept: 'application/json' },
    });
    displayedEmails.value = await response.json();
};

const selectedEmailIds = ref<number[]>(
    props.emails
        ?.filter((email) => email.active === 1)
        .map((email) => email.id) ?? [],
);

const form = useForm({
    active: 0,
});

const handleChange = (emailId: number) => {
    const isActive = selectedEmailIds.value.includes(emailId);
    form.active = isActive ? 1 : 0;
    form.patch(emailsUpdate(emailId).url, { preserveScroll: true });
};

const emailToDelete = ref<number | null>(null);

const confirmDelete = () => {
    if (emailToDelete.value === null) return;
    router.delete(emailsDestroy(emailToDelete.value).url, { preserveScroll: true });
    emailToDelete.value = null;
};
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

            <!-- Header -->
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
                            Administració dels Correus
                        </h1>
                        <p class="mt-2 max-w-xl text-sm text-muted-foreground">
                            Gestiona els correus electrònics associats a les
                            notificacions de la farmàcia.
                        </p>
                        <p class="mt-2 max-w-xl text-sm text-muted-foreground">
                            Correus actius:
                            <span class="text-primary">
                                {{ selectedEmailIds.length }}/3
                            </span>
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
                        v-model="search"
                        @input="doSearch"
                        type="text"
                        placeholder="Cercar correu..."
                        class="w-full rounded-xl border border-sidebar-border/80 bg-background px-4 py-2 text-sm shadow-xs transition focus-visible:border-ring focus-visible:ring-2 focus-visible:ring-ring/30 focus-visible:outline-none sm:max-w-xs"
                    />

                    <div class="relative w-full sm:w-auto">
                        <span
                            class="pointer-events-none absolute top-1/2 left-3 -translate-y-1/2 text-muted-foreground"
                        >
                            <ListFilter class="h-4 w-4" />
                        </span>
                        <select
                            @change="handleFilterChange"
                            class="w-full appearance-none rounded-xl border border-sidebar-border/80 bg-background py-2 pr-9 pl-9 text-sm shadow-xs transition focus-visible:border-ring focus-visible:ring-2 focus-visible:ring-ring/30 focus-visible:outline-none sm:w-auto"
                        >
                            <option>Tot</option>
                            <option>Actiu</option>
                            <option>Inactiu</option>
                        </select>
                        <span
                            class="pointer-events-none absolute top-1/2 right-3 -translate-y-1/2 text-muted-foreground"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="14"
                                height="14"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <polyline points="6 9 12 15 18 9" />
                            </svg>
                        </span>
                    </div>

                    <Link
                        :href="emailsCreate().url"
                        class="group inline-flex items-center justify-center rounded-xl bg-primary px-4 py-2 text-sm font-medium text-primary-foreground shadow-sm transition hover:bg-primary/90 sm:ml-auto"
                    >
                        <Plus
                            class="mr-2 h-4 w-4 transition-transform group-hover:rotate-90"
                        />
                        Crear correu
                    </Link>
                </div>

                <!-- Table -->
                <div
                    class="overflow-hidden rounded-2xl border border-sidebar-border/70"
                >
                    <div class="overflow-x-auto">
                        <table
                            class="min-w-full divide-y divide-sidebar-border/70 text-sm"
                        >
                            <thead
                                class="bg-muted/50 backdrop-blur supports-[backdrop-filter]:bg-muted/100"
                            >
                                <tr>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-semibold uppercase"
                                    >
                                        Correu
                                    </th>

                                    <th
                                        class="px-6 py-4 text-left text-xs font-semibold uppercase"
                                    >
                                        Estat
                                    </th>

                                    <th
                                        class="px-6 py-4 text-left text-xs font-semibold uppercase"
                                    >
                                        Accions
                                    </th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr
                                    v-for="(email, index) in displayedEmails"
                                    :key="email.id"
                                    :class="[
                                        index % 2 === 0
                                            ? 'bg-background'
                                            : 'bg-muted/30',
                                        'border-t border-sidebar-border/60 transition hover:bg-muted/60',
                                    ]"
                                >
                                    <td
                                        class="px-6 py-4 font-medium text-foreground"
                                    >
                                        {{ email.email }}
                                    </td>

                                    <td class="px-6 py-4">
                                        <span
                                            :class="
                                                email.active === 1
                                                    ? 'border-green-200 bg-green-50 text-green-700'
                                                    : 'border-zinc-200 bg-zinc-100 text-zinc-600'
                                            "
                                            class="inline-flex rounded-full border px-3 py-1 text-xs font-semibold"
                                        >
                                            {{
                                                email.active === 1
                                                    ? 'Actiu'
                                                    : 'Inactiu'
                                            }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-4">
                                            <button
                                                type="button"
                                                class="transition hover:scale-105"
                                                @click="emailToDelete = email.id"
                                            >
                                                <Trash2
                                                    class="h-4 w-4 text-muted-foreground hover:text-red-500"
                                                />
                                            </button>

                                            <input
                                                type="checkbox"
                                                :disabled="
                                                    selectedEmailIds.length >=
                                                        3 &&
                                                    !selectedEmailIds.includes(
                                                        email.id,
                                                    )
                                                "
                                                :id="`email-${email.id}`"
                                                :value="email.id"
                                                v-model="selectedEmailIds"
                                                @change="handleChange(email.id)"
                                                class="h-4 w-4 rounded border-sidebar-border text-primary focus:ring-primary"
                                            />
                                        </div>
                                    </td>
                                </tr>

                                <tr
                                    v-if="
                                        displayedEmails &&
                                        displayedEmails.length === 0
                                    "
                                >
                                    <td
                                        colspan="3"
                                        class="px-6 py-12 text-center text-sm text-muted-foreground"
                                    >
                                        No s'han trobat correus.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <ConfirmDeleteDialog
            :open="emailToDelete !== null"
            title="Eliminar correu"
            description="Segur que vols eliminar aquesta adreça? Deixarà de rebre notificacions del sistema."
            @confirm="confirmDelete"
            @cancel="emailToDelete = null"
        />
    </AppLayout>
</template>
