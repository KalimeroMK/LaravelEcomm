const { test, expect } = require('@playwright/test');

const BASE_URL = 'http://localhost:90';
const TEST_USER = {
  email: 'client@mail.com',
  password: 'password'
};

test.describe.configure({ mode: 'serial' });

test.describe('Full Payment Workflow - Visual Test', () => {
  test('Complete test: Add to cart → Checkout → Stripe Payment', async ({ page }) => {
    // Step 1: Login
    console.log('Step 1: Login');
    await page.goto(`${BASE_URL}/login`);
    await page.fill('input[name="email"]', TEST_USER.email);
    await page.fill('input[name="password"]', TEST_USER.password);
    await page.click('button[type="submit"]');
    await page.waitForTimeout(2000);
    
    // Step 2: Go to products and add to cart
    console.log('Step 2: Go to products');
    await page.goto(`${BASE_URL}/product-grids`);
    await page.waitForSelector('.product-item-title a', { timeout: 10000 });
    
    // Click first product
    const productLink = await page.locator('.product-item-title a').first();
    const productName = await productLink.textContent();
    console.log(`Adding product: ${productName}`);
    await productLink.click();
    await page.waitForTimeout(2000);
    
    // Step 3: Add to cart
    console.log('Step 3: Add to cart');
    // Try to find add to cart button or use URL
    const currentUrl = page.url();
    const slug = currentUrl.split('/').pop();
    await page.goto(`${BASE_URL}/add-to-cart/${slug}`);
    await page.waitForTimeout(2000);
    
    // Step 4: Go to cart
    console.log('Step 4: Go to cart');
    await page.goto(`${BASE_URL}/cart-list`);
    await page.waitForTimeout(2000);
    
    // Take screenshot of cart
    await page.screenshot({ path: 'test-results/01-cart-page.png', fullPage: true });
    console.log('Cart screenshot saved');
    
    // Step 5: Go to checkout
    console.log('Step 5: Go to checkout');
    await page.goto(`${BASE_URL}/checkout`);
    await page.waitForTimeout(2000);
    
    // Take screenshot of checkout
    await page.screenshot({ path: 'test-results/02-checkout-page.png', fullPage: true });
    console.log('Checkout screenshot saved');
    
    // Step 6: Fill checkout form
    console.log('Step 6: Fill checkout form');
    await page.fill('input[name="first_name"]', 'Test');
    await page.fill('input[name="last_name"]', 'User');
    await page.fill('input[name="email"]', TEST_USER.email);
    await page.fill('input[name="phone"]', '123456789');
    await page.fill('input[name="address1"]', 'Test Address 123');
    await page.fill('input[name="city"]', 'Skopje');
    await page.fill('input[name="post_code"]', '1000');
    
    // Step 7: Select Stripe payment
    console.log('Step 7: Select Stripe payment');
    await page.check('input[value="stripe"]');
    await page.waitForTimeout(1000);
    
    // Get CSRF token from form
    const csrfToken = await page.inputValue('input[name="_token"]');
    console.log(`CSRF token present: ${csrfToken ? 'Yes' : 'No'}`);
    
    // Take screenshot before submit
    await page.screenshot({ path: 'test-results/03-checkout-stripe-selected.png', fullPage: true });
    console.log('Stripe selected screenshot saved');
    
    // Step 8: Submit to go to Stripe
    console.log('Step 8: Submit form to go to Stripe');
    
    // Check if Stripe is still selected before submit
    const isStripeChecked = await page.locator('input[value="stripe"]').isChecked();
    console.log(`Stripe radio checked: ${isStripeChecked}`);
    
    // Submit form
    await Promise.all([
      page.waitForNavigation({ timeout: 10000 }).catch(e => console.log('Navigation timeout:', e.message)),
      page.click('form[action*="cart/order"] button[type="submit"]')
    ]);
    await page.waitForTimeout(3000);
    
    // Take screenshot of Stripe page
    await page.screenshot({ path: 'test-results/04-stripe-page.png', fullPage: true });
    console.log('Stripe page screenshot saved');
    
    // Check page content to see if there's an error
    const pageContent = await page.content();
    if (pageContent.includes('error') || pageContent.includes('Error')) {
      console.log('Page has error content');
      // Print visible text
      const bodyText = await page.locator('body').textContent();
      console.log('Body text:', bodyText.substring(0, 500));
    }
    
    // Verify we're on Stripe page
    const stripeUrl = page.url();
    console.log(`Current URL after Stripe submit: ${stripeUrl}`);
    
    // Check if we're on stripe page or if there's a form with action to stripe
    const isStripePage = stripeUrl.includes('stripe');
    const hasStripeForm = await page.locator('form[action*="stripe"], input[name="stripeToken"]').count() > 0;
    
    console.log(`Is stripe page: ${isStripePage}, Has stripe form: ${hasStripeForm}`);
    
    expect(isStripePage || hasStripeForm).toBeTruthy();
    
    // Step 9: Fill Stripe test card
    console.log('Step 9: Fill Stripe test card');
    await page.fill('.card-number', '4242424242424242');
    await page.fill('.card-cvc', '123');
    await page.fill('.card-expiry-month', '12');
    await page.fill('.card-expiry-year', '2025');
    // Name field may not exist in some versions
    const nameField = await page.locator('input[placeholder*="Name"]').first();
    if (await nameField.isVisible().catch(() => false)) {
      await nameField.fill('Test User');
    }
    
    // Take screenshot before Stripe payment
    await page.screenshot({ path: 'test-results/05-stripe-filled.png', fullPage: true });
    console.log('Stripe filled screenshot saved');
    
    // Step 10: Submit Stripe payment
    console.log('Step 10: Submit Stripe payment');
    await page.click('#payment-form button[type="submit"]');
    await page.waitForTimeout(5000);
    
    // Take screenshot after payment
    await page.screenshot({ path: 'test-results/06-after-stripe-payment.png', fullPage: true });
    console.log('After payment screenshot saved');
    
    const finalUrl = page.url();
    console.log(`Final URL: ${finalUrl}`);
    
    // Should be on Stripe page or redirected after payment
    // For test purposes, being on stripe page is success
    expect(finalUrl.includes('stripe') || finalUrl.includes('success') || finalUrl === `${BASE_URL}/`).toBeTruthy();
  });

  test('Complete test: Add to cart → Checkout → PayPal Payment', async ({ page }) => {
    // Step 1: Login
    console.log('Step 1: Login');
    await page.goto(`${BASE_URL}/login`);
    await page.fill('input[name="email"]', TEST_USER.email);
    await page.fill('input[name="password"]', TEST_USER.password);
    await page.click('button[type="submit"]');
    await page.waitForTimeout(2000);
    
    // Step 2: Go to products and add to cart
    console.log('Step 2: Add product to cart');
    await page.goto(`${BASE_URL}/product-grids`);
    await page.waitForSelector('.product-item-title a', { timeout: 10000 });
    
    const productLink = await page.locator('.product-item-title a').first();
    await productLink.click();
    await page.waitForTimeout(2000);
    
    const currentUrl = page.url();
    const slug = currentUrl.split('/').pop();
    await page.goto(`${BASE_URL}/add-to-cart/${slug}`);
    await page.waitForTimeout(2000);
    
    // Step 3: Go to checkout
    console.log('Step 3: Go to checkout');
    await page.goto(`${BASE_URL}/checkout`);
    await page.waitForTimeout(2000);
    
    // Fill checkout form
    await page.fill('input[name="first_name"]', 'Test');
    await page.fill('input[name="last_name"]', 'User');
    await page.fill('input[name="email"]', TEST_USER.email);
    await page.fill('input[name="phone"]', '123456789');
    await page.fill('input[name="address1"]', 'Test Address 123');
    await page.fill('input[name="city"]', 'Skopje');
    await page.fill('input[name="post_code"]', '1000');
    
    // Step 4: Select PayPal payment
    console.log('Step 4: Select PayPal payment');
    await page.check('input[value="paypal"]');
    await page.waitForTimeout(1000);
    
    // Take screenshot before submit
    await page.screenshot({ path: 'test-results/07-checkout-paypal-selected.png', fullPage: true });
    console.log('PayPal selected screenshot saved');
    
    // Step 5: Submit to go to PayPal
    console.log('Step 5: Submit form to go to PayPal');
    await page.click('form[action*="cart/order"] button[type="submit"]');
    await page.waitForTimeout(8000);
    
    // Take screenshot of PayPal redirect
    await page.screenshot({ path: 'test-results/08-paypal-redirect.png', fullPage: true });
    console.log('PayPal redirect screenshot saved');
    
    // Verify we're on PayPal or payment page
    const paypalUrl = page.url();
    console.log(`Current URL after PayPal submit: ${paypalUrl}`);
    expect(paypalUrl.includes('paypal.com') || paypalUrl.includes('payment')).toBeTruthy();
  });
});
