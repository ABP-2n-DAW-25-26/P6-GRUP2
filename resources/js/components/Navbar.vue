<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

const isMobileMenuOpen = ref(false);
const page = usePage();

function isActive(path: string): boolean {
  return page.url === path || page.url.startsWith(`${path}/`);
}
</script>

<template>
  <nav class="sticky top-0 z-30 w-full border-b border-[#DCEEF5] bg-white/95 backdrop-blur">
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3">
      <div class="flex min-w-0 items-center gap-5">
        <a href="/" class="flex items-center">
          <img class="h-10 w-auto md:h-11 lg:h-12" src="/storage/FarmaciaSolerLogoLong.jpeg" alt="Farmacia Soler" />
        </a>

        <ul class="hidden items-center gap-1 text-[1.05rem] font-semibold text-[#3F2E05] md:flex lg:gap-2">
          <li>
            <a
              href="/#qui-som"
              class="rounded-lg px-2 py-2 whitespace-nowrap transition hover:bg-[#E6F5FB] hover:text-[#01617F] lg:px-3"
            >
              Qui som
            </a>
          </li>
          <li>
            <a
              href="/#farmacies-guardia"
              class="rounded-lg px-2 py-2 whitespace-nowrap transition hover:bg-[#E6F5FB] hover:text-[#01617F] lg:px-3"
            >
              Farmàcies de guàrdia
            </a>
          </li>
          <li>
            <Link
              href="/pedir-cita"
              :class="[
                'rounded-lg px-2 py-2 whitespace-nowrap transition lg:px-3',
                isActive('/pedir-cita') ? 'bg-[#E6F5FB] text-[#01617F]' : 'hover:bg-[#E6F5FB] hover:text-[#01617F]'
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
                isActive('/assignments/create') ? 'bg-[#E6F5FB] text-[#01617F]' : 'hover:bg-[#E6F5FB] hover:text-[#01617F]'
              ]"
            >
              Demanar encàrrec
            </Link>
          </li>
          <li>
            <Link
              href="/contactans"
              :class="[
                'rounded-lg px-2 py-2 whitespace-nowrap transition lg:px-3',
                isActive('/contactans') ? 'bg-[#E6F5FB] text-[#01617F]' : 'hover:bg-[#E6F5FB] hover:text-[#01617F]'
              ]"
            >
              Contacta’ns
            </Link>
          </li>
        </ul>
      </div>

      <div class="flex items-center gap-2">
        <button
          class="flex h-10 w-10 items-center justify-center rounded-md text-[#2A5970] transition hover:bg-[#F1F9FD] md:hidden"
          @click="isMobileMenuOpen = !isMobileMenuOpen"
        >
          <span class="sr-only">Open menu</span>
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-width="2" d="M5 7h14M5 12h14M5 17h14" />
          </svg>
        </button>
      </div>
    </div>

    <div v-show="isMobileMenuOpen" class="border-t border-[#DCEEF5] bg-white px-4 py-4 md:hidden">
      <ul class="flex flex-col gap-2 text-lg font-semibold text-[#3F2E05]">
        <li>
          <a
            href="/#qui-som"
            class="block rounded-lg px-3 py-2 transition hover:bg-[#E6F5FB] hover:text-[#01617F]"
            @click="isMobileMenuOpen = false"
          >
            Qui som
          </a>
        </li>
        <li>
          <a
            href="/#farmacies-guardia"
            class="block rounded-lg px-3 py-2 transition hover:bg-[#E6F5FB] hover:text-[#01617F]"
            @click="isMobileMenuOpen = false"
          >
            Farmàcies de guàrdia
          </a>
        </li>
        <li>
          <Link
            href="/pedir-cita"
            :class="[
              'block rounded-lg px-3 py-2 transition',
              isActive('/pedir-cita') ? 'bg-[#E6F5FB] text-[#01617F]' : 'hover:bg-[#E6F5FB] hover:text-[#01617F]'
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
              isActive('/assignments/create') ? 'bg-[#E6F5FB] text-[#01617F]' : 'hover:bg-[#E6F5FB] hover:text-[#01617F]'
            ]"
            @click="isMobileMenuOpen = false"
          >
            Demanar encàrrec
          </Link>
        </li>
        <li>
          <Link
            href="/contactans"
            :class="[
              'block rounded-lg px-3 py-2 transition',
              isActive('/contactans') ? 'bg-[#E6F5FB] text-[#01617F]' : 'hover:bg-[#E6F5FB] hover:text-[#01617F]'
            ]"
            @click="isMobileMenuOpen = false"
          >
            Contacta’ns
          </Link>
        </li>
      </ul>
    </div>
  </nav>
</template>
