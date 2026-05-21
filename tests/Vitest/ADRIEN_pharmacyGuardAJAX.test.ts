import fs from 'node:fs';
import path from 'node:path';
import { afterAll, describe, expect, it } from 'vitest';

// Use internal Sail URL when running inside the container, otherwise use the external port
const defaultUrl = process.env.LARAVEL_SAIL
    ? 'http://localhost'
    : 'http://localhost:6969';
const baseUrl = (process.env.VITEST_BASE_URL ?? defaultUrl).replace(/\/$/, '');
const hotFilePath = path.resolve('public/hot');
const hotFileUrl = process.env.VITE_DEV_SERVER_URL ?? 'http://localhost:5173';
let createdHotFile = false;

describe('AJAX pharmacy guard', () => {
    it(
        'retorna dades de la farmàcia de guàrdia per a la data actual',
        { timeout: 10000 },
        async () => {
            // Create the Vite hot file if it does not exist so the app responds correctly
            if (!fs.existsSync(hotFilePath)) {
                fs.mkdirSync(path.dirname(hotFilePath), { recursive: true });
                fs.writeFileSync(hotFilePath, hotFileUrl, 'utf8');
                createdHotFile = true;
            }

            const today = new Date();
            const year = today.getFullYear();
            const month = today.getMonth() + 1;
            const day = today.getDate();

            // Make a real HTTP request to the pharmacy guard endpoint
            const response = await fetch(
                `${baseUrl}/pharmacyguard/${year}-${month}-${day}`,
            );

            // Fail with a descriptive message if the server returns an error
            if (!response.ok) {
                const body = await response.text();

                throw new Error(
                    `La petició a farmàcia de guàrdia ha fallat (${response.status}). ${body.slice(0, 400)}`,
                );
            }

            // Verify the response is JSON and not an HTML error page
            const contentType = response.headers.get('content-type') ?? '';

            if (!contentType.includes('application/json')) {
                const body = await response.text();

                throw new Error(
                    `S'esperava JSON però s'ha rebut ${contentType}. ${body.slice(0, 400)}`,
                );
            }

            const data = await response.json();

            // Verify the response contains the expected success field
            expect(data).toHaveProperty('success');
        },
    );
});

// Remove the hot file after all tests if we created it
afterAll(() => {
    if (createdHotFile && fs.existsSync(hotFilePath)) {
        fs.unlinkSync(hotFilePath);
    }
});
