import { test, expect } from "@playwright/test";
test("home, catalog, search and native cart work", async ({
  page,
  request,
}) => {
  const health = await request.get("/health.php");
  expect(health.status()).toBe(200);
  const response = await page.goto("/");
  expect(response.headers()["x-robots-tag"]).toContain("noindex");
  await expect(
    page.getByRole("heading", { name: "Que lo limpio se sienta bien." }),
  ).toBeVisible();
  await expect(page.locator(".brisa-product-card")).toHaveCount(4);
  await page.locator(".brisa-product-card").first().locator("h3 a").click();
  await expect(
    page.getByRole("heading", { name: "Multiusos cítrico", exact: true }),
  ).toBeVisible();
  await page.locator('.product__add-to-cart-button').click();
  await expect(page.locator("#blockcart-modal")).toBeVisible();
  await page.locator('#blockcart-modal a.btn-primary').click();
  await expect(
    page.getByText("Multiusos cítrico", { exact: true }).first(),
  ).toBeVisible();
  await page.goto("/");
  await page.getByRole("button", { name: "Abrir buscador" }).click();
  await page.locator("#brisa-search").fill("limpiacristales");
  await page.locator("#brisa-search-panel button[type=submit]").click();
  await expect(page.locator("body")).toContainText("Limpiacristales");
});

test("guest checkout records a fictitious order without payment", async ({ page }) => {
  await page.goto("/");
  await page.locator(".brisa-product-card").first().locator("h3 a").click();
  await page.locator('.product__add-to-cart-button').click();
  await page.locator('#blockcart-modal a.btn-primary').click();
  await page.locator('.js-cart-detailed-actions a.btn-primary').click();
  const customer = page.locator('#customer-form');
  await customer.locator('[name=firstname]').fill('Cliente');
  await customer.locator('[name=lastname]').fill('Prueba');
  await customer.locator('[name=email]').fill(`prueba-${Date.now()}@example.com`);
  for (const checkbox of await customer.locator('input[type=checkbox][required]').all()) {
    await checkbox.check();
  }
  await customer.locator('button[name=continue]').click();
  const address = page.locator('#checkout-addresses-step');
  await address.locator('[name=address1]').fill('Calle Ficticia 123');
  await address.locator('[name=postcode]').fill('28001');
  await address.locator('[name=city]').fill('Madrid');
  await address.locator('[name=confirm-addresses]').click();
  await expect(page.locator('#js-delivery')).toContainText('Brisa · Envío de prueba');
  await page.locator('[name=confirmDeliveryOption]').click();
  await expect(page.locator('#cart-subtotal-shipping')).toContainText('3,90');
  const payment = page.locator('[name=payment-option][data-module-name=brisademo]');
  await expect(page.locator('[name=payment-option]')).toHaveCount(1);
  await payment.check();
  for (const checkbox of await page.locator('#conditions-to-approve input[type=checkbox]').all()) {
    await checkbox.check();
  }
  await page.locator('#payment-confirmation button[type=submit]').click();
  await expect(page.locator('body')).toContainText('Pedido de prueba registrado.');
  await expect(page.locator('body')).toContainText('No se ha realizado ningún cobro');
});
test("mobile layout fits and administration requires login", async ({
  page,
}) => {
  await page.setViewportSize({ width: 390, height: 844 });
  await page.goto("/");
  await expect(page.locator(".brisa-logo").first()).toBeVisible();
  expect(
    await page.evaluate(() => document.documentElement.scrollWidth),
  ).toBeLessThanOrEqual(390);
  await page.goto("/admin-brisa/");
  await expect(page.locator("input[type=password]").first()).toBeVisible();
});
