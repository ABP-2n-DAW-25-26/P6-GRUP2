<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import {
    Pill,
    HeartPulse,
    FlaskConical,
    ShieldCheck,
    ScanFace,
    Droplet,
    Activity,
    Stethoscope,
    Syringe,
    Microscope,
    Apple,
    Brain,
    Bandage,
    Thermometer,
} from 'lucide-vue-next';
import { computed } from 'vue';
import TiptapEditor from '@/components/TiptapEditor.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { admindashboard as dashboard } from '@/routes';
import { index as servicesIndex, update as servicesUpdate } from '@/routes/services';

const iconOptions = [
    { value: 'pill', label: 'Medicació', icon: Pill },
    { value: 'heart', label: 'Cor / Salut', icon: HeartPulse },
    { value: 'flask', label: 'Laboratori', icon: FlaskConical },
    { value: 'shield', label: 'Protecció', icon: ShieldCheck },
    { value: 'scan', label: 'Anàlisi / Escàner', icon: ScanFace },
    { value: 'droplet', label: 'Analítica (sang/aigua)', icon: Droplet },
    { value: 'activity', label: 'Control de salut', icon: Activity },
    { value: 'stethoscope', label: 'Consulta mèdica', icon: Stethoscope },
    { value: 'syringe', label: 'Vacunació', icon: Syringe },
    { value: 'microscope', label: 'Investigació', icon: Microscope },
    { value: 'apple', label: 'Nutrició', icon: Apple },
    { value: 'brain', label: 'Benestar mental', icon: Brain },
    { value: 'bandage', label: 'Cures', icon: Bandage },
    { value: 'thermometer', label: 'Control temperatura', icon: Thermometer },
];

interface Service {
    id: number;
    name: string;
    description: string;
    duration_minutes: number;
    icon: string | null;
}

const props = defineProps<{ service: Service }>();

const service = props.service;

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Administració de serveis', href: dashboard().url },
    { title: 'Editar servei', href: '#' },
];

const form = useForm({
    name: service.name,
    description: service.description,
    duration_minutes: service.duration_minutes,
    icon: service.icon ?? '',
});

const submit = () => {
    form.put(servicesUpdate(service.id).url, {
        preserveScroll: true,
    });
};

