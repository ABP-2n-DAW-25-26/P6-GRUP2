import { expect, test } from '@playwright/test';

test.describe('App Rendering', () => {
    test('should display the contact us page', async ({ page }) => {
        await page.goto('http://localhost:8080/contact-us');
        const mainContent = page.locator('body');
        await expect(mainContent).toBeVisible();
    });
});