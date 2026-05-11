<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
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


const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Administració de serveis', href: dashboard().url },
    { title: 'Nou servei', href: '#' },
]

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
]

// FORM
const form = useForm({
    name: '',
    description: '',
    duration_minutes: 30,
    icon: '',
})

const submit = () => {
    form.post('/admin/services')
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">

        <Head title="Nou servei" />


        <div class="p-6 w-full max-w-5xl space-y-6">

            <!-- HEADER -->
            <div>
                <h1 class="text-2xl font-bold">Nou Servei</h1>
                <p class="text-gray-500 text-sm">
                    Crea un nou servei disponible per als pacients
                </p>
            </div>

            <!-- FORM CARD -->
            <div class="bg-white rounded-xl shadow border p-6">

                <form @submit.prevent="submit" class="space-y-6">


                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <!-- NAME -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Nom del servei
                            </label>
                            <input v-model="form.name" type="text" placeholder="Ex.: Consulta general"
                                class="mt-1 w-full rounded-lg border px-3 py-2 text-sm" />
                            <p v-if="form.errors.name" class="text-xs text-red-500 mt-1">
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <!-- DURATION -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Durada (minuts)
                            </label>
                            <input v-model="form.duration_minutes" type="number" min="1"
                                class="mt-1 w-full rounded-lg border px-3 py-2 text-sm" />
                            <p v-if="form.errors.duration_minutes" class="text-xs text-red-500 mt-1">
                                {{ form.errors.duration_minutes }}
                            </p>
                        </div>

                    </div>

                    <!-- DESCRIPTION-->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Descripció
                        </label>
                        <textarea v-model="form.description" rows="3" placeholder="Descripció breu del servei..."
                            class="mt-1 w-full rounded-lg border px-3 py-2 text-sm"></textarea>
                        <p v-if="form.errors.description" class="text-xs text-red-500 mt-1">
                            {{ form.errors.description }}
                        </p>
                    </div>

                    <!-- ICON -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Icona
                        </label>

                        <div class="flex flex-wrap gap-3">
                            <button v-for="opt in iconOptions" :key="opt.value" type="button"
                                @click="form.icon = opt.value" :class="[
                                    'flex items-center gap-2 px-3 py-2 rounded-lg border text-sm transition',
                                    form.icon === opt.value
                                        ? 'border-blue-500 bg-blue-50 text-blue-700'
                                        : 'border-gray-200 hover:bg-gray-50 text-gray-600',
                                ]">
                                <component :is="opt.icon" class="w-4 h-4" />
                                {{ opt.label }}
                            </button>
                        </div>

                        <p v-if="form.errors.icon" class="text-xs text-red-500 mt-1">
                            {{ form.errors.icon }}
                        </p>
                    </div>

                    <!-- ACTIONS -->
                    <div class="flex justify-end gap-3 pt-4 border-t">

                        <Link href="/admin/services" class="text-sm font-medium text-gray-600 hover:text-gray-900">
                            Cancel·lar
                        </Link>

                        <button type="submit" :disabled="form.processing"
                            class="rounded-lg px-6 py-2 text-sm font-medium text-white bg-blue-400 disabled:opacity-50">
                            {{ form.processing ? 'Desant...' : 'Crear servei' }}
                        </button>

                    </div>

                </form>

            </div>
        </div>
    </AppLayout>
</template>