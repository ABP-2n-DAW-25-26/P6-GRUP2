<script setup lang="ts">
import { Form, Head, router, usePage } from '@inertiajs/vue3';
import { CalendarX, Plus, Trash2 } from 'lucide-vue-next';
import ConfirmDeleteDialog from '@/components/ConfirmDeleteDialog.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import {
    destroy as exceptionsDestroy,
    index as exceptionsIndex,
    store as exceptionsStore,
} from '@/routes/calendar-exceptions';
import { ref } from 'vue';

interface CalendarException {
    id: number;
    day: string;
}

const props = defineProps<{
    exceptions: CalendarException[];
}>();

const page = usePage<{ flash?: { success?: string } }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dies festius', href: exceptionsIndex().url },
];

const exceptionToDelete = ref<number | null>(null);

const removeException = (id: number) => {
    exceptionToDelete.value = id;
};

const confirmDelete = () => {
    if (exceptionToDelete.value === null) {
        return;
    }

    router.delete(exceptionsDestroy(exceptionToDelete.value).url, {
        preserveScroll: true,
    });
    exceptionToDelete.value = null;
};

const cancelDelete = () => {
    exceptionToDelete.value = null;
};

const formatDay = (day: string) => {
    return new Date(day).toLocaleDateString('ca-ES', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        timeZone: 'UTC',
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Dies festius" />

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

                <div class="relative flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p
                            class="inline-flex items-center gap-2 rounded-full border border-sidebar-border/70 bg-background/80 px-3 py-1 text-[10px] font-semibold tracking-[0.18em] text-muted-foreground uppercase shadow-xs backdrop-blur"
                        >
                            <span class="inline-block h-1.5 w-1.5 rounded-full bg-primary"></span>
                            Farmacia Soler
                        </p>
                        <h1 class="mt-3 text-3xl font-semibold tracking-tight text-foreground sm:text-4xl">
                            Dies festius
                        </h1>
                        <p class="mt-2 max-w-xl text-sm text-muted-foreground">
                            Gestiona els dies en què la farmàcia no ofereix servei de cita prèvia.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Flash -->
            <div
                v-if="page.props.flash?.success"
                class="rounded-xl border border-green-200 bg-green-50/90 px-4 py-3 text-sm text-green-700 shadow-sm"
                role="alert"
            >
                {{ page.props.flash.success }}
            </div>

            <!-- Form Card -->
            <div class="relative rounded-2xl border border-sidebar-border/70 bg-background/95 p-6 shadow-sm">
                <h2 class="mb-4 text-base font-semibold text-foreground">Afegir dia festiu</h2>

                <Form
                    v-bind="exceptionsStore.form()"
                    :reset-on-success="['day']"
                    v-slot="{ errors, processing }"
                    class="flex flex-col gap-4 sm:flex-row sm:items-end"
                >
                    <div class="grid flex-1 gap-2">
                        <Label for="day">Data</Label>
                        <Input
                            id="day"
                            type="date"
                            name="day"
                            required
                            autofocus
                        />
                        <InputError :message="errors.day" />
                    </div>

                    <Button
                        type="submit"
                        :disabled="processing"
                        class="group bg-primary text-primary-foreground hover:bg-primary/90"
                    >
                        <Plus class="mr-2 h-4 w-4 transition-transform group-hover:rotate-90" />
                        {{ processing ? 'Afegint...' : 'Afegir dia' }}
                    </Button>
                </Form>
            </div>

            <!-- Table Card -->
            <div class="relative rounded-2xl border border-sidebar-border/70 bg-background/95 p-6 shadow-sm">
                <div class="mb-5">
                    <h2 class="text-lg font-semibold text-foreground">Dies festius registrats</h2>
                    <p class="text-sm text-muted-foreground">
                        {{ props.exceptions.length }} {{ props.exceptions.length === 1 ? 'dia' : 'dies' }} registrats
                    </p>
                </div>

                <div class="overflow-hidden rounded-2xl border border-sidebar-border/70">
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-muted/80">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase">
                                        Data
                                    </th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold uppercase">
                                        Accions
                                    </th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr
                                    v-for="(exception, index) in props.exceptions"
                                    :key="exception.id"
                                    :class="[
                                        index % 2 === 0 ? 'bg-background' : 'bg-muted/30',
                                        'border-t border-sidebar-border/60 transition hover:bg-muted/60',
                                    ]"
                                >
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-primary/10 text-primary">
                                                <CalendarX class="h-4 w-4" />
                                            </div>
                                            <span class="font-medium capitalize text-foreground">
                                                {{ formatDay(exception.day) }}
                                            </span>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="flex justify-end">
                                            <button
                                                type="button"
                                                class="inline-flex items-center rounded-lg p-2 text-muted-foreground transition hover:bg-red-50 hover:text-red-600"
                                                aria-label="Eliminar dia festiu"
                                                @click="removeException(exception.id)"
                                            >
                                                <Trash2 class="h-4 w-4" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Empty -->
                                <tr v-if="props.exceptions.length === 0">
                                    <td
                                        colspan="2"
                                        class="px-6 py-12 text-center text-sm text-muted-foreground"
                                    >
                                        Encara no hi ha dies festius registrats.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <ConfirmDeleteDialog
            :open="exceptionToDelete !== null"
            title="Eliminar dia festiu"
            description="Segur que vols eliminar aquest dia festiu? Els usuaris podran tornar a demanar cita en aquesta data."
            @confirm="confirmDelete"
            @cancel="cancelDelete"
        />
    </AppLayout>
</template>
