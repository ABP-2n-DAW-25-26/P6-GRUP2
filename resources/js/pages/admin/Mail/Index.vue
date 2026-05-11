<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Administració del Mail', href: 'admin/mail' },
];

import { store } from '@/routes/mail';

defineProps<{
    mail: {
        mail_mailer: string;
        mail_scheme: string;
        mail_host: string;
        mail_port: string;
        mail_username: string;
        mail_from_address: string;
    };
}>();
</script>
<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <h1 class="mb-6 text-2xl font-semibold">Configuració SMTP</h1>

            <Form v-bind="store.form()" class="max-w-2xl space-y-4">
                <div>
                    <label class="mb-1 block text-sm font-medium">Mailer</label>
                    <input
                        name="mail_mailer"
                        :value="mail.mail_mailer"
                        placeholder="smtp"
                        class="w-full rounded border px-3 py-2"
                    />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">Scheme</label>
                    <input
                        name="mail_scheme"
                        :value="mail.mail_scheme"
                        placeholder="smtp"
                        class="w-full rounded border px-3 py-2"
                    />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">Host</label>
                    <input
                        name="mail_host"
                        :value="mail.mail_host"
                        placeholder="smtp.example.com"
                        class="w-full rounded border px-3 py-2"
                    />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">Port</label>
                    <input
                        name="mail_port"
                        :value="mail.mail_port"
                        placeholder="587"
                        class="w-full rounded border px-3 py-2"
                    />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium"
                        >Username</label
                    >
                    <input
                        name="mail_username"
                        :value="mail.mail_username"
                        placeholder="you@example.com"
                        class="w-full rounded border px-3 py-2"
                    />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium"
                        >Password
                        <span class="font-normal text-gray-400"
                            >(deixa buit per mantenir l'actual)</span
                        ></label
                    >
                    <input
                        type="password"
                        name="mail_password"
                        placeholder="••••••••"
                        class="w-full rounded border px-3 py-2"
                    />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium"
                        >From Address</label
                    >
                    <input
                        name="mail_from_address"
                        :value="mail.mail_from_address"
                        placeholder="no-reply@example.com"
                        class="w-full rounded border px-3 py-2"
                    />
                </div>

                <button
                    type="submit"
                    class="rounded bg-black px-4 py-2 text-white"
                >
                    Guardar
                </button>
            </Form>
        </div>
    </AppLayout>
</template>
