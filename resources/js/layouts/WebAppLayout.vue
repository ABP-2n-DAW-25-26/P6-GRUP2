<script setup lang="ts">
import { Icon } from '@iconify/vue';
import Footer from '@/components/Footer.vue';
import Navbar from '@/components/Navbar.vue';
</script>

<template>
    <Navbar />
    <div class="min-h-[80dvh]">
        <slot />
    </div>
    <Footer />

    <a
        href="https://wa.me/34688466225"
        target="_blank"
        rel="noopener noreferrer"
        class="fixed right-4 bottom-4 z-50 flex h-15 w-15 items-center justify-center rounded-full bg-[#25D366] shadow-lg transition hover:scale-110 hover:bg-[#1ebe5d]"
        aria-label="Contacta'ns per WhatsApp"
    >
        <Icon
            icon="fontisto:whatsapp"
            width="30"
            height="30"
            class="text-white"
        />
    </a>
</template>
