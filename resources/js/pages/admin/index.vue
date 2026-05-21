
<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { admindashboard as dashboard } from '@/routes';

const breadcrumbs: BreadcrumbItem[] = [
    { title: "Pantalla d'inici", href: dashboard().url },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Admin" />

        <div
            class="relative flex h-full flex-1 flex-col gap-4 overflow-x-auto p-4 md:p-6"
        >
            <div
                class="pointer-events-none absolute top-0 right-8 h-48 w-48 rounded-full bg-muted/70 blur-3xl"
            ></div>
            <div
                class="pointer-events-none absolute bottom-0 left-0 h-56 w-56 rounded-full bg-secondary/60 blur-3xl"
            ></div>

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
                    Panell d'administració
                </h1>
                <p class="mt-2 max-w-2xl text-sm text-muted-foreground">
                    Gestiona usuaris i configuració interna des d'un únic espai,
                    amb un entorn visual alineat amb la marca.
                </p>
            </div>
        </div>
    </AppLayout>
</template>
