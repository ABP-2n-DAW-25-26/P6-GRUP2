import { expect, test } from '@playwright/test';

test('should display the assignment form', async ({ page }) => {
    await page.goto('http://localhost:8080/assignments/create');

    await expect(page.locator('#name')).toBeVisible();
});