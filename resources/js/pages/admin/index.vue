<script setup lang="ts">
import { computed } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import {
    ArrowRight,
    CalendarX,
    ClipboardPen,
    GraduationCap,
    Mail,
    Mails,
    Pill,
    Shield,
    UserCog,
    UsersRound,
} from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { admindashboard as dashboard } from '@/routes';
import { index as assignmentsIndex } from '@/routes/adminAssignments';
import { index as calendarExceptionsIndex } from '@/routes/calendar-exceptions';
import { index as emailsIndex } from '@/routes/emails';
import { index as mailIndex } from '@/routes/mail';
import { index as pharmaciesIndex } from '@/routes/pharmacies';
import { index as pharmacyguardsIndex } from '@/routes/pharmacyguards';
import { index as servicesIndex } from '@/routes/services';
import { index as usersIndex } from '@/routes/users';
import { index as workshopsIndex } from '@/routes/workshops';

const page = usePage();
const isSuperAdmin = page.props.auth.user.role === 'superadmin';

const breadcrumbs: BreadcrumbItem[] = [
    { title: "Pantalla d'inici", href: dashboard().url },
];

const allSections = [
    {
        title: 'Serveis',
        description: 'Configura els serveis i els seus horaris disponibles.',
        href: servicesIndex().url,
        icon: Pill,
        superAdminOnly: false,
    },
    {
        title: 'Encàrrecs',
        description: 'Gestiona els encàrrecs de serveis als usuaris.',
        href: assignmentsIndex().url,
        icon: ClipboardPen,
        superAdminOnly: false,
    },
    {
        title: 'Guàrdies',
        description: 'Consulta i gestiona les guàrdies programades.',
        href: pharmacyguardsIndex().url,
        icon: Shield,
        superAdminOnly: false,
    },
    {
        title: 'Farmàcies',
        description: "Administra les farmàcies associades a l'aplicació.",
        href: pharmaciesIndex().url,
        icon: UserCog,
        superAdminOnly: false,
    },
    {
        title: 'Tallers',
        description: 'Crea, edita i programa els tallers de la farmàcia.',
        href: workshopsIndex().url,
        icon: GraduationCap,
        superAdminOnly: false,
    },
    {
        title: 'Dies festius',
        description: 'Gestiona els dies festius i excepcions del calendari.',
        href: calendarExceptionsIndex().url,
        icon: CalendarX,
        superAdminOnly: false,
    },
    {
        title: 'Usuaris',
        description: 'Gestiona els comptes i rols dels administradors.',
        href: usersIndex().url,
        icon: UsersRound,
        superAdminOnly: true,
    },
    {
        title: 'Correus',
        description: 'Gestiona els correus de notificació actius.',
        href: emailsIndex().url,
        icon: Mails,
        superAdminOnly: true,
    },
    {
        title: 'Missatges',
        description: 'Envia comunicacions als usuaris registrats.',
        href: mailIndex().url,
        icon: Mail,
        superAdminOnly: true,
    },
];

const sections = computed(() =>
    allSections.filter((s) => !s.superAdminOnly || isSuperAdmin),
);
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Admin" />

        <div
            class="relative flex h-full flex-1 flex-col gap-6 overflow-x-auto p-4 md:p-6"
        >
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
                <div class="relative">
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
                        Panell d'administració
                    </h1>
                    <p class="mt-2 max-w-2xl text-sm text-muted-foreground">
                        Gestiona usuaris i configuració interna des d'un únic
                        espai.
                    </p>
                </div>
            </div>

            <!-- Quick access grid -->
            <div
                class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
            >
                <Link
                    v-for="section in sections"
                    :key="section.href"
                    :href="section.href"
                    class="group relative flex flex-col gap-4 rounded-2xl border border-sidebar-border/70 bg-background/95 p-5 shadow-sm transition hover:border-primary/30 hover:shadow-md"
                >
                    <div class="flex items-start justify-between">
                        <span
                            class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-primary/10 text-primary transition group-hover:bg-primary group-hover:text-primary-foreground"
                        >
                            <component :is="section.icon" class="h-5 w-5" />
                        </span>
                        <ArrowRight
                            class="h-4 w-4 text-muted-foreground/40 transition group-hover:translate-x-1 group-hover:text-primary"
                        />
                    </div>

                    <div>
                        <p class="font-semibold text-foreground">
                            {{ section.title }}
                        </p>
                        <p
                            class="mt-1 text-xs leading-relaxed text-muted-foreground"
                        >
                            {{ section.description }}
                        </p>
                    </div>
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
