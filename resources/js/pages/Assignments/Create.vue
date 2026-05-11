<script setup lang="ts">
import WebAppLayout from '@/layouts/WebAppLayout.vue';
import { store } from '@/routes/assignments';
// import { show } from '@/routes/users';
import { Form, Link } from '@inertiajs/vue3';
import { onMounted, onUnmounted } from 'vue';
import { ref } from 'vue';

const props = defineProps<{
    turnstileSiteKey: string | null;
    name: string | null;
    address: string | null;
    phone_number: number | null;
    description: string | null;
}>();

function renderTurnstile() {
    const el = document.querySelector('.cf-turnstile') as HTMLElement | null;
    const t = (window as any).turnstile;

    if (!el || !props.turnstileSiteKey || !t) return;

    el.innerHTML = '';
    t.render(el, {
        sitekey: props.turnstileSiteKey,
        language: 'es',
    });
}

onMounted(() => {
    if (!props.turnstileSiteKey) return;

    if (document.getElementById('cf-turnstile-api')) {
        renderTurnstile();
        return;
    }

    const s = document.createElement('script');
    s.id = 'cf-turnstile-api';
    s.src =
        'https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit';
    s.async = true;
    s.defer = true;
    s.onload = renderTurnstile;
    document.head.appendChild(s);
});

onUnmounted(() => {
    const s = document.getElementById('cf-turnstile-api');
    if (s) {
        s.remove();
    }
});

const formRef = ref<HTMLFormElement | null>(null);
const showModal = ref(false);

