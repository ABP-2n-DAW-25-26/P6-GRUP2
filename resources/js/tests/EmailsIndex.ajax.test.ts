import { mount } from '@vue/test-utils';
import { describe, it, expect, vi } from 'vitest';
import EmailsIndex from '@/pages/admin/Emails/Index.vue';

vi.mock('@/layouts/AppLayout.vue', () => ({
    default: {
        template: '<div><slot /></div>',
    },
}));

vi.mock('@inertiajs/vue3', async () => ({
    Link: {
        template: '<a><slot /></a>',
    },

    useForm: () => ({
        active: 0,
        patch: vi.fn(),
    }),
}));

describe('ajax filter test', () => {
    it('calls fetch when filter changes', async () => {
        globalThis.fetch = vi.fn(() =>
            Promise.resolve({
                json: () => Promise.resolve([]),
            }),
        ) as any;

        const wrapper = mount(EmailsIndex, {
            props: {
                emails: [],
            },
        });

        await wrapper.get('select').setValue('Actiu');

        expect(fetch).toHaveBeenCalled();
    });
});
