<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import {
    create as workshopsCreate,
    index as workshopsIndex,
    store as workshopsStore,
} from '@/routes/workshops';
import { type BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Tallers', href: workshopsIndex().url },
    { title: 'Crear taller', href: workshopsCreate().url },
];

const selectedPhoto = ref<File | null>(null);

const photoPreview = computed(() => {
    if (!selectedPhoto.value) {
        return null;
    }

    return URL.createObjectURL(selectedPhoto.value);
});

const handlePhotoChange = (event: Event): void => {
    const input = event.target as HTMLInputElement;
    selectedPhoto.value = input.files?.[0] ?? null;
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Crear taller" />

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
                    Crear taller
                </h1>
                <p class="mt-2 text-sm text-muted-foreground">
                    Defineix la informació bàsica, la data i la capacitat del
                    nou taller.
                </p>
            </div>

            <div
                class="relative rounded-2xl border border-sidebar-border/70 bg-background/95 p-6 shadow-sm"
            >
                <Form
                    :action="workshopsStore().url"
                    method="post"
                    reset-on-success
                    #default="{ errors, processing, progress }"
                    class="grid gap-5"
                >
                    <div class="grid gap-2">
                        <Label for="name">Nom</Label>
                        <Input
                            id="name"
                            type="text"
                            name="name"
                            required
                            autofocus
                            placeholder="Títol del taller"
                        />
                        <InputError :message="errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="description">Descripció</Label>
                        <textarea
                            id="description"
                            name="description"
                            rows="4"
                            required
                            placeholder="Explica de què tracta el taller"
                            class="w-full rounded-xl border border-sidebar-border/80 bg-background px-3 py-2 text-sm shadow-xs transition focus-visible:border-ring focus-visible:ring-2 focus-visible:ring-ring/30 focus-visible:outline-none"
                        ></textarea>
                        <InputError :message="errors.description" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="photo">Foto</Label>
                        <Input
                            id="photo"
                            type="file"
                            name="photo"
                            accept="image/*"
                            @change="handlePhotoChange"
                        />
                        <div
                            v-if="photoPreview"
                            class="mt-2 overflow-hidden rounded-xl bg-muted/20"
                        >
                            <img
                                :src="photoPreview"
                                alt="Previsualització de la foto del taller"
                                class="h-90 w-60 object-cover"
                            />
                        </div>
                        <p
                            v-if="progress"
                            class="text-xs text-muted-foreground"
                        >
                            Pujant: {{ progress.percentage }}%
                        </p>
                        <InputError :message="errors.photo" />
                    </div>

                    <div class="grid gap-5 md:grid-cols-2">
                        <div class="grid gap-2">
                            <Label for="workshop_date">Data</Label>
                            <Input
                                id="workshop_date"
                                type="date"
                                name="workshop_date"
                                required
                            />
                            <InputError :message="errors.workshop_date" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="max_attendees">Places màximes</Label>
                            <Input
                                id="max_attendees"
                                type="number"
                                name="max_attendees"
                                min="1"
                                placeholder="Ex.: 20"
                            />
                            <InputError :message="errors.max_attendees" />
                        </div>
                    </div>

                    <div class="grid gap-2">
                        <Label for="is_active">Estat</Label>
                        <select
                            id="is_active"
                            name="is_active"
                            class="w-full rounded-xl border border-sidebar-border/80 bg-background px-3 py-2 text-sm shadow-xs transition focus-visible:border-ring focus-visible:ring-2 focus-visible:ring-ring/30 focus-visible:outline-none"
                        >
                            <option value="1" selected>Actiu</option>
                            <option value="0">Inactiu</option>
                        </select>
                        <InputError :message="errors.is_active" />
                    </div>

                    <div class="mt-2 flex items-center justify-end gap-3">
                        <Button as-child type="button" variant="outline">
                            <Link :href="workshopsIndex().url">Cancel·lar</Link>
                        </Button>
                        <Button
                            type="submit"
                            :disabled="processing"
                            class="bg-primary text-primary-foreground hover:bg-primary/90"
                        >
                            {{ processing ? 'Creant...' : 'Crear taller' }}
                        </Button>
                    </div>
                </Form>
            </div>
        </div>
    </AppLayout>
</template>
