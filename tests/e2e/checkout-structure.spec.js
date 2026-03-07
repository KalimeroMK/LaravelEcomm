const { test, expect } = require('@playwright/test');

const BASE_URL = 'http://localhost:90';
const TEST_USER = {
  email: 'client@mail.com',
  password: 'password'
};

test.describe('Checkout Page Structure Tests', () => {
  test.beforeEach(async ({ page }) => {
    // Login before each test and verify
    await page.goto(`${BASE_URL}/login`);
    await page.fill('input[name="email"]', TEST_USER.email);
    await page.fill('input[name="password"]', TEST_USER.password);
    await page.click('button[type="submit"]');
    await page.waitForLoadState('networkidle');
    
    // Wait to be redirected (either to home or intended page)
    await page.waitForTimeout(2000);
  });

  test('Checkout page has all payment options', async ({ page }) => {
    // After login, add a product to cart
    await page.goto(`${BASE_URL}/product-grids`);
    await page.waitForSelector('.product-item-title a', { timeout: 10000 });
    
    // Get first product link and add to cart
    const productLink = await page.locator('.product-item-title a').first();
    const href = await productLink.getAttribute('href');
    const slug = href.split('/').pop();
    await page.goto(`${BASE_URL}/add-to-cart/${slug}`);
    await page.waitForTimeout(2000);
    
    // Now go to checkout
    await page.goto(`${BASE_URL}/checkout`);
    await page.waitForLoadState('networkidle');
    
    // Check if we're on checkout page (not redirected to login)
    const currentUrl = page.url();
    expect(currentUrl).toContain('checkout');
    
    // Check for form fields
    await expect(page.locator('input[name="first_name"]')).toBeVisible();
    await expect(page.locator('input[name="last_name"]')).toBeVisible();
    await expect(page.locator('input[name="email"]')).toBeVisible();
    await expect(page.locator('input[name="phone"]')).toBeVisible();
    await expect(page.locator('input[name="address1"]')).toBeVisible();
    await expect(page.locator('input[name="city"]')).toBeVisible();
    await expect(page.locator('input[name="post_code"]')).toBeVisible();
    
    // Check for payment method options
    await expect(page.locator('input[value="cod"]')).toBeVisible();
    await expect(page.locator('input[value="stripe"]')).toBeVisible();
    await expect(page.locator('input[value="paypal"]')).toBeVisible();
    
    // Check for submit button in checkout form (form has action with cart/order)
    await expect(page.locator('form[action*="cart/order"] button[type="submit"]').first()).toBeVisible();
  });

  test('Can select Stripe payment', async ({ page }) => {
    await page.goto(`${BASE_URL}/checkout`);
    await page.waitForLoadState('networkidle');
    
    // Fill required fields
    await page.fill('input[name="first_name"]', 'Test');
    await page.fill('input[name="last_name"]', 'User');
    await page.fill('input[name="email"]', TEST_USER.email);
    await page.fill('input[name="phone"]', '123456789');
    await page.fill('input[name="address1"]', 'Test Address');
    await page.fill('input[name="city"]', 'Skopje');
    await page.fill('input[name="post_code"]', '1000');
    
    // Select Stripe
    await page.check('input[value="stripe"]');
    
    // Verify it's checked
    const isChecked = await page.locator('input[value="stripe"]').isChecked();
    expect(isChecked).toBeTruthy();
  });

  test('Can select PayPal payment', async ({ page }) => {
    await page.goto(`${BASE_URL}/checkout`);
    await page.waitForLoadState('networkidle');
    
    // Fill required fields
    await page.fill('input[name="first_name"]', 'Test');
    await page.fill('input[name="last_name"]', 'User');
    await page.fill('input[name="email"]', TEST_USER.email);
    await page.fill('input[name="phone"]', '123456789');
    await page.fill('input[name="address1"]', 'Test Address');
    await page.fill('input[name="city"]', 'Skopje');
    await page.fill('input[name="post_code"]', '1000');
    
    // Select PayPal
    await page.check('input[value="paypal"]');
    
    // Verify it's checked
    const isChecked = await page.locator('input[value="paypal"]').isChecked();
    expect(isChecked).toBeTruthy();
  });

  test('Can select COD payment', async ({ page }) => {
    await page.goto(`${BASE_URL}/checkout`);
    await page.waitForLoadState('networkidle');
    
    // Fill required fields
    await page.fill('input[name="first_name"]', 'Test');
    await page.fill('input[name="last_name"]', 'User');
    await page.fill('input[name="email"]', TEST_USER.email);
    await page.fill('input[name="phone"]', '123456789');
    await page.fill('input[name="address1"]', 'Test Address');
    await page.fill('input[name="city"]', 'Skopje');
    await page.fill('input[name="post_code"]', '1000');
    
    // Select COD (default)
    await page.check('input[value="cod"]');
    
    // Verify it's checked
    const isChecked = await page.locator('input[value="cod"]').isChecked();
    expect(isChecked).toBeTruthy();
  });
});

test.describe('Stripe Payment Page', () => {
  test('Stripe page loads with correct elements', async ({ page }) => {
    await page.goto(`${BASE_URL}/stripe/123`);
    await page.waitForLoadState('networkidle');
    
    // Check for Stripe form elements
    await expect(page.locator('input.card-number, input[name="stripeToken"]')).toBeVisible();
    await expect(page.locator('input.card-cvc')).toBeVisible();
    await expect(page.locator('input.card-expiry-month')).toBeVisible();
    await expect(page.locator('input.card-expiry-year')).toBeVisible();
    await expect(page.locator('button[type="submit"]')).toBeVisible();
    
    // Check for Stripe key configuration
    const form = await page.locator('#payment-form');
    const stripeKey = await form.getAttribute('data-stripe-publishable-key');
    expect(stripeKey).toContain('pk_test');
  });
});

test.describe('Payment Routes', () => {
  test('PayPal payment route exists', async ({ page }) => {
    // This should redirect to PayPal or show error (not 404)
    const response = await page.goto(`${BASE_URL}/payment`);
    expect(response.status()).not.toBe(404);
  });
  
  test('Stripe payment route exists', async ({ page }) => {
    const response = await page.goto(`${BASE_URL}/stripe/123`);
    expect(response.status()).toBe(200);
  });
});
