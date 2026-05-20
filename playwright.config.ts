import { defineConfig, devices } from '@playwright/test';

export default defineConfig({
    testDir: './tests/E2e',
    use: {
        baseURL: process.env.APP_URL ?? 'http://localhost:6969',
    },
    projects: [
        {
            name: 'chromium',
            use: { ...devices['Desktop Chrome'] },
        },
    ],
});