const selectedIconComponent = computed(
    () => iconOptions.find((o) => o.value === form.icon)?.icon ?? null,
);
</script>
<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Editar servei" />

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
                            Editar servei
                        </h1>
                        <p class="mt-2 max-w-xl text-sm text-muted-foreground">
                            Modifica els detalls del servei i desa els canvis
                        </p>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="grid gap-6 lg:grid-cols-12">
                <!-- Form -->
                <div class="lg:col-span-8">
                    <div
                        class="rounded-2xl border border-sidebar-border/70 bg-background/95 p-6 shadow-sm"
                    >
                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Name -->
                            <div class="grid gap-2">
                                <label
                                    class="text-sm font-medium text-foreground"
                                >
                                    Nom del servei
                                </label>

                                <input
                                    type="text"
                                    v-model="form.name"
                                    aria-label="Nom del servei"
                                    class="w-full rounded-xl border border-sidebar-border/80 bg-background px-4 py-2 text-sm shadow-xs transition focus-visible:border-ring focus-visible:ring-2 focus-visible:ring-ring/30 focus-visible:outline-none"
                                />

                                <p
                                    v-if="form.errors.name"
                                    class="text-xs text-red-500"
                                >
                                    {{ form.errors.name }}
                                </p>
                            </div>

                            <!-- Description -->
                            <div class="grid gap-2">
                                <label
                                    class="text-sm font-medium text-foreground"
                                >
                                    Descripció
                                </label>

                                <div
                                    class="rounded-2xl border border-sidebar-border/70 bg-background"
                                >
                                    <TiptapEditor v-model="form.description" />
                                </div>

                                <p
                                    v-if="form.errors.description"
                                    class="text-xs text-red-500"
                                >
                                    {{ form.errors.description }}
                                </p>
                            </div>

                            <!-- Duration -->
                            <div class="grid gap-2">
                                <label
                                    class="text-sm font-medium text-foreground"
                                >
                                    Durada (minuts)
                                </label>

                                <input
                                    type="number"
                                    v-model="form.duration_minutes"
                                    min="1"
                                    aria-label="Durada"
                                    class="w-full rounded-xl border border-sidebar-border/80 bg-background px-4 py-2 text-sm shadow-xs transition focus-visible:border-ring focus-visible:ring-2 focus-visible:ring-ring/30 focus-visible:outline-none"
                                />

                                <p
                                    v-if="form.errors.duration_minutes"
                                    class="text-xs text-red-500"
                                >
                                    {{ form.errors.duration_minutes }}
                                </p>
                            </div>

                            <!-- Icons -->
                            <div class="grid gap-3">
                                <label
                                    class="text-sm font-medium text-foreground"
                                >
                                    Icona
                                </label>

                                <div
                                    class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3"
                                >
                                    <button
                                        v-for="opt in iconOptions"
                                        :key="opt.value"
                                        type="button"
                                        @click="form.icon = opt.value"
                                        :aria-label="`Seleccionar icona: ${opt.label}`"
                                        :class="[
                                            'flex items-center gap-3 rounded-2xl border px-4 py-3 text-left text-sm transition',
                                            form.icon === opt.value
                                                ? 'border-primary bg-primary/10 text-primary'
                                                : 'border-sidebar-border/70 hover:bg-muted/60',
                                        ]"
                                    >
                                        <div
                                            class="flex h-10 w-10 items-center justify-center rounded-full bg-background shadow-sm"
                                        >
                                            <component
                                                :is="opt.icon"
                                                class="h-5 w-5"
                                            />
                                        </div>

                                        <span class="font-medium">
                                            {{ opt.label }}
                                        </span>
                                    </button>
                                </div>

                                <p
                                    v-if="form.errors.icon"
                                    class="text-xs text-red-500"
                                >
                                    {{ form.errors.icon }}
                                </p>
                            </div>

                            <!-- Actions -->
                            <div
                                class="flex justify-end gap-3 border-t border-sidebar-border/70 pt-5"
                            >
                                <Link
                                    :href="servicesIndex().url"
                                    class="inline-flex items-center rounded-xl border border-sidebar-border px-4 py-2 text-sm transition hover:bg-muted"
                                >
                                    Cancel·lar
                                </Link>

                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="rounded-xl bg-primary px-5 py-2 text-sm font-medium text-primary-foreground shadow-sm transition hover:bg-primary/90 disabled:opacity-50"
                                >
                                    {{
                                        form.processing
                                            ? 'Desant...'
                                            : 'Desar canvis'
                                    }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Preview -->
                <div class="lg:col-span-4">
                    <div
                        class="sticky top-6 rounded-2xl border border-sidebar-border/70 bg-background/95 p-6 shadow-sm"
                    >
                        <div class="mb-5 flex items-center justify-between">
                            <h2 class="text-lg font-semibold text-foreground">
                                Previsualització
                            </h2>
                        </div>

                        <!-- Preview Card -->
                        <div
                            class="space-y-5 rounded-2xl border border-sidebar-border/70 bg-muted/20 p-5"
                        >
                            <!-- Header -->
                            <div class="flex items-center gap-4">
                                <div
                                    class="flex h-14 w-14 items-center justify-center rounded-2xl bg-primary/10 text-primary"
                                >
                                    <component
                                        v-if="selectedIconComponent"
                                        :is="selectedIconComponent"
                                        class="h-7 w-7"
                                    />

                                    <span
                                        v-else
                                        class="text-xs text-muted-foreground"
                                    >
                                        —
                                    </span>
                                </div>

                                <div>
                                    <h3 class="font-semibold text-foreground">
                                        {{ form.name || 'Sense nom' }}
                                    </h3>

                                    <p class="text-sm text-muted-foreground">
                                        {{ form.duration_minutes }}
                                        minuts
                                    </p>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="space-y-2">
                                <p
                                    class="text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                                >
                                    Descripció
                                </p>

                                <div
                                    v-if="form.description"
                                    v-html="form.description"
                                    class="prose prose-sm dark:prose-invert max-w-none text-sm"
                                ></div>

                                <p v-else class="text-sm text-muted-foreground">
                                    Sense descripció
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
