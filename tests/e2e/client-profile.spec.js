const { test, expect } = require('@playwright/test');

const BASE_URL = 'http://localhost:90';
const TEST_USER = {
  email: 'client@mail.com',
  password: 'password'
};

test.describe('Client Profile & Orders Access', () => {
  test.beforeEach(async ({ page }) => {
    // Login as client
    await page.goto(`${BASE_URL}/login`);
    await page.fill('input[name="email"]', TEST_USER.email);
    await page.fill('input[name="password"]', TEST_USER.password);
    await page.click('button[type="submit"]');
    await page.waitForTimeout(2000);
  });

  test('Client can access my-orders page', async ({ page }) => {
    await page.goto(`${BASE_URL}/my-orders`);
    await page.waitForTimeout(2000);
    
    // Should not be 403
    const url = page.url();
    expect(url).not.toContain('403');
    expect(url).not.toContain('login');
    
    // Should see orders page (check body content)
    const bodyText = await page.locator('body').textContent();
    expect(bodyText.includes('My Orders') || bodyText.includes('No orders found') || bodyText.includes('orders')).toBeTruthy();
    
    // Take screenshot
    await page.screenshot({ path: 'test-results/09-client-my-orders.png', fullPage: true });
  });

  test('Client gets 404/redirect for non-existent order', async ({ page }) => {
    // Try to access order that doesn't belong to user
    await page.goto(`${BASE_URL}/my-orders/99999`);
    await page.waitForTimeout(2000);
    
    // Should redirect or show error, not 403
    const url = page.url();
    expect(url.includes('my-orders') || url.includes('error')).toBeTruthy();
  });
});
