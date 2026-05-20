import { expect, test } from '@playwright/test';

test.describe('Assignment Form', () => {
    test('should display and fill the form', async ({ page }) => {
        await page.goto('http://localhost:8080/assignments/create');

        // Check inputs exist
        await expect(page.locator('#name')).toBeVisible();
        await expect(page.locator('#address')).toBeVisible();
        await expect(page.locator('#phone_number')).toBeVisible();
        await expect(page.locator('#description')).toBeVisible();

        // Fill form
        await page.locator('#name').fill('Ilyass');
        await page.locator('#address').fill('Barcelona');
        await page.locator('#phone_number').fill('123456789');
        await page.locator('#description').fill('Need medicine');

        // Verify values
        await expect(page.locator('#name')).toHaveValue('Ilyass');
        await expect(page.locator('#description')).toHaveValue('Need medicine');

        // Check submit button
        await expect(
            page.getByRole('button', { name: /enviar/i }),
        ).toBeVisible();
    });
});