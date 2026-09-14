import {test, expect} from '@playwright/test';
test('home, catalog, search and native cart work', async ({page, request}) => {
  const health = await request.get('/health.php');
  expect(health.status()).toBe(200);
  const response = await page.goto('/');
  expect(response.headers()['x-robots-tag']).toContain('noindex');
  await expect(page.getByRole('heading', {name:'Que lo limpio se sienta bien.'})).toBeVisible();
  await expect(page.locator('.brisa-product-card')).toHaveCount(4);
  await page.locator('.brisa-product-card').first().locator('h3 a').click();
  await expect(page.getByRole('heading', {name:'Multiusos cítrico', exact:true})).toBeVisible();
  await page.locator('[data-button-action="add-to-cart"]').click();
  await expect(page.locator('#blockcart-modal')).toBeVisible();
  await page.goto('/carrito?action=show');
  await expect(page.getByText('Multiusos cítrico', {exact:true}).first()).toBeVisible();
  await page.goto('/');
  await page.getByRole('button', {name:'Abrir buscador'}).click();
  await page.locator('#brisa-search').fill('limpiacristales');
  await page.locator('#brisa-search-panel button[type=submit]').click();
  await expect(page.locator('body')).toContainText('Limpiacristales');
});
test('mobile layout fits and administration requires login', async ({page}) => {
  await page.setViewportSize({width:390,height:844});
  await page.goto('/');
  await expect(page.locator('.brisa-logo').first()).toBeVisible();
  expect(await page.evaluate(() => document.documentElement.scrollWidth)).toBeLessThanOrEqual(390);
  await page.goto('/admin-brisa/');
  await expect(page.locator('input[type=password]').first()).toBeVisible();
});
