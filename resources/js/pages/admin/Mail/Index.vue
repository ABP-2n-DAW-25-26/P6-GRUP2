<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Administració del Correu', href: 'admin/mail' },
];

import { store } from '@/routes/mail';

defineProps<{
    mail: {
        mail_mailer: string;
        mail_scheme: string;
        mail_host: string;
        mail_port: string;
        mail_username: string;
        mail_from_address: string;
    };
}>();
</script>
<template>
    <AppLayout :breadcrumbs="breadcrumbs">

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
                            Configuració SMTP

                        </h1>
                        <p class="mt-2 max-w-xl text-sm text-muted-foreground">
                            Configura el servidor SMTP utilitzat per enviar correus del
                            sistema. </p>
                    </div>


                </div>
            </div>

            <!-- Main Card -->
            <div class="relative rounded-2xl border border-sidebar-border/70 bg-background/95 p-6 shadow-sm">
                <Form v-bind="store.form()" class="grid gap-5 md:grid-cols-2">
                    <!-- Mailer -->
                    <div class="grid gap-2">
                        <label class="text-sm font-medium text-foreground">
                            Mailer
                        </label>

                        <input name="mail_mailer" :value="mail.mail_mailer" placeholder="smtp"
                            class="w-full rounded-xl border border-sidebar-border/80 bg-background px-4 py-2 text-sm shadow-xs transition focus-visible:border-ring focus-visible:ring-2 focus-visible:ring-ring/30 focus-visible:outline-none" />
                    </div>

                    <!-- Scheme -->
                    <div class="grid gap-2">
                        <label class="text-sm font-medium text-foreground">
                            Scheme
                        </label>

                        <input name="mail_scheme" :value="mail.mail_scheme" placeholder="tls"
                            class="w-full rounded-xl border border-sidebar-border/80 bg-background px-4 py-2 text-sm shadow-xs transition focus-visible:border-ring focus-visible:ring-2 focus-visible:ring-ring/30 focus-visible:outline-none" />
                    </div>

                    <!-- Host -->
                    <div class="grid gap-2 md:col-span-2">
                        <label class="text-sm font-medium text-foreground">
                            Host
                        </label>

                        <input name="mail_host" :value="mail.mail_host" placeholder="smtp.example.com"
                            class="w-full rounded-xl border border-sidebar-border/80 bg-background px-4 py-2 text-sm shadow-xs transition focus-visible:border-ring focus-visible:ring-2 focus-visible:ring-ring/30 focus-visible:outline-none" />
                    </div>

                    <!-- Port -->
                    <div class="grid gap-2">
                        <label class="text-sm font-medium text-foreground">
                            Port
                        </label>

                        <input name="mail_port" :value="mail.mail_port" placeholder="587"
                            class="w-full rounded-xl border border-sidebar-border/80 bg-background px-4 py-2 text-sm shadow-xs transition focus-visible:border-ring focus-visible:ring-2 focus-visible:ring-ring/30 focus-visible:outline-none" />
                    </div>

                    <!-- Username -->
                    <div class="grid gap-2">
                        <label class="text-sm font-medium text-foreground">
                            Username
                        </label>

                        <input name="mail_username" :value="mail.mail_username" placeholder="you@example.com"
                            class="w-full rounded-xl border border-sidebar-border/80 bg-background px-4 py-2 text-sm shadow-xs transition focus-visible:border-ring focus-visible:ring-2 focus-visible:ring-ring/30 focus-visible:outline-none" />
                    </div>

                    <!-- Password -->
                    <div class="grid gap-2 md:col-span-2">
                        <label class="text-sm font-medium text-foreground">
                            Password

                            <span class="ml-1 text-xs font-normal text-muted-foreground">
                                (deixa buit per mantenir l'actual)
                            </span>
                        </label>

                        <input type="password" name="mail_password" placeholder="••••••••"
                            class="w-full rounded-xl border border-sidebar-border/80 bg-background px-4 py-2 text-sm shadow-xs transition focus-visible:border-ring focus-visible:ring-2 focus-visible:ring-ring/30 focus-visible:outline-none" />
                    </div>

                    <!-- From Address -->
                    <div class="grid gap-2 md:col-span-2">
                        <label class="text-sm font-medium text-foreground">
                            From Address
                        </label>

                        <input name="mail_from_address" :value="mail.mail_from_address"
                            placeholder="no-reply@example.com"
                            class="w-full rounded-xl border border-sidebar-border/80 bg-background px-4 py-2 text-sm shadow-xs transition focus-visible:border-ring focus-visible:ring-2 focus-visible:ring-ring/30 focus-visible:outline-none" />
                    </div>

                    <!-- Submit -->
                    <div class="mt-2 flex justify-end md:col-span-2">
                        <button type="submit"
                            class="rounded-xl bg-primary px-5 py-2 text-sm font-medium text-primary-foreground shadow-sm transition hover:bg-primary/90">
                            Guardar configuració
                        </button>
                    </div>
                </Form>
            </div>
        </div>
    </AppLayout>
</template>