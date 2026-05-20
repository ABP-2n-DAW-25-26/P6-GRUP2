import { describe, it, expect } from 'vitest';

const baseUrl = 'http://localhost';

async function getInertiaPage(url: string) {
    const html = await fetch(url).then((r) => r.text());
    const doc = new DOMParser().parseFromString(html, 'text/html');
    const raw =
        doc.querySelector('script[data-page="app"]')?.textContent ?? '{}';
    return JSON.parse(raw) as {
        component: string;
        props: Record<string, unknown>;
    };
}

describe('assignments/create page DOM', () => {
    it('loads the create assignment component', async () => {
        const page = await getInertiaPage(`${baseUrl}/assignments/create`);
        expect(page.component).toBe('Assignments/Create');
    });

    it('has name, address, phone_number and description props', async () => {
        const page = await getInertiaPage(`${baseUrl}/assignments/create`);
        expect(page.props).toHaveProperty('name');
        expect(page.props).toHaveProperty('address');
        expect(page.props).toHaveProperty('phone_number');
        expect(page.props).toHaveProperty('description');
    });
});
