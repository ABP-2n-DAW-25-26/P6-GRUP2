import { mount } from '@vue/test-utils';
import { afterEach, describe, expect, it } from 'vitest';
import ConfirmAppointmentModal from '../../resources/js/components/ConfirmAppointmentModal.vue';

// Default props reused across tests
const defaultProps = {
    show: true,
    serviceName: 'Revisió de tensió',
    date: '20 Maig 2026',
    time: '10:30',
    isSubmitting: false,
};

describe('ConfirmAppointmentModal', () => {
    let wrapper: ReturnType<typeof mount> | undefined;

    // Unmount the component after each test to avoid state leaking between tests
    afterEach(() => {
        if (wrapper) {
            wrapper.unmount();
            wrapper = undefined;
        }
    });

    it('no es mostra quan show és false', () => {
        wrapper = mount(ConfirmAppointmentModal, {
            props: { ...defaultProps, show: false },
        });

        // The modal overlay should not exist in the DOM
        expect(wrapper.find('.fixed').exists()).toBe(false);
    });

    it('es mostra amb les dades correctes quan show és true', () => {
        wrapper = mount(ConfirmAppointmentModal, {
            props: defaultProps,
        });

        // All booking details must be visible
        expect(wrapper.text()).toContain('Revisió de tensió');
        expect(wrapper.text()).toContain('20 Maig 2026');
        expect(wrapper.text()).toContain('10:30');
    });

    it('emet cancel quan es clica el botó Cancel·lar', async () => {
        wrapper = mount(ConfirmAppointmentModal, {
            props: defaultProps,
        });

        const cancelButton = wrapper
            .findAll('button')
            .find((b) => b.text().includes('Cancel·lar'));

        expect(cancelButton).toBeTruthy();
        await cancelButton!.trigger('click');

        // Verify the cancel event was emitted to the parent
        expect(wrapper.emitted('cancel')).toBeTruthy();
    });

    it('emet confirm quan es clica el botó Sí, confirmar', async () => {
        wrapper = mount(ConfirmAppointmentModal, {
            props: defaultProps,
        });

        const confirmButton = wrapper
            .findAll('button')
            .find((b) => b.text().includes('confirmar'));

        expect(confirmButton).toBeTruthy();
        await confirmButton!.trigger('click');

        // Verify the confirm event was emitted to the parent
        expect(wrapper.emitted('confirm')).toBeTruthy();
    });

    it('desactiva els botons i mostra Confirmant… quan isSubmitting és true', () => {
        wrapper = mount(ConfirmAppointmentModal, {
            props: { ...defaultProps, isSubmitting: true },
        });

        // Both buttons must be disabled while the form is submitting
        wrapper.findAll('button').forEach((button) => {
            expect(button.attributes('disabled')).toBeDefined();
        });

        expect(wrapper.text()).toContain('Confirmant…');
    });
});