function onSuccess() {
    showModal.value = true;
    formRef.value?.reset();
}
</script>
<template>
    <WebAppLayout>
        <div
            class="min-h-screen bg-blue-50 px-4 py-6 sm:px-6 lg:px-10 xl:px-16"
        >
            <div class="pb-10">
                <h1 class="text-2xl font-extrabold sm:text-3xl lg:text-4xl">
                    Encàrrecs
                </h1>
            </div>
            <div class="flex items-center gap-3 pb-5">
                <h2
                    class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-[#00617E] text-3xl leading-none font-extrabold text-white italic"
                >
                    <span class="-translate-x-px">1</span>
                </h2>
                <p class="text-lg italic sm:text-xl lg:text-2xl">
                    Demana el que necessitis.
                </p>
                <Link
                    class="ml-auto rounded-lg bg-[#00617E] p-2 text-xl font-bold text-white"
                    href="/assignments"
                    >Consultar Encarrecs
                </Link>
            </div>
            <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
                <div
                    class="space-y-6 rounded-2xl bg-[#ffffff] p-4 shadow-lg sm:p-5 lg:col-span-2"
                >
                    <Form
                        ref="formRef"
                        :action="store()"
                        method="POST"
                        class="space-y-4"
                        @success="onSuccess"
                        #default="{ errors, validating }"
                    >
                        <div>
                            <label
                                class="mb-2 block text-lg text-gray-900 italic"
                                >Nom</label
                            >
                            <input
                                type="text"
                                name="name"
                                class="w-full rounded-2xl border-none bg-gray-200 px-4 py-4 outline-none"
                            />
                        </div>

                        <div class="flex flex-col gap-4 md:flex-row md:gap-6">
                            <div class="flex-1">
                                <label
                                    class="mb-2 block text-lg text-gray-900 italic"
                                    >Adreça</label
                                >
                                <input
                                    type="email"
                                    name="address"
                                    class="w-full rounded-2xl border-none bg-gray-200 px-4 py-4 outline-none"
                                />
                            </div>
                            <div class="flex-1">
                                <label
                                    class="mb-2 block text-lg text-gray-900 italic"
                                    >Telefón</label
                                >
                                <input
                                    type="tel"
                                    name="phone_number"
                                    class="w-full rounded-2xl border-none bg-gray-200 px-4 py-4 outline-none"
                                />
                            </div>
                        </div>

                        <div>
                            <label
                                class="mb-2 block text-lg text-gray-900 italic"
                                >Descripció</label
                            >
                            <textarea
                                maxlength="500"
                                rows="8"
                                name="description"
                                class="w-full resize-none rounded-2xl border-none bg-gray-200 px-4 py-4 outline-none"
                            />
                        </div>

                        <div class="flex items-start gap-3">
                            <input
                                required
                                type="checkbox"
                                class="h-5 w-5 cursor-pointer rounded border-gray-300 bg-gray-200 accent-gray-500"
                            />
                            <span class="text-sm text-gray-800">
                                He leído y acepto las
                                <a href="#" class="underline"
                                    >condiciones de uso</a
                                >
                                y la
                                <a href="#" class="underline"
                                    >política de privacidad</a
                                >
                            </span>
                        </div>
                        <div v-if="props.turnstileSiteKey" class="pt-2">
                            <div
                                class="cf-turnstile"
                                :data-sitekey="props.turnstileSiteKey"
                                data-language="es"
                            ></div>
                            <p
                                v-if="errors['cf-turnstile-response']"
                                class="mt-2 text-sm text-red-600"
                            >
                                {{ errors['cf-turnstile-response'] }}
                            </p>
                        </div>

                        <div>
                            <button
                                preserve-scroll
                                class="rounded-2xl bg-yellow-300 px-4 py-3 text-lg font-medium sm:text-xl lg:text-2xl"
                                type="submit"
                                :disabled="validating"
                            >
                                Enviar
                            </button>
                        </div>
                    </Form>
                </div>
                <div
                    class="space-y-6 rounded-2xl bg-[#ffffff] p-4 shadow-lg sm:p-5"
                >
                    <div class="flex items-start gap-3 pb-5">
                        <h2
                            class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-[#00617E] text-3xl leading-none font-extrabold text-white italic"
                        >
                            <span class="-translate-x-px">2</span>
                        </h2>
                        <p class="text-base italic sm:text-lg lg:text-xl">
                            Prepararem el teu encarrec lo abans possible i
                            t’enviarem un missatge quan ja estigui llest.
                        </p>
                    </div>
                    <div class="flex items-start gap-3 pb-5">
                        <h2
                            class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-[#00617E] text-3xl leading-none font-extrabold text-white italic"
                        >
                            <span class="-translate-x-px">3</span>
                        </h2>
                        <p class="text-base italic sm:text-lg lg:text-xl">
                            Vina a buscar el teu encarrec i paga a la nostre
                            farmacia!
                        </p>
                    </div>
                    <div>
                        <img
                            src="/storage/vagaro-ckWrZO5sWOQ-unsplash.jpg"
                            alt="Image"
                            class="h-48 w-full rounded-xl object-cover sm:h-56 md:h-64"
                        />
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal — outside the scrollable div so it overlays everything -->
        <div
            v-if="showModal"
            class="fixed top-0 right-0 left-0 z-50 flex h-[calc(100%-1rem)] max-h-full w-full items-center justify-center overflow-x-hidden overflow-y-auto md:inset-0"
        >
            <div class="relative max-h-full w-full max-w-2xl p-4">
                <div
                    class="border-default rounded-base relative border bg-white p-4 shadow-sm md:p-6"
                >
                    <!-- Header -->
                    <div
                        class="border-default flex items-center justify-between border-b pb-4 md:pb-5"
                    >
                        <h3 class="text-heading text-lg font-medium">
                            Encàrrec creat correctament
                        </h3>
                        <button
                            type="button"
                            class="text-body hover:bg-neutral-tertiary hover:text-heading rounded-base ms-auto inline-flex h-9 w-9 items-center justify-center bg-transparent text-sm"
                            @click="showModal = false"
                        >
                            <svg
                                class="h-5 w-5"
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
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M6 18 17.94 6M18 18 6.06 6"
                                />
                            </svg>
                            <span class="sr-only">Tancar</span>
                        </button>
                    </div>
                    <!-- Body -->
                    <div class="py-4">
                        <p class="text-body text-1xl leading-relaxed font-bold">
                            Si us plau, confirmeu la vostra comanda.
                        </p>
                        <p class="text-body leading-relaxed">
                            <strong>Nom: </strong>{{ props.name }}
                        </p>
                        <p class="text-body leading-relaxed">
                            <strong>Address: </strong>{{ props.address }}
                        </p>
                        <p class="text-body leading-relaxed">
                            <strong>Telefón: </strong>{{ props.phone_number }}
                        </p>
                        <p
                            class="text-body flex items-start gap-2 leading-relaxed"
                        >
                            <strong class="shrink-0">Encarrec:</strong>
                            <span
                                class="min-w-0 [overflow-wrap:anywhere] break-words whitespace-pre-line"
                            >
                                {{ props.description }}
                            </span>
                        </p>
                        <p class="text-body text-1xl leading-relaxed font-bold">
                            Rebreu una confirmació per correu electrònic.
                        </p>
                    </div>
                    <!-- Footer -->
                    <div
                        class="border-default flex items-center space-x-4 border-t pt-4 md:pt-5"
                    >
                        <button
                            type="button"
                            class="rounded-2xl bg-yellow-300 px-4 py-3 text-lg font-medium sm:text-xl"
                            @click="showModal = false"
                        >
                            Acceptar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </WebAppLayout>
</template>
