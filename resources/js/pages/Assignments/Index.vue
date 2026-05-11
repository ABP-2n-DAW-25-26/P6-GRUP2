<script setup lang="ts">
import WebAppLayout from '@/layouts/WebAppLayout.vue';
import { Form } from '@inertiajs/vue3';
import { ref } from 'vue';
const formRef = ref<HTMLFormElement | null>(null);
function onSuccess() {
    formRef.value?.reset();
}
</script>

<template>
    <WebAppLayout>
        <section class="min-h-screen bg-blue-50 px-4 py-8 sm:px-6 lg:px-10">
            <div
                class="mx-auto mb-5 w-full max-w-3xl rounded-2xl bg-white p-4 shadow-lg sm:p-6"
            >
                <h1 class="mb-4 text-xl font-bold text-[#00617E] sm:text-2xl">
                    Consultar encàrrecs
                </h1>

                <Form
                    ref="formRef"
                    action="/assignments/code"
                    method="post"
                    class="w-full"
                    @success="onSuccess"
                >
                    <label for="search" class="sr-only"> Search </label>

                    <div class="relative">
                        <div
                            class="pointer-events-none absolute inset-y-0 start-0 flex items-center ps-4"
                        >
                            <svg
                                class="h-5 w-5 text-gray-500"
                                aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg"
                                width="24"
                                height="24"
                                fill="none"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke="currentColor"
                                    stroke-linecap="round"
                                    stroke-width="2"
                                    d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"
                                />
                            </svg>
                        </div>

                        <input
                            id="search"
                            type="email"
                            name="email"
                            placeholder="Consulta per corréu electrònic"
                            required
                            class="w-full rounded-2xl border border-gray-200 bg-gray-50 py-3 ps-11 pe-28 text-sm text-gray-900 transition outline-none placeholder:text-gray-500 focus:border-[#00617E] focus:bg-white focus:ring-2 focus:ring-[#00617E]/20"
                        />

                        <button
                            preserve-scroll
                            type="submit"
                            class="absolute end-1.5 top-1/2 -translate-y-1/2 rounded-xl bg-[#00617E] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#004f66] focus:ring-2 focus:ring-[#00617E]/30 focus:outline-none"
                        >
                            Enviar Codi
                        </button>
                    </div>
                </Form>
            </div>
            <div
                class="mx-auto w-full max-w-3xl rounded-2xl bg-white p-4 shadow-lg sm:p-6"
            >
                <h1 class="mb-4 text-xl font-bold text-[#00617E] sm:text-2xl">
                    Verifica el codi
                </h1>

                <Form
                    ref="formRef"
                    action="/assignments/code"
                    method="post"
                    class="w-full"
                    @success="onSuccess"
                >
                    <label for="search" class="sr-only"> Search </label>

                    <div class="relative">
                        <input
                            id="search"
                            type="email"
                            name="code"
                            placeholder="Codi"
                            required
                            class="w-full rounded-2xl border border-gray-200 bg-gray-50 py-3 ps-11 pe-28 text-sm text-gray-900 transition outline-none placeholder:text-gray-500 focus:border-[#00617E] focus:bg-white focus:ring-2 focus:ring-[#00617E]/20"
                        />

                        <button
                            preserve-scroll
                            type="submit"
                            class="absolute end-1.5 top-1/2 -translate-y-1/2 rounded-xl bg-[#00617E] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#004f66] focus:ring-2 focus:ring-[#00617E]/30 focus:outline-none"
                        >
                            Aceptarº
                        </button>
                    </div>
                </Form>
            </div>
        </section>
    </WebAppLayout>
</template>
