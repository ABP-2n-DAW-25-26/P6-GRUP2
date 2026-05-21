<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { QuillEditor } from '@vueup/vue-quill';
import { computed, ref } from 'vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import {
    edit as workshopsEdit,
    index as workshopsIndex,
    update as workshopsUpdate,
} from '@/routes/workshops';

type Workshop = {
    id: number;
    name: string;
    description: string;
    photo_path: string | null;
    workshop_date: string;
    start_time: string;
    end_time: string;
    max_attendees: number | null;
    is_active: boolean;
};

const props = defineProps<{
    workshop: Workshop;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Tallers', href: workshopsIndex().url },
    { title: props.workshop.name, href: workshopsEdit(props.workshop.id).url },
];

const isActive = ref(props.workshop.is_active ? '1' : '0');
const selectedPhoto = ref<File | null>(null);
const description = ref(props.workshop.description);
const photoPreview = computed(() => {
    if (selectedPhoto.value) {
        return URL.createObjectURL(selectedPhoto.value);
    }

    if (props.workshop.photo_path) {
        return `/storage/${props.workshop.photo_path}`;
    }

    return null;
});

const formattedDate = computed(() => props.workshop.workshop_date.slice(0, 10));
const formattedStartTime = computed(() =>
    props.workshop.start_time.slice(0, 5),
);
const formattedEndTime = computed(() => props.workshop.end_time.slice(0, 5));

const handlePhotoChange = (event: Event): void => {
    const input = event.target as HTMLInputElement;
    selectedPhoto.value = input.files?.[0] ?? null;
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head :title="`Editar: ${workshop.name}`" />
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
                            Editar taller
                        </h1>
                        <p class="mt-2 text-sm text-muted-foreground">
                            Modifica la informació del taller
                            <strong>{{ workshop.name }}</strong
                            >.
                        </p>
                    </div>
                </div>
            </div>

            <div
                class="relative rounded-2xl border border-sidebar-border/70 bg-background/95 p-6 shadow-sm"
            >
                <Form
                    :action="workshopsUpdate(workshop.id).url"
                    method="put"
                    #default="{ errors, processing, progress }"
                    class="grid gap-5"
                >
                    <div class="grid gap-2">
                        <Label for="name">Nom</Label>
                        <Input
                            id="name"
                            type="text"
                            name="name"
                            :default-value="workshop.name"
                            required
                            autofocus
                            placeholder="Títol del taller"
                        />
                        <InputError :message="errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="description">Descripció</Label>

                        <QuillEditor
                            v-model:content="description"
                            contentType="html"
                            theme="snow"
                            toolbar="full"
                            class="rounded-xl bg-background"
                        />

                        <input
                            type="hidden"
                            name="description"
                            :value="description"
                        />

                        <InputError :message="errors.description" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="photo">Foto</Label>
                        <div
                            v-if="photoPreview"
                            class="overflow-hidden rounded-xl bg-muted/20"
                        >
                            <img
                                :src="photoPreview"
                                alt="Foto actual del taller"
                                class="h-40 w-60 object-cover"
                            />
                        </div>
                        <Input
                            id="photo"
                            type="file"
                            name="photo"
                            accept="image/*"
                            @change="handlePhotoChange"
                        />
                        <p class="text-xs text-muted-foreground">
                            Deixa-ho buit per conservar la foto actual.
                        </p>
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
                                :default-value="formattedDate"
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
                                :default-value="workshop.max_attendees ?? ''"
                                min="1"
                                placeholder="Ex.: 20"
                            />
                            <InputError :message="errors.max_attendees" />
                        </div>
                    </div>

                    <div class="grid gap-5 md:grid-cols-2">
                        <div class="grid gap-2">
                            <Label for="start_time">Hora d'inici</Label>
                            <Input
                                id="start_time"
                                type="time"
                                name="start_time"
                                :default-value="formattedStartTime"
                                required
                            />
                            <InputError :message="errors.start_time" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="end_time">Hora de fi</Label>
                            <Input
                                id="end_time"
                                type="time"
                                name="end_time"
                                :default-value="formattedEndTime"
                                required
                            />
                            <InputError :message="errors.end_time" />
                        </div>
                    </div>

                    <div class="grid gap-2">
                        <Label for="is_active">Estat</Label>
                        <select
                            id="is_active"
                            name="is_active"
                            v-model="isActive"
                            class="w-full rounded-xl border border-sidebar-border/80 bg-background px-3 py-2 text-sm shadow-xs transition focus-visible:border-ring focus-visible:ring-2 focus-visible:ring-ring/30 focus-visible:outline-none"
                        >
                            <option value="1">Actiu</option>
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
                            {{ processing ? 'Guardant...' : 'Guardar canvis' }}
                        </Button>
                    </div>
                </Form>
            </div>
        </div>
    </AppLayout>
</template>
