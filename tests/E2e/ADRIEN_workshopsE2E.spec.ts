import { expect, test } from '@playwright/test';

test.describe('Workshops', () => {
    test('mostra el títol i el camp de cerca', async ({ page }) => {
        await page.goto('/workshops');

        await expect(page.getByText('Tallers i activitats').first()).toBeVisible({ timeout: 10000 });
        await expect(page.locator('input[type="search"]')).toBeVisible();
    });

    test('mostra missatge buit quan la cerca no troba resultats', async ({ page }) => {
        await page.goto('/workshops');

        // Type a search query that will not match any workshop
        await page.locator('input[type="search"]').fill('zzzzzzzzzzzzz');

        // Empty state message must appear
        await expect(page.getByText("No s'han trobat tallers")).toBeVisible();

        // Clear button must appear
        await expect(page.getByRole('button', { name: /Esborrar cerca/i })).toBeVisible();

        // Clicking clear resets the search and hides the empty state message
        await page.getByRole('button', { name: /Esborrar cerca/i }).click();
        await expect(page.getByText("No s'han trobat tallers")).not.toBeVisible();
    });
});
