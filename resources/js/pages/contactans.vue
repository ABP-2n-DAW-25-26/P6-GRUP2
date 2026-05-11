<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { Icon } from '@iconify/vue';
import WebAppLayout from '@/layouts/WebAppLayout.vue';
import Card from '@/components/Card.vue';
import 'leaflet';
import 'leaflet/dist/leaflet.css';

const map = ref();

onMounted(() => {
    // Setup Map
    map.value = L.map('map').setView(['42.2655267', '2.9631527'], 19);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
    }).addTo(map.value);
    L.marker(['42.2655267', '2.9631527']).addTo(map.value);
});
</script>

<template>
    <WebAppLayout>
        <div class="bg-[#F2FAFF] px-2 py-12 md:px-24">
            <Card class="flex-col bg-white md:flex-row">
                <div
                    id="map"
                    class="flex h-110 w-full items-end overflow-hidden rounded-xl md:relative md:h-auto md:w-1/2"
                ></div>
                <div
                    class="flex h-160 flex-col gap-4 px-8 py-8 md:w-1/2 md:py-16"
                >
                    <h2
                        class="text-center text-2xl font-bold md:text-left md:text-4xl"
                    >
                        Contactan's
                    </h2>
                    <div>
                        <h3 class="text-xl font-medium">Horari d'atenció</h3>
                        <div class="pt-4 pl-6">
                            <li>Dilluns: 8:30 am - 8:30 pm</li>
                            <li>Dimarts: 8:30 am - 8:30 pm</li>
                            <li>Dimecres: 8:30 am - 8:30 pm</li>
                            <li>Dijous: 8:30 am - 8:30 pm</li>
                            <li>Divendres: 8:30 am - 8:30 pm</li>
                            <li>Dissabte: 8:30 am - 8:30 pm</li>
                            <li>Diumenge: Tancat</li>
                        </div>
                    </div>
                    <div class="flex flex-col gap-1">
                        <div class="flex gap-1">
                            <Icon icon="ic:mail" width="24" height="24"></Icon>
                        </div>
                        <div class="flex gap-1">
                            <Icon
                                icon="ri:phone-fill"
                                width="24"
                                height="24"
                            ></Icon>
                            <a href="tel:+34972500299">972 50 02 99</a>
                        </div>
                        <div class="flex gap-1">
                            <Icon
                                icon="ri:instagram-fill"
                                width="24"
                                height="24"
                            ></Icon>
                            <a
                                href="https://www.instagram.com/farmaciasolerfigueres/"
                                >farmaciasolerfigueres</a
                            >
                        </div>
                        <div class="flex gap-1">
                            <Icon
                                icon="mage:tiktok-circle"
                                width="24"
                                height="24"
                            ></Icon>
                            <a href="https://www.tiktok.com/@farmaciasoler"
                                >farmaciasoler</a
                            >
                        </div>
                        <div class="flex gap-1">
                            <Icon
                                icon="ic:twotone-facebook"
                                width="24"
                                height="24"
                            ></Icon>
                            <a
                                href="https://es-es.facebook.com/farmaciasolerfigueres"
                                >farmaciasolerfigueres</a
                            >
                        </div>
                    </div>
                    <p class="text-gray-700 italic underline">
                        Carrer Nou, 22, 17600 Figueres, Girona, Spain
                    </p>
                </div>
            </Card>
        </div>
    </WebAppLayout>
    <div class="fixed right-0 bottom-0">
        <a
            class="mr-4 mb-4 flex h-15 w-15 cursor-pointer justify-center rounded-4xl bg-[#08c004]"
            href="ht tps://wa.me/34658359458?text=Hola%2C%20estoy%20interesado%20en%20"
        >
            <Icon
                icon="fontisto:whatsapp"
                width="35"
                height="35"
                class="self-center text-white"
            ></Icon>
        </a>
    </div>
</template>
