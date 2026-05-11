<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import { computed } from 'vue'
import { admindashboard as dashboard } from '@/routes'
import { type BreadcrumbItem } from '@/types'
import AppLayout from '@/layouts/AppLayout.vue'
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
} from 'lucide-vue-next'

const iconOptions = [
    { value: 'pill',        label: 'Medicació',            icon: Pill },
    { value: 'heart',       label: 'Cor / Salut',          icon: HeartPulse },
    { value: 'flask',       label: 'Laboratori',           icon: FlaskConical },
    { value: 'shield',      label: 'Protecció',            icon: ShieldCheck },
    { value: 'scan',        label: 'Anàlisi / Escàner',    icon: ScanFace },
    { value: 'droplet',     label: 'Analítica (sang/aigua)', icon: Droplet },
    { value: 'activity',    label: 'Control de salut',     icon: Activity },
    { value: 'stethoscope',label: 'Consulta mèdica',       icon: Stethoscope },
    { value: 'syringe',     label: 'Vacunació',            icon: Syringe },
    { value: 'microscope',  label: 'Investigació',         icon: Microscope },
    { value: 'apple',       label: 'Nutrició',             icon: Apple },
    { value: 'brain',       label: 'Benestar mental',      icon: Brain },
    { value: 'bandage',     label: 'Cures',                icon: Bandage },
    { value: 'thermometer', label: 'Control temperatura',  icon: Thermometer },
]

interface Service {
    id: number
    name: string
    description: string
    duration_minutes: number
    icon: string | null
}


const props = defineProps<{ service: Service }>()

const service = props.service

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Administració de serveis', href: dashboard().url },
    { title: 'Editar servei', href: '#' },
]


const form = useForm({
    name: service.name,
    description: service.description,
    duration_minutes: service.duration_minutes,
    icon: service.icon ?? '',
})

const submit = () => {
    form.put(`/admin/services/${service.id}`, {
        preserveScroll: true,
    })
}

const selectedIconComponent = computed(() =>
    iconOptions.find((o) => o.value === form.icon)?.icon ?? null
)
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Editar servei" />

        <div class="p-6 space-y-6">
            <div>
                <h1 class="text-2xl font-bold">Editar Servei</h1>
                <p class="text-gray-500 text-sm">Modifica les dades del servei</p>
            </div>

            <div class="grid gap-8 lg:grid-cols-12">

                <!-- FORM -->
                <div class="lg:col-span-8">
                    <div class="bg-white rounded-xl shadow border p-6">
                        <form @submit.prevent="submit" class="space-y-5">

                            <!-- NAME -->
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">Nom del servei</label>
                                <input
                                    type="text"
                                    v-model="form.name"
                                    class="w-full rounded-lg border px-3 py-2 text-sm"
                                />
                                <p v-if="form.errors.name" class="text-xs text-red-500">
                                    {{ form.errors.name }}
                                </p>
                            </div>

                            <!-- DESCRIPTION -->
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">Descripció</label>
                                <textarea
                                    v-model="form.description"
                                    rows="3"
                                    class="w-full rounded-lg border px-3 py-2 text-sm"
                                ></textarea>
                                <p v-if="form.errors.description" class="text-xs text-red-500">
                                    {{ form.errors.description }}
                                </p>
                            </div>

                            <!-- DURATION -->
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">Durada</label>
                                <input
                                    type="number"
                                    v-model="form.duration_minutes"
                                    min="1"
                                    class="w-full rounded-lg border px-3 py-2 text-sm"
                                />
                                <p v-if="form.errors.duration_minutes" class="text-xs text-red-500">
                                    {{ form.errors.duration_minutes }}
                                </p>
                            </div>

                            <!-- ICON -->
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">Icona</label>
                                <div class="flex flex-wrap gap-3">
                                    <button
                                        v-for="opt in iconOptions"
                                        :key="opt.value"
                                        type="button"
                                        @click="form.icon = opt.value"
                                        :class="[
                                            'flex items-center gap-2 px-3 py-2 rounded-lg border text-sm',
                                            form.icon === opt.value
                                                ? 'border-blue-500 bg-blue-50 text-blue-700'
                                                : 'border-gray-200'
                                        ]"
                                    >
                                        <component :is="opt.icon" class="w-4 h-4" />
                                        {{ opt.label }}
                                    </button>
                                </div>
                                <p v-if="form.errors.icon" class="text-xs text-red-500">
                                    {{ form.errors.icon }}
                                </p>
                            </div>

                            <!-- ACTIONS -->
                            <div class="flex justify-end gap-3 border-t pt-4">
                                <Link href="/admin/services" class="text-sm text-gray-600">
                                    Cancel·lar
                                </Link>

                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="px-6 py-2 text-white rounded bg-blue-400"
                                >
                                    {{ form.processing ? 'Desant...' : 'Desar canvis' }}
                                </button>
                            </div>

                        </form>
                    </div>
                </div>

                <!-- PREVIEW -->
                <div class="lg:col-span-4">
                    <div class="sticky top-6 rounded-xl border bg-white p-6 space-y-4">
                        <h3 class="text-sm font-bold">Previsualització</h3>

                        <div class="flex items-center gap-3">
                            <div class="p-3 bg-blue-50 rounded">
                                <component
                                    v-if="selectedIconComponent"
                                    :is="selectedIconComponent"
                                    class="w-6 h-6 text-blue-600"
                                />
                                <span v-else class="text-xs text-gray-400">Cap icona</span>
                            </div>
                            <span>{{ form.name || '—' }}</span>
                        </div>

                        <div class="bg-gray-50 p-4 rounded space-y-2">
                            <div>
                                <p class="text-xs text-gray-400">ID</p>
                                <p>{{ service.id }}</p>
                            </div>

                            <div>
                                <p class="text-xs text-gray-400">Descripció</p>
                                <p>{{ form.description || '—' }}</p>
                            </div>

                            <div>
                                <p class="text-xs text-gray-400">Durada</p>
                                <p>{{ form.duration_minutes }} min</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AppLayout>
</template>