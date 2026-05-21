<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';

interface Service {
    id: number;
    name: string;
}

const props = defineProps<{ service: Service }>();

const daysOfWeek = [
    { value: 1, label: 'Dilluns' },
    { value: 2, label: 'Dimarts' },
    { value: 3, label: 'Dimecres' },
    { value: 4, label: 'Dijous' },
    { value: 5, label: 'Divendres' },
    { value: 6, label: 'Dissabte' },
    { value: 7, label: 'Diumenge' },
];

const form = useForm({
    service_id: props.service.id,
    day_of_week: '',
    start_time: '',
    end_time: '',
});

const selectedDayLabel = computed(
    () => daysOfWeek.find((d) => d.value == form.day_of_week)?.label,
);

const isValid = computed(
    () => form.day_of_week && form.start_time && form.end_time,
);

const submit = () => {
    if (!isValid.value) {
        return;
    }

    form.post('/admin/service-schedules');
};
</script>
<template>
    <AppLayout>

        <Head title="Nou horari" />

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
                            Nou horari
                        </h1>
                        <p class="mt-2 text-sm text-muted-foreground">
                            Configura un nou horari per al servei
                            <span class="font-medium text-foreground">
                                {{ service.name }}
                            </span>
                        </p>
                    </div>


                </div>
            </div>


            <!-- Content -->
            <div class="grid gap-6 lg:grid-cols-12">
                <!-- Form -->
                <div class="lg:col-span-8">
                    <div class="rounded-2xl border border-sidebar-border/70 bg-background/95 p-6 shadow-sm">
                        <div class="space-y-6">
                            <!-- Days -->
                            <div class="grid gap-3">
                                <label class="text-sm font-medium text-foreground">
                                    Dia de la setmana
                                </label>

                                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                                    <button v-for="day in daysOfWeek" :key="day.value" type="button" @click="
                                        form.day_of_week =
                                        day.value
                                        " :class="[
                                            'rounded-2xl border px-4 py-3 text-sm font-medium transition',
                                            form.day_of_week ==
                                                day.value
                                                ? 'border-primary bg-primary/10 text-primary'
                                                : 'border-sidebar-border/70 hover:bg-muted/60',
                                        ]">
                                        {{ day.label }}
                                    </button>
                                </div>
                            </div>

                            <!-- Time -->
                            <div class="grid gap-5 md:grid-cols-2">
                                <!-- Start -->
                                <div class="grid gap-2">
                                    <label class="text-sm font-medium text-foreground">
                                        Hora inici
                                    </label>

                                    <input type="time" v-model="form.start_time
                                        " aria-label="Hora inici"
                                        class="w-full rounded-xl border border-sidebar-border/80 bg-background px-4 py-2 text-sm shadow-xs transition focus-visible:border-ring focus-visible:ring-2 focus-visible:ring-ring/30 focus-visible:outline-none" />
                                </div>

                                <!-- End -->
                                <div class="grid gap-2">
                                    <label class="text-sm font-medium text-foreground">
                                        Hora fi
                                    </label>

                                    <input type="time" v-model="form.end_time
                                        " aria-label="Hora fi"
                                        class="w-full rounded-xl border border-sidebar-border/80 bg-background px-4 py-2 text-sm shadow-xs transition focus-visible:border-ring focus-visible:ring-2 focus-visible:ring-ring/30 focus-visible:outline-none" />
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex justify-end gap-3 border-t border-sidebar-border/70 pt-5">
                                <Link :href="`/admin/services/${service.id}`"
                                    class="inline-flex items-center rounded-xl border border-sidebar-border px-4 py-2 text-sm transition hover:bg-muted">
                                    Cancel·lar
                                </Link>

                                <button @click="submit" :disabled="!isValid ||
                                    form.processing
                                    "
                                    class="rounded-xl bg-primary px-5 py-2 text-sm font-medium text-primary-foreground shadow-sm transition hover:bg-primary/90 disabled:opacity-50"
                                    aria-label="Crear horari">
                                    {{
                                        form.processing
                                            ? 'Desant...'
                                            : 'Crear horari'
                                    }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Summary -->
                <div class="lg:col-span-4">
                    <div
                        class="sticky top-6 rounded-2xl border border-sidebar-border/70 bg-background/95 p-6 shadow-sm">
                        <div class="mb-5 flex items-center justify-between">
                            <h2 class="text-lg font-semibold text-foreground">
                                Resum
                            </h2>

                            <span class="rounded-full bg-primary/10 px-3 py-1 text-xs font-medium text-primary">
                                Horari
                            </span>
                        </div>

                        <div class="space-y-5 rounded-2xl border border-sidebar-border/70 bg-muted/20 p-5">
                            <!-- Service -->
                            <div>
                                <p class="text-xs font-semibold tracking-wide text-muted-foreground uppercase">
                                    Servei
                                </p>

                                <p class="mt-1 font-medium text-foreground">
                                    {{ service.name }}
                                </p>
                            </div>

                            <!-- Day -->
                            <div>
                                <p class="text-xs font-semibold tracking-wide text-muted-foreground uppercase">
                                    Dia
                                </p>

                                <p class="mt-1 text-sm text-foreground">
                                    {{
                                        selectedDayLabel ||
                                        'No seleccionat'
                                    }}
                                </p>
                            </div>

                            <!-- Time -->
                            <div>
                                <p class="text-xs font-semibold tracking-wide text-muted-foreground uppercase">
                                    Horari
                                </p>

                                <p class="mt-1 text-sm text-foreground">
                                    {{
                                        form.start_time ||
                                        '--:--'
                                    }}
                                    →
                                    {{
                                        form.end_time ||
                                        '--:--'
                                    }}
                                </p>
                            </div>

                            <!-- Status -->
                            <div v-if="isValid"
                                class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                                Horari llest per guardar
                            </div>

                            <div v-else
                                class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">
                                Completa tots els camps
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>