<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { home } from '@/routes';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div
        class="relative flex min-h-svh flex-col items-center justify-center overflow-hidden bg-background p-6 md:p-10"
    >
        <div
            class="pointer-events-none absolute -top-20 -left-16 h-64 w-64 rounded-full bg-[#dbeaf4]/80 blur-3xl"
        ></div>
        <div
            class="pointer-events-none absolute -right-12 bottom-0 h-72 w-72 rounded-full bg-[#0f5f7f]/15 blur-3xl"
        ></div>

        <div class="relative w-full max-w-md">
            <div class="flex flex-col gap-6">
                <div class="flex flex-col items-center gap-4 text-center">
                    <Link
                        :href="home()"
                        class="inline-flex items-center justify-center"
                    >
                        <img
                            src="/storage/FarmaciaSolerLogoLong.svg"
                            alt="Farmacia Soler"
                            class="h-14 w-auto rounded-lg border border-[#dbeaf4] bg-card p-1.5 shadow-sm"
                        />
                    </Link>
                    <div class="space-y-1.5">
                        <h1
                            class="text-2xl font-semibold tracking-tight text-[#0f5f7f] dark:text-foreground"
                        >
                            {{ title }}
                        </h1>
                        <p class="text-sm text-muted-foreground">
                            {{ description }}
                        </p>
                    </div>
                </div>

                <div
                    class="rounded-2xl border border-[#dbeaf4] bg-card/95 p-6 shadow-sm backdrop-blur md:p-8 dark:border-border"
                >
                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>
