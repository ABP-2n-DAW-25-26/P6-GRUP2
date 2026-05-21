<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ListFilter, SquarePen, Trash2 } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import ConfirmDeleteDialog from '@/components/ConfirmDeleteDialog.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { admindashboard as dashboard } from '@/routes';
import {
    create as usersCreate,
    destroy as usersDestroy,
    edit as usersEdit,
} from '@/routes/users';

type User = {
    id: number;
    name: string;
    email: string;
    nickname?: string | null;
    role?: string | null;
    created_at?: string | null;
};

const props = defineProps<{
    users: User[];
    currentUser: User;
}>();

const page = usePage<{ flash?: { message?: string } }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: "Administració d'usuaris", href: dashboard().url },
];

const searchQuery = ref('');
const selectedRole = ref('');
const currentPage = ref(1);
const perPage = 8;

const filteredUsers = computed(() => {
    const query = searchQuery.value.trim().toLowerCase();

    return props.users
        .filter((user) => user.id !== props.currentUser.id)
        .filter((user) => {
            if (!query) {
                return true;
            }

            return (
                user.name.toLowerCase().includes(query) ||
                user.email.toLowerCase().includes(query) ||
                (user.nickname ?? '').toLowerCase().includes(query)
            );
        })
        .filter((user) => {
            if (!selectedRole.value) {
                return true;
            }

            return (user.role ?? '') === selectedRole.value;
        });
});

const totalPages = computed(() =>
    Math.max(1, Math.ceil(filteredUsers.value.length / perPage)),
);

const visibleUsers = computed(() => {
    const start = (currentPage.value - 1) * perPage;

    return filteredUsers.value.slice(start, start + perPage);
});

const canGoPrev = computed(() => currentPage.value > 1);
const canGoNext = computed(() => currentPage.value < totalPages.value);

const goPrev = () => {
    if (canGoPrev.value) {
        currentPage.value -= 1;
    }
};

const goNext = () => {
    if (canGoNext.value) {
        currentPage.value += 1;
    }
};

const resetPage = () => {
    currentPage.value = 1;
};

const roleBadgeClass = (role?: string | null) => {
    if (role === 'superadmin') {
        return 'border-zinc-300 bg-zinc-100 text-zinc-800';
    }

    if (role === 'admin') {
        return 'border-zinc-300 bg-zinc-50 text-zinc-700';
    }

    return 'border-sidebar-border/80 bg-muted text-muted-foreground';
};

const roleLabel = (role?: string | null) => {
    if (role === 'superadmin') {
        return 'Superadmin';
    }

    if (role === 'admin') {
        return 'Admin';
    }

    return '—';
};

const userToDelete = ref<User | null>(null);

const deleteUser = (user: User) => {
    userToDelete.value = user;
};

const confirmDelete = () => {
    if (userToDelete.value === null) return;
    router.delete(usersDestroy(userToDelete.value.id).url);
    userToDelete.value = null;
};

