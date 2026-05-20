<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Icon } from '@iconify/vue';

const isMobileMenuOpen = ref(false);
const page = usePage();

function isActive(path: string): boolean {
    return page.url === path || page.url.startsWith(`${path}/`);
}
</script>

<template>
    <!-- Top bar -->
    <div class="w-full bg-[#00617E] text-white">
        <div
            class="mx-auto flex max-w-7xl items-center justify-between px-4 py-2.5 text-xs"
        >
            <span class="hidden text-white/70 sm:block">Farmàcia Soler</span>

            <div class="flex items-center gap-4">
                <a
                    href="tel:+34688466225"
                    class="flex items-center gap-1.5 text-white/80 transition hover:text-white"
                >
                    <Icon icon="mdi:phone" width="13" height="13" />
                    688 466 225
                </a>

                <span class="text-white/30">|</span>

                <div class="flex items-center gap-3">
                    <a
                        href="https://www.instagram.com/farmaciasolerfigueres"
                        target="_blank"
                        aria-label="Instagram"
                        class="text-white/70 transition hover:text-white"
                    >
                        <Icon icon="ri:instagram-fill" width="15" height="15" />
                    </a>
                    <a
                        href="https://www.facebook.com/farmaciasolerfigueres"
                        target="_blank"
                        aria-label="Facebook"
                        class="text-white/70 transition hover:text-white"
                    >
                        <Icon icon="ri:facebook-fill" width="15" height="15" />
                    </a>
                    <a
                        href="https://www.tiktok.com/@farmaciasoler"
                        target="_blank"
                        aria-label="TikTok"
                        class="text-white/70 transition hover:text-white"
                    >
                        <Icon icon="ri:tiktok-fill" width="15" height="15" />
                    </a>
                </div>
            </div>
        </div>
    </div>

    <nav
        class="sticky top-0 z-30 w-full border-b border-[#DCEEF5] bg-white/95 backdrop-blur"
    >
        <div
            class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3"
        >
            <!-- Left: logo + desktop nav -->
            <div class="flex min-w-0 items-center gap-5">
                <a
                    href="/"
                    class="flex min-w-0 shrink items-center self-center"
                >
                    <img
                        class="h-10 w-auto md:h-11 lg:h-12"
                        src="/storage/FarmaciaSolerLogoLong.webp"
                        alt="Farmacia Soler"
                    />
                </a>

                <ul
                    class="hidden items-center gap-1 text-[1.05rem] font-semibold text-[#3F2E05] xl:flex xl:gap-2"
                >
                    <li>
                        <a
                            href="/#who_we_are"
                            class="rounded-lg px-2 py-2 whitespace-nowrap transition hover:bg-[#E6F5FB] hover:text-[#01617F]/90 lg:px-3"
                        >
                            Qui som
                        </a>
                    </li>
                    <li>
                        <a
                            href="/#pharmacy-guards"
                            class="rounded-lg px-2 py-2 whitespace-nowrap transition hover:bg-[#E6F5FB] hover:text-[#01617F]/90 lg:px-3"
                        >
                            Farmàcies de guàrdia
                        </a>
                    </li>
                    <li>
                        <Link
                            href="/workshops"
                            :class="[
                                'rounded-lg px-2 py-2 whitespace-nowrap transition lg:px-3',
                                isActive('/workshops')
                                    ? 'bg-[#E6F5FB] text-[#01617F]/90'
                                    : 'hover:bg-[#E6F5FB] hover:text-[#01617F]/90',
                            ]"
                        >
                            Tallers
                        </Link>
                    </li>
                    <li>
                        <Link
                            href="/appointments/create"
                            :class="[
                                'rounded-lg px-2 py-2 whitespace-nowrap transition lg:px-3',
                                isActive('/appointments/create')
                                    ? 'bg-[#E6F5FB] text-[#01617F]/90'
                                    : 'hover:bg-[#E6F5FB] hover:text-[#01617F]/90',
                            ]"
                        >
                            Demanar cita
                        </Link>
                    </li>
                    <li>
                        <Link
                            href="/assignments/create"
                            :class="[
                                'rounded-lg px-2 py-2 whitespace-nowrap transition lg:px-3',
                                isActive('/assignments/create')
                                    ? 'bg-[#E6F5FB] text-[#01617F]/90'
                                    : 'hover:bg-[#E6F5FB] hover:text-[#01617F]/90',
                            ]"
                        >
                            Demanar encàrrec
                        </Link>
                    </li>
                    <li>
                        <Link
                            href="/contact-us"
                            :class="[
                                'rounded-lg px-2 py-2 whitespace-nowrap transition lg:px-3',
                                isActive('/contact-us')
                                    ? 'bg-[#E6F5FB] text-[#01617F]/90'
                                    : 'hover:bg-[#E6F5FB] hover:text-[#01617F]/90',
                            ]"
                        >
                            Contacta'ns
                        </Link>
                    </li>
                </ul>
            </div>

            <!-- Right: mobile toggle -->
            <button
                class="flex h-10 w-10 items-center justify-center rounded-md text-[#2A5970] transition hover:bg-[#F1F9FD] xl:hidden"
                @click="isMobileMenuOpen = !isMobileMenuOpen"
            >
                <span class="sr-only">Open menu</span>
                <svg
                    class="h-6 w-6"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-width="2"
                        d="M5 7h14M5 12h14M5 17h14"
                    />
                </svg>
            </button>
        </div>

        <!-- Mobile menu -->
        <div
            v-show="isMobileMenuOpen"
            class="border-t border-[#DCEEF5] bg-white px-4 py-4 xl:hidden"
        >
            <ul
                class="flex flex-col gap-2 text-lg font-semibold text-[#3F2E05]"
            >
                <li>
                    <a
                        href="/#who_we_are"
                        class="block rounded-lg px-3 py-2 transition hover:bg-[#E6F5FB] hover:text-[#01617F]/90"
                        @click="isMobileMenuOpen = false"
                    >
                        Qui som
                    </a>
                </li>
                <li>
                    <a
                        href="/#pharmacies-guards"
                        class="block rounded-lg px-3 py-2 transition hover:bg-[#E6F5FB] hover:text-[#01617F]/90"
                        @click="isMobileMenuOpen = false"
                    >
                        Farmàcies de guàrdia
                    </a>
                </li>
                <li>
                    <Link
                        href="/workshops"
                        :class="[
                            'block rounded-lg px-3 py-2 transition',
                            isActive('/workshops')
                                ? 'bg-[#E6F5FB] text-[#01617F]/90'
                                : 'hover:bg-[#E6F5FB] hover:text-[#01617F]/90',
                        ]"
                        @click="isMobileMenuOpen = false"
                    >
                        Tallers
                    </Link>
                </li>
                <li>
                    <Link
                        href="/appointments/create"
                        :class="[
                            'block rounded-lg px-3 py-2 transition',
                            isActive('/appointments/create')
                                ? 'bg-[#E6F5FB] text-[#01617F]/90'
                                : 'hover:bg-[#E6F5FB] hover:text-[#01617F]/90',
                        ]"
                        @click="isMobileMenuOpen = false"
                    >
                        Demanar cita
                    </Link>
                </li>
                <li>
                    <Link
                        href="/assignments/create"
                        :class="[
                            'block rounded-lg px-3 py-2 transition',
                            isActive('/assignments/create')
                                ? 'bg-[#E6F5FB] text-[#01617F]/90'
                                : 'hover:bg-[#E6F5FB] hover:text-[#01617F]/90',
                        ]"
                        @click="isMobileMenuOpen = false"
                    >
                        Demanar encàrrec
                    </Link>
                </li>
                <li>
                    <Link
                        href="/contact-us"
                        :class="[
                            'block rounded-lg px-3 py-2 transition',
                            isActive('/contact-us')
                                ? 'bg-[#E6F5FB] text-[#01617F]/90'
                                : 'hover:bg-[#E6F5FB] hover:text-[#01617F]/90',
                        ]"
                        @click="isMobileMenuOpen = false"
                    >
                        Contacta'ns
                    </Link>
                </li>
            </ul>
        </div>
    </nav>
</template>
