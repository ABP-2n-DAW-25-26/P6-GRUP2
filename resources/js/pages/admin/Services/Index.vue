<script setup lang="ts">
import { Link, Head, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import { SquarePen, Trash2, Plus, CalendarPlus } from 'lucide-vue-next'
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

// ICON MAP
const iconMap: Record<string, any> = {
    pill: Pill,
    heart: HeartPulse,
    flask: FlaskConical,
    shield: ShieldCheck,
    scan: ScanFace,
    droplet: Droplet,
    activity: Activity,
    stethoscope: Stethoscope,
    syringe: Syringe,
    microscope: Microscope,
    apple: Apple,
    brain: Brain,
    bandage: Bandage,
    thermometer: Thermometer,
}


const breadcrumbs: BreadcrumbItem[] = [
    { title: "Administració de serveis", href: dashboard().url },
]

// TYPES
interface Service {
    id: number
    name: string
    description: string
    duration_minutes: number
    icon: string | null
}

const props = defineProps<{
    services: Service[]
}>()


const search = ref('')


const searchResults = ref<Service[]>(props.services)


const filteredServices = ref<Service[]>(props.services)


function filter() {
    let result = searchResults.value

    if (search.value.trim() !== '') {
        result = result.filter(service =>
            service.name.toLowerCase().includes(search.value.toLowerCase())
        )
    }

    filteredServices.value = result
}


function handleSearch(event: Event) {
    const input = event.target as HTMLInputElement
    search.value = input.value
    filter()
}


// DELETE

const showDeleteModal = ref(false)

const selectedService = ref({
    id: 0,
    name: ''
})

function openDeleteModal(service: Service) {
    selectedService.value = {
        id: service.id,
        name: service.name
    }
    showDeleteModal.value = true
}

function deleteService(id: number) {
    const form = useForm({})
    const serviceId = selectedService.value.id

    form.delete('/admin/services/' + id, {
        onSuccess: () => {

            filteredServices.value = filteredServices.value.filter(
                s => s.id !== serviceId
            )

            searchResults.value = searchResults.value.filter(
                s => s.id !== serviceId
            )

            showDeleteModal.value = false
        },
        onFinish: () => {
            selectedService.value = { id: 0, name: '' }
        }
    })
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">

        <Head title="Gestió de serveis" />

        <div class="p-6 space-y-6">

            <!-- HEADER -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

                <div>
                    <h1 class="text-2xl font-bold">Gestió de Serveis</h1>
                    <p class="text-gray-500 text-sm">
                        Administra tots els serveis disponibles
                    </p>
                    <div v-if="$page.flash.message"
                        class="mx-auto mb-4 mt-4 w-full rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"
                        role="alert">
                        <p> {{ $page.flash.message }}</p>


                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <input :value="search" @input="handleSearch" type="text" placeholder="Cercar servei..."
                        class="border rounded-lg px-4 py-2 focus:ring focus:ring-blue-200" />

                    <Link :href="`/admin/services/create`"
                        class="flex items-center gap-2 px-4 py-2 rounded-lg text-white"
                        style="background-color: #aadbf0">
                        <Plus class="w-4 h-4" />
                        Nou servei
                    </Link>
                </div>
            </div>

            <!-- TABLE -->
            <div class="bg-white rounded-xl shadow border overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100 text-gray-600">
                        <tr>
                            <th class="p-3 text-left">Servei</th>
                            <th class="p-3 text-left">Descripció</th>
                            <th class="p-3 text-left">Durada</th>
                            <th class="p-3 text-right">Accions</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr v-for="service in filteredServices" :key="service.id" class="border-t hover:bg-gray-50">
                            <td class="p-3">
                                <div class="flex items-center gap-2">
                                    <component v-if="service.icon && iconMap[service.icon]" :is="iconMap[service.icon]"
                                        class="w-5 h-5 text-blue-600" />
                                    <span class="font-medium">
                                        {{ service.name }}
                                    </span>
                                </div>
                            </td>

                            <td class="p-3 text-gray-600">
                                {{ service.description }}
                            </td>

                            <td class="p-3">
                                {{ service.duration_minutes }} min
                            </td>

                            <td class="p-3">
                                <div class="flex justify-end gap-2 flex-wrap">

                                    <Link :href="`/admin/services/${service.id}`"
                                        class="p-2 rounded hover:bg-green-100">
                                        <CalendarPlus class="w-4 h-4 text-blue-600" />
                                    </Link>

                                    <Link :href="`/admin/services/${service.id}/edit`"
                                        class="p-2 rounded hover:bg-orange-100">
                                        <SquarePen class="w-4 h-4 text-orange-600" />
                                    </Link>

                                 <!---   <Link :href="`/admin/service-schedules/create?service_id=${service.id}`"
                                        class="p-2 rounded hover:bg-blue-100">
                                        <CalendarPlus class="w-4 h-4 text-blue-600" />
                                    </Link>--->

                                    <button @click="openDeleteModal(service)" class="p-2 rounded hover:bg-red-100">
                                        <Trash2 class="w-4 h-4 text-red-600" />
                                    </button>

                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- DELETE MODAL -->
        <div v-show="showDeleteModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
            <div class="bg-white rounded-xl p-6 w-full max-w-md space-y-4">

                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-bold">Eliminar servei</h2>
                    <button @click="showDeleteModal = false">✕</button>
                </div>

                <p class="text-gray-600">
                    Estàs segur que vols eliminar el servei:
                </p>

                <p class="font-semibold break-words">
                    {{ selectedService.name }}
                </p>

                <div class="flex justify-end gap-3 pt-4">
                    <button @click="showDeleteModal = false" class="px-4 py-2 border rounded">
                        Cancel·lar
                    </button>

                    <button @click="deleteService(selectedService.id)"
                        class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                        Eliminar definitivament
                    </button>
                </div>

            </div>
        </div>

    </AppLayout>
</template>