<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { QuillEditor } from '@vueup/vue-quill';
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import {
    create as workshopsCreate,
    index as workshopsIndex,
    store as workshopsStore,
} from '@/routes/workshops';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Tallers', href: workshopsIndex().url },
    { title: 'Crear taller', href: workshopsCreate().url },
];

const selectedPhoto = ref<File | null>(null);
const description = ref('');
const photoPreview = computed(() => {
    if (!selectedPhoto.value) {
        return null;
    }

    return URL.createObjectURL(selectedPhoto.value);
});

function toWebp(file: File): Promise<Blob> {
    return new Promise((resolve) => {
        const img = new Image();
        img.src = URL.createObjectURL(file);
        img.onload = () => {
            const width = Math.round(img.width * 0.8);
            const height = Math.round(img.height * 0.8);
            const canvas = document.createElement('canvas');
            canvas.width = width;
            canvas.height = height;
            canvas.getContext('2d')!.drawImage(img, 0, 0, width, height);
            canvas.toBlob((blob) => resolve(blob!), 'image/webp', 0.8);
        };
    });
}

const handlePhotoChange = async (event: Event): Promise<void> => {
    const input = event.target as HTMLInputElement;
    const file = input.files?.[0];
    if (file) {
        const converted = await toWebp(file);
        selectedPhoto.value = new File([converted], 'photo.webp', {
            type: 'image/webp',
        });

        const dt = new DataTransfer();
        dt.items.add(selectedPhoto.value);
        input.files = dt.files;
    }
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">

        <Head title="Crear taller" />
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
                            Crear nou taller
                        </h1>
                        <p class="mt-2 max-w-xl text-sm text-muted-foreground">
                            Defineix la informació bàsica, la data i la capacitat del
                            nou taller.
                        </p>
                    </div>


                </div>
            </div>

            <div class="relative rounded-2xl border border-sidebar-border/70 bg-background/95 p-6 shadow-sm">
                <Form :action="workshopsStore().url" method="post" reset-on-success
                    #default="{ errors, processing, progress }" class="grid gap-5">
                    <div class="grid gap-2">
                        <Label for="name">Nom</Label>
                        <Input id="name" type="text" name="name" required autofocus placeholder="Títol del taller" />
                        <InputError :message="errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="description">Descripció</Label>

                        <QuillEditor v-model:content="description" contentType="html" theme="snow" toolbar="full"
                            class="rounded-xl bg-background" />

                        <input type="hidden" name="description" :value="description" />

                        <InputError :message="errors.description" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="photo">Foto</Label>
                        <Input id="photo" type="file" name="photo" accept="image/*" @change="handlePhotoChange" />
                        <div v-if="photoPreview" class="mt-2 overflow-hidden rounded-xl bg-muted/20">
                            <img :src="photoPreview" alt="Previsualització de la foto del taller"
                                class="h-90 w-60 object-cover" />
                        </div>
                        <p v-if="progress" class="text-xs text-muted-foreground">
                            Pujant: {{ progress.percentage }}%
                        </p>
                        <InputError :message="errors.photo" />
                    </div>

                    <div class="grid gap-5 md:grid-cols-2">
                        <div class="grid gap-2">
                            <Label for="workshop_date">Data</Label>
                            <Input id="workshop_date" type="date" name="workshop_date" required />
                            <InputError :message="errors.workshop_date" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="max_attendees">Plaçes disponibles</Label>
                            <Input id="max_attendees" type="number" name="max_attendees" min="1"
                                placeholder="Ex.: 20" />
                            <InputError :message="errors.max_attendees" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="start_time">Hora d'inici</Label>
                            <Input id="start_time" type="time" name="start_time" required />
                            <InputError :message="errors.start_time" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="end_time">Hora de finalització</Label>
                            <Input id="end_time" type="time" name="end_time" required />
                            <InputError :message="errors.end_time" />
                        </div>
                    </div>

                    <div class="grid gap-2">
                        <Label for="is_active">Estat</Label>
                        <select id="is_active" name="is_active"
                            class="w-full rounded-xl border border-sidebar-border/80 bg-background px-3 py-2 text-sm shadow-xs transition focus-visible:border-ring focus-visible:ring-2 focus-visible:ring-ring/30 focus-visible:outline-none">
                            <option value="1" selected>Actiu</option>
                            <option value="0">Inactiu</option>
                        </select>
                        <InputError :message="errors.is_active" />
                    </div>

                    <div class="mt-2 flex items-center justify-end gap-3">
                        <Button as-child type="button" variant="outline">
                            <Link :href="workshopsIndex().url">Cancel·lar</Link>
                        </Button>
                        <Button type="submit" :disabled="processing"
                            class="bg-primary text-primary-foreground hover:bg-primary/90">
                            {{ processing ? 'Creant...' : 'Crear taller' }}
                        </Button>
                    </div>
                </Form>
            </div>
        </div>
    </AppLayout>
</template>
