<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { Mail } from 'lucide-vue-next';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { create as emailsCreate, index as emailsIndex, store } from '@/routes/emails';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Correus', href: emailsIndex().url },
    { title: 'Nou correu', href: emailsCreate().url },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Nou correu" />

        <div class="relative flex h-full flex-1 flex-col gap-6 overflow-x-auto p-4 md:p-6">
            <!-- Decorative gradients -->
            <div class="pointer-events-none absolute top-0 right-8 h-56 w-56 rounded-full bg-gradient-to-br from-primary/20 to-muted/70 blur-3xl"></div>
            <div class="pointer-events-none absolute bottom-10 left-0 h-64 w-64 rounded-full bg-gradient-to-tr from-secondary/60 to-primary/10 blur-3xl"></div>

            <!-- Header -->
            <div class="relative overflow-hidden rounded-2xl border border-sidebar-border/70 bg-gradient-to-br from-background via-background to-muted/60 p-7 shadow-sm">
                <div class="pointer-events-none absolute -top-12 -right-12 h-40 w-40 rounded-full bg-primary/10 blur-2xl"></div>
                <div class="relative">
                    <p class="inline-flex items-center gap-2 rounded-full border border-sidebar-border/70 bg-background/80 px-3 py-1 text-[10px] font-semibold tracking-[0.18em] text-muted-foreground uppercase shadow-xs backdrop-blur">
                        <span class="inline-block h-1.5 w-1.5 rounded-full bg-primary"></span>
                        Farmacia Soler
                    </p>
                    <h1 class="mt-3 text-3xl font-semibold tracking-tight text-foreground sm:text-4xl">
                        Nou correu
                    </h1>
                    <p class="mt-2 max-w-xl text-sm text-muted-foreground">
                        Afegeix una adreça de correu per rebre notificacions del sistema.
                    </p>
                </div>
            </div>

            <!-- Form -->
            <div class="relative rounded-2xl border border-sidebar-border/70 bg-background/95 p-6 shadow-sm">
                <Form
                    v-bind="store.form()"
                    :reset-on-success="['email']"
                    v-slot="{ errors, processing }"
                    class="max-w-md space-y-5"
                >
                    <div class="grid gap-2">
                        <Label for="email">Adreça de correu electrònic</Label>
                        <div class="relative">
                            <Mail class="pointer-events-none absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                            <Input
                                id="email"
                                type="email"
                                name="email"
                                placeholder="exemple@domini.cat"
                                required
                                autofocus
                                class="pl-9"
                            />
                        </div>
                        <InputError :message="errors.email" />
                    </div>

                    <div class="flex items-center gap-3 pt-1">
                        <Button
                            type="submit"
                            :disabled="processing"
                            class="bg-primary text-primary-foreground hover:bg-primary/90"
                        >
                            {{ processing ? 'Creant...' : 'Crear correu' }}
                        </Button>
                    </div>
                </Form>
            </div>
        </div>
    </AppLayout>
</template>
