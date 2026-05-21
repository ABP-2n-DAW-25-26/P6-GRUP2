import { describe, it, expect } from 'vitest';

const baseUrl = 'http://localhost';

const adminEmail = 'admin@farmaciasoler.com';
const adminPassword = 'knqm4141';

const jar: Record<string, string> = {};

const updateCookieJar = (response: Response) => {
    const setCookie =
        response.headers.getSetCookie?.() ??
        (response.headers.get('set-cookie')
            ? [response.headers.get('set-cookie')]
            : []);

    for (const cookie of setCookie) {
        if (!cookie) continue;

        const [pair] = cookie.split(';');   
        const index = pair.indexOf('=');

        if (index === -1) continue;

        const name = pair.slice(0, index).trim();
        const value = pair.slice(index + 1).trim();

        jar[name] = value;
    }
};

const buildCookieHeader = () =>
    Object.entries(jar)
        .map(([k, v]) => `${k}=${v}`)
        .join('; ');

describe('test pharmacy', () => {
    it('returns JSON with all pharmacys', async () => {
        const loginPage = await fetch(`${baseUrl}/`, {
            redirect: 'manual',
        });

        updateCookieJar(loginPage);

        const csrf = decodeURIComponent(jar['XSRF-TOKEN'] ?? '');
        
        const loginResponse = await fetch(`${baseUrl}/login`, {
            method: 'POST',
            redirect: 'manual',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-XSRF-TOKEN': csrf,
                Cookie: buildCookieHeader(),
                Accept: 'text/html',
            },
            body: new URLSearchParams({
                email: adminEmail,
                password: adminPassword,
            }).toString(),
        });

        updateCookieJar(loginResponse);

        const farmacia1 = {
            name: 'farmacia_dia',
            latitude: 1,
            longitude: 2,
        }

        // Add Pharmacy 1
        
        await fetch(`${baseUrl}/admin/pharmacies`, {
            method: "POST",
            body: JSON.stringify(farmacia1),
            headers: {
                Accept: 'application/json',
                Cookie: buildCookieHeader(),
            },
        });
        
        const farmacia2 = {
            name: 'farmacia_nit',
            latitude: 2,
            longitude: 3,
        }

        // Add Pharmacy 2

        await fetch(`${baseUrl}/admin/pharmacies`, {
            method: "POST",
            body: JSON.stringify(farmacia2),
            headers: {
                Accept: 'application/json',
                Cookie: buildCookieHeader(),
            },
        });

        const response = await fetch(`${baseUrl}/admin/pharmacies`, {
            headers: {
                Accept: 'application/json',
                Cookie: buildCookieHeader(),
            },
        });

        // Test

        expect(response.ok).toBe(true);

        const data = await response.json();
        expect(Array.isArray(data)).toBe(true);
    });
});