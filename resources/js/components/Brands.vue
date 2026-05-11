<script setup lang="ts">
const brands = [
    { name: 'Caudalie', logo: '/storage/brands/caudalie.svg' },
    { name: 'La Roche-Posay', logo: '/storage/brands/la-roche-posay.svg' },
    { name: 'Vichy', logo: '/storage/brands/vichy.svg' },
    { name: "L'Oréal", logo: '/storage/brands/loreal.svg' },
    { name: 'Darphin', logo: '/storage/brands/darphin.svg' },
    { name: 'Solaray', logo: '/storage/brands/solaray.svg' },
    { name: 'A-Derma', logo: '/storage/brands/a-derma.svg' },
];
</script>

<template>
    <section class="bg-[#F2FAFF] py-14">
        <div class="mb-8 text-center">
            <h2 class="text-3xl font-bold text-[#124559] md:text-4xl">
                Marques amb les que treballem
            </h2>
            <div class="mx-auto mt-3 h-1 w-16 rounded-full bg-[#01617F]"></div>
        </div>

        <div class="flex flex-col gap-4">
            <div
                v-for="(direction, row) in ['left', 'right']"
                :key="row"
                class="relative overflow-hidden"
            >
                <div
                    class="pointer-events-none absolute inset-y-0 left-0 z-10 w-32 bg-linear-to-r from-[#F2FAFF] to-transparent"
                ></div>
                <div
                    class="pointer-events-none absolute inset-y-0 right-0 z-10 w-32 bg-linear-to-l from-[#F2FAFF] to-transparent"
                ></div>

                <div :class="`marquee-${direction} flex gap-5`">
                    <div
                        v-for="(brand, i) in [
                            ...brands,
                            ...brands,
                            ...brands,
                            ...brands,
                        ]"
                        :key="`${row}-${i}`"
                        class="flex h-20 w-48 shrink-0 items-center justify-center rounded-xl border border-[#D0EAF3] bg-white px-5 py-3 shadow-sm transition-all duration-300 hover:border-[#01617F]/40 hover:bg-[#E9F7FC] hover:shadow-md"
                    >
                        <img
                            :src="brand.logo"
                            :alt="brand.name"
                            class="max-h-10 max-w-full object-contain opacity-60 grayscale transition-all duration-300 hover:opacity-100 hover:grayscale-0"
                        />
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<style scoped>
.marquee-left {
    animation: scroll-left 10s linear infinite;
}
.marquee-right {
    animation: scroll-right 10s linear infinite;
}

@media (min-width: 768px) {
    .marquee-left {
        animation-duration: 32s;
    }
    .marquee-right {
        animation-duration: 32s;
    }
}
.marquee-left:hover,
.marquee-right:hover {
    animation-play-state: paused;
}

@keyframes scroll-left {
    from {
        transform: translateX(0);
    }
    to {
        transform: translateX(-25%);
    }
}
@keyframes scroll-right {
    from {
        transform: translateX(-25%);
    }
    to {
        transform: translateX(0);
    }
}
</style>
