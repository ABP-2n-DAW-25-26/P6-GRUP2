import { describe, expect, test } from 'vitest';

describe('Assignment DOM', () => {
    test('form contains expected elements', () => {
        document.body.innerHTML = `
            <form>
                <input id="name" />
                <input id="address" />
                <textarea id="description"></textarea>
                <button type="submit">Enviar</button>
            </form>
        `;

        const nameInput = document.getElementById('name');
        const addressInput = document.getElementById('address');
        const description = document.getElementById('description');
        const button = document.querySelector('button');

        expect(nameInput).not.toBeNull();
        expect(addressInput).not.toBeNull();
        expect(description).not.toBeNull();

        expect(button?.textContent).toContain('Enviar');
    });
});