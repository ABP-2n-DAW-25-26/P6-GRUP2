import { expect, test } from '@playwright/test';

test.describe('App Rendering Contact us page', () => {
    test('should display the contact us page', async ({ page }) => {
        await page.goto('http://localhost/contact-us');
        const mainContent = page.locator('body');
        await expect(mainContent).toBeVisible();
    });
});