const cancelDelete = () => {
    userToDelete.value = null;
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">

        <Head title="Administració d'usuaris" />

        <div class="relative flex h-full flex-1 flex-col gap-6 overflow-x-auto p-4 md:p-6">
            <!-- Decorative blurred gradients -->
            <div
                class="pointer-events-none absolute top-0 right-8 h-56 w-56 rounded-full bg-gradient-to-br from-primary/20 to-muted/70 blur-3xl">
            </div>
            <div
                class="pointer-events-none absolute bottom-10 left-0 h-64 w-64 rounded-full bg-gradient-to-tr from-secondary/60 to-primary/10 blur-3xl">
            </div>

            <!--  header -->
            <div
                class="relative overflow-hidden rounded-2xl border border-sidebar-border/70 bg-gradient-to-br from-background via-background to-muted/60 p-7 shadow-sm">
                <div
                    class="pointer-events-none absolute -top-12 -right-12 h-40 w-40 rounded-full bg-primary/10 blur-2xl">
                </div>

                <div class="relative flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p
                            class="inline-flex items-center gap-2 rounded-full border border-sidebar-border/70 bg-background/80 px-3 py-1 text-[10px] font-semibold tracking-[0.18em] text-muted-foreground uppercase shadow-xs backdrop-blur">
                            <span class="inline-block h-1.5 w-1.5 rounded-full bg-primary"></span>
                            Farmacia Soler
                        </p>
                        <h1 class="mt-3 text-3xl font-semibold tracking-tight text-foreground sm:text-4xl">
                            Administració d'usuaris
                        </h1>
                        <p class="mt-2 max-w-xl text-sm text-muted-foreground">
                            Visualitza i gestiona els usuaris interns de manera
                            ràpida i clara.
                        </p>
                    </div>


                </div>
            </div>

            <!-- Flash message -->
            <transition enter-active-class="transition duration-300 ease-out"
                enter-from-class="opacity-0 -translate-y-1" enter-to-class="opacity-100 translate-y-0"
                leave-active-class="transition duration-200 ease-in" leave-from-class="opacity-100"
                leave-to-class="opacity-0">
                <div v-if="page.props.flash?.message"
                    class="flex items-center gap-3 rounded-xl border border-green-200 bg-green-50/90 px-4 py-3 text-sm text-green-700 shadow-sm"
                    role="alert">
                    <span
                        class="inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-green-100 text-green-700">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12" />
                        </svg>
                    </span>
                    {{ page.props.flash?.message }}
                </div>
            </transition>

            <!-- Main panel -->
            <div class="relative rounded-2xl border border-sidebar-border/70 bg-background/95 p-5 shadow-sm md:p-6">
                <!-- Toolbar -->
                <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center">
                    <div class="relative w-full sm:max-w-xs">
                        <span
                            class="pointer-events-none absolute top-1/2 left-3 -translate-y-1/2 text-muted-foreground">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8" />
                                <path d="m21 21-4.3-4.3" />
                            </svg>
                        </span>
                        <input v-model="searchQuery" @input="resetPage" type="search"
                            placeholder="Cerca per nom, email o nickname"
                            class="w-full rounded-xl border border-sidebar-border/80 bg-background py-2 pr-3 pl-9 text-sm shadow-xs transition focus-visible:border-ring focus-visible:ring-2 focus-visible:ring-ring/30 focus-visible:outline-none" />
                    </div>

                    <div class="relative w-full sm:w-auto">
                        <span
                            class="pointer-events-none absolute top-1/2 left-3 -translate-y-1/2 text-muted-foreground">
                            <ListFilter class="h-4 w-4" />
                        </span>
                        <select v-model="selectedRole" @change="resetPage"
                            class="w-full appearance-none rounded-xl border border-sidebar-border/80 bg-background py-2 pr-9 pl-9 text-sm shadow-xs transition focus-visible:border-ring focus-visible:ring-2 focus-visible:ring-ring/30 focus-visible:outline-none sm:w-auto">
                            <option value="">Tots els rols</option>
                            <option value="admin">Admin</option>
                            <option value="superadmin">Superadmin</option>
                        </select>
                        <span
                            class="pointer-events-none absolute top-1/2 right-3 -translate-y-1/2 text-muted-foreground">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <polyline points="6 9 12 15 18 9" />
                            </svg>
                        </span>
                    </div>

                    <Link :href="usersCreate().url"
                        class="group inline-flex w-full items-center justify-center gap-2 rounded-xl bg-primary px-4 py-2 text-sm font-medium text-primary-foreground shadow-sm transition hover:bg-primary/90 hover:shadow-md focus-visible:ring-2 focus-visible:ring-ring/40 focus-visible:outline-none sm:ml-auto sm:w-auto">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                            class="transition-transform group-hover:rotate-90">
                            <path d="M5 12h14" />
                            <path d="M12 5v14" />
                        </svg>
                        Crear usuari
                    </Link>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto rounded-xl border border-sidebar-border/60">
                    <table class="min-w-full divide-y divide-sidebar-border/70 text-sm">
                        <thead class="bg-muted/50 backdrop-blur supports-[backdrop-filter]:bg-muted/100">
                            <tr>
                                <th
                                    class="px-4 py-3 text-left text-[11px] font-semibold tracking-wider text-foreground/80 uppercase">
                                    Nom
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-[11px] font-semibold tracking-wider text-foreground/80 uppercase">
                                    Email
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-[11px] font-semibold tracking-wider text-foreground/80 uppercase">
                                    Usuari
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-[11px] font-semibold tracking-wider text-foreground/80 uppercase">
                                    Rol
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-[11px] font-semibold tracking-wider text-foreground/80 uppercase">
                                    Creat el
                                </th>
                                <th
                                    class="px-4 py-3 text-right text-[11px] font-semibold tracking-wider text-foreground/80 uppercase">
                                    Accions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-sidebar-border/60">
                            <tr v-for="user in visibleUsers" :key="user.id"
                                class="group transition-colors hover:bg-muted/40">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <span
                                            class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-primary/15 to-secondary/40 text-xs font-semibold text-foreground ring-1 ring-sidebar-border/60">
                                            {{
                                                user.name
                                                    .split(' ')
                                                    .map((n) => n[0])
                                                    .join('')
                                                    .slice(0, 2)
                                                    .toUpperCase()
                                            }}
                                        </span>
                                        <span class="font-medium">{{
                                            user.name
                                            }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-muted-foreground">
                                    <a :href="`mailto:${user.email}`"
                                        class="transition-colors hover:text-foreground hover:underline">
                                        {{ user.email }}
                                    </a>
                                </td>
                                <td class="px-4 py-3 text-muted-foreground">
                                    {{ user.nickname || '—' }}
                                </td>
                                <td class="px-4 py-3 text-muted-foreground">
                                    <span
                                        class="inline-flex items-center gap-1.5 rounded-full border px-2.5 py-0.5 text-xs font-semibold"
                                        :class="roleBadgeClass(user.role)">
                                        <span
                                            class="inline-block h-1.5 w-1.5 rounded-full bg-current opacity-60"></span>
                                        {{ roleLabel(user.role) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-muted-foreground">
                                    {{
                                        user.created_at
                                            ? new Date(
                                                user.created_at,
                                            ).toLocaleString('ca-ES')
                                            : '—'
                                    }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <Link
                                            :href="usersEdit(user.id).url"
                                            class="inline-flex items-center rounded-lg p-2 text-muted-foreground transition hover:bg-orange-50 hover:text-orange-600"
                                            aria-label="Editar usuari"
                                        >
                                            <SquarePen class="h-4 w-4" />
                                        </Link>
                                        <button
                                            type="button"
                                            @click="deleteUser(user)"
                                            class="inline-flex items-center rounded-lg p-2 text-muted-foreground transition hover:bg-red-50 hover:text-red-600"
                                            aria-label="Eliminar usuari"
                                        >
                                            <Trash2 class="h-4 w-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="visibleUsers.length === 0">
                                <td colspan="6" class="px-4 py-12 text-center">
                                    <div
                                        class="mx-auto flex max-w-xs flex-col items-center gap-3 text-muted-foreground">
                                        <span
                                            class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-muted">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="11" cy="11" r="8" />
                                                <path d="m21 21-4.3-4.3" />
                                            </svg>
                                        </span>
                                        <p class="text-sm font-medium text-foreground">
                                            Cap resultat
                                        </p>
                                        <p class="text-xs">
                                            No s'han trobat usuaris amb aquests
                                            filtres.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-xs text-muted-foreground">
                        Pàgina
                        <span class="font-semibold text-foreground">{{
                            currentPage
                            }}</span>
                        de
                        <span class="font-semibold text-foreground">{{
                            totalPages
                            }}</span>
                    </p>
                    <div class="flex gap-2">
                        <button
                            class="inline-flex items-center gap-1.5 rounded-lg border border-sidebar-border bg-background px-3 py-1.5 text-sm transition hover:bg-sidebar-accent/45 disabled:cursor-not-allowed disabled:opacity-40"
                            :disabled="!canGoPrev" @click="goPrev">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <polyline points="15 18 9 12 15 6" />
                            </svg>
                            Anterior
                        </button>
                        <button
                            class="inline-flex items-center gap-1.5 rounded-lg border border-sidebar-border bg-background px-3 py-1.5 text-sm transition hover:bg-sidebar-accent/45 disabled:cursor-not-allowed disabled:opacity-40"
                            :disabled="!canGoNext" @click="goNext">
                            Següent
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <polyline points="9 18 15 12 9 6" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <ConfirmDeleteDialog
            :open="userToDelete !== null"
            title="Eliminar usuari"
            :description="`Segur que vols eliminar l'usuari &quot;${userToDelete?.name}&quot;? Aquesta acció no es pot desfer.`"
            @confirm="confirmDelete"
            @cancel="cancelDelete"
        />
    </AppLayout>
</template>