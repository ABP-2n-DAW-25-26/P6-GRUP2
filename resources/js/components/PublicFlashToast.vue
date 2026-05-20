<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { onMounted, onUnmounted, ref, watch } from 'vue';

const page = usePage<{
    flash?: {
        success?: string | Record<string, any>;
        error?: string;
    };
}>();

const visible = ref(false);
const message = ref('');
const type = ref<'success' | 'error'>('success');
let timer: ReturnType<typeof setTimeout> | null = null;

function show(msg: string, t: 'success' | 'error') {
    if (timer) {
        clearTimeout(timer);
    }

    message.value = msg;
    type.value = t;
    visible.value = true;
    timer = setTimeout(() => {
        visible.value = false;
    }, 4000);
}

watch(
    () => [page.props.flash?.success, page.props.flash?.error] as const,
    ([success, error]) => {
        if (success && typeof success === 'string') {
            show(success, 'success');
        } else if (error && typeof error === 'string') {
            show(error, 'error');
        }
    },
);

onMounted(() => {
    const flash = page.props.flash;

    if (flash?.success && typeof flash.success === 'string') {
        show(flash.success, 'success');
    } else if (flash?.error && typeof flash.error === 'string') {
        show(flash.error, 'error');
    }
});

onUnmounted(() => {
    if (timer) {
        clearTimeout(timer);
    }
});
</script>

<template>
    <div
        v-if="visible"
        class="mb-6 flex items-start gap-3 rounded-xl border bg-white px-4 py-3 shadow-sm"
        :class="type === 'success' ? 'border-emerald-200' : 'border-rose-200'"
        role="alert"
    >
        <span
            class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full"
            :class="type === 'success' ? 'bg-emerald-100 text-emerald-600' : 'bg-rose-100 text-rose-600'"
        >
            <svg
                class="h-3.5 w-3.5"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="3"
                stroke-linecap="round"
                stroke-linejoin="round"
            >
                <polyline v-if="type === 'success'" points="20 6 9 17 4 12" />
                <template v-else>
                    <line x1="18" y1="6" x2="6" y2="18" />
                    <line x1="6" y1="6" x2="18" y2="18" />
                </template>
            </svg>
        </span>
        <p class="text-sm font-medium text-slate-700">
            {{ message }}
        </p>
    </div>
</template>
