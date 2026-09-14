import { test, expect } from '@playwright/test';
import { existsSync, readFileSync } from 'node:fs';
import { parseEnv } from 'node:util';

const local = existsSync('.env') ? parseEnv(readFileSync('.env', 'utf8')) : {};
const email = process.env.ADMIN_MAIL || local.ADMIN_MAIL;
const password = process.env.ADMIN_PASSWD || local.ADMIN_PASSWD;

// Authentication credentials must never enter browser trace or screenshot artifacts.
test.use({ trace: 'off', screenshot: 'off', video: 'off' });
test('administrator can access the real management panel', async ({ page }) => {
  test.skip(!email || !password, 'Provide administrator credentials through the environment');
  await page.goto('/admin-brisa/');
  const form = page.locator('form').filter({ has: page.locator('input[type=password]') }).first();
  await form.locator('input[type=email]').fill(email);
  await form.locator('input[type=password]').fill(password);
  await form.locator('button[type=submit]').click();
  await expect(page.locator('.main-menu')).toBeVisible({ timeout: 30000 });
});
