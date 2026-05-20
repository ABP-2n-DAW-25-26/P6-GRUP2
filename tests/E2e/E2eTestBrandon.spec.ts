import { expect, test } from '@playwright/test';

test.describe('App Rendering', () => {
    test('should display the main welcome page', async ({ page }) => {
        await page.goto('http://localhost/assignment/create');
        const mainContent = page.locator('body');
        await expect(mainContent).toBeVisible();
    });
});
