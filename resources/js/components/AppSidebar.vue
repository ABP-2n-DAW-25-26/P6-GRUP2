<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    Globe,
    GraduationCap,
    HeartPulse,
    LayoutGrid,
    UserCog,
    Mail,
    ClipboardPen,
    UsersRound,
    Shield,
    Pill,
} from 'lucide-vue-next';
import AppLogo from '@/components/AppLogo.vue';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { admindashboard as dashboard } from '@/routes';
import { index as usersIndex } from '@/routes/users';
import { index as mailIndex } from '@/routes/mail';
import { index as servicesIndex } from '@/routes/services';
import { index as assignmentsIndex } from '@/routes/adminAssignments';
import { index as pharmacyguardsIndex } from '@/routes/pharmacyguards';
import { index as pharmaciesIndex } from '@/routes/pharmacies';
import { index as workshopsIndex } from '@/routes/workshops';

import type { NavItem } from '@/types';

const mainNavItems: NavItem[] = [
    {
        title: 'Inici',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Usuaris',
        href: usersIndex(),
        icon: UsersRound,
    },
    {
        title: 'Mail',
        href: mailIndex(),
        icon: Mail,
    },
    {
        title: 'Serveis',
        href: servicesIndex(),
        icon: Pill,
    },

    {
        title: 'Encarrecs',
        href: assignmentsIndex(),
        icon: ClipboardPen,
    },
    {
        title: 'Guardies',
        href: pharmacyguardsIndex(),
        icon: Shield,
    },
    {
        title: 'Farmacies',
        href: pharmaciesIndex(),
        icon: UserCog,
    },
    {
        title: 'Tallers',
        href: workshopsIndex(),
        icon: GraduationCap,
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Web Farmàcia',
        href: '/',
        icon: Globe,
    },
    {
        title: 'Contacte públic',
        href: '/contactans',
        icon: HeartPulse,
    },
];
</script>

<template>
    <Sidebar
        collapsible="icon"
        variant="inset"
        class="border-sidebar-border/70 bg-sidebar/95 shadow-sm"
    >
        <SidebarHeader
            class="border-b border-sidebar-border/65 bg-sidebar/95 px-2 py-3"
        >
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter class="border-t border-sidebar-border/65 bg-sidebar/90">
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
