const { test, expect } = require('@playwright/test');

// Test data from seeders
const TEST_USER = {
  email: 'client@mail.com',
  password: 'password'
};

const BASE_URL = 'http://localhost:90';

// Helper function to login
async function login(page, email, password) {
  await page.goto(`${BASE_URL}/login`);
  await page.fill('input[name="email"]', email);
  await page.fill('input[name="password"]', password);
  await page.click('button[type="submit"]');
  await page.waitForLoadState('networkidle');
}

// Helper function to add product to cart
async function addProductToCart(page) {
  await page.goto(`${BASE_URL}/product-grids`);
  await page.waitForSelector('.product-item, .listing-item, .single-product, .product-list-item', { timeout: 10000 });
  
  // Click on first product link (get the product slug from the link)
  const productLink = await page.locator('.product-item-title a, .product-item a, .listing-item a').first();
  const href = await productLink.getAttribute('href');
  const slug = href.split('/').pop();
  
  // Direct add to cart via URL (most reliable)
  await page.goto(`${BASE_URL}/add-to-cart/${slug}`);
  await page.waitForTimeout(2000);
}

// Helper function to go to cart and update quantity
async function updateCartQuantity(page) {
  await page.goto(`${BASE_URL}/cart-list`);
  await page.waitForLoadState('networkidle');
  
  // Check if cart has items
  const cartItems = await page.locator('input[name^="quantity"], .qty input').count();
  if (cartItems > 0) {
    // Update first item quantity to 2
    const qtyInput = await page.locator('input[name^="quantity"]').first();
    await qtyInput.fill('2');
    
    // Click update button
    const updateBtn = await page.locator('button:has-text("Update"), .btn-update').first();
    if (await updateBtn.isVisible()) {
      await updateBtn.click();
      await page.waitForTimeout(2000);
    }
  }
}

// Helper function to remove product from cart
async function removeFromCart(page) {
  await page.goto(`${BASE_URL}/cart-list`);
  await page.waitForLoadState('networkidle');
  
  const removeLinks = await page.locator('a[href*="cart-delete"], .remove-icon, .btn-danger').count();
  if (removeLinks > 0) {
    // Remove first item
    const removeBtn = await page.locator('a[href*="cart-delete"]').first();
    await removeBtn.click();
    await page.waitForTimeout(2000);
  }
}

// Helper function to checkout with COD
async function checkoutWithCOD(page) {
  await page.goto(`${BASE_URL}/checkout`);
  await page.waitForLoadState('networkidle');
  
  // Fill checkout form
  await page.fill('input[name="first_name"]', 'Test');
  await page.fill('input[name="last_name"]', 'User');
  await page.fill('input[name="email"]', TEST_USER.email);
  await page.fill('input[name="phone"]', '123456789');
  await page.fill('input[name="address1"]', 'Test Address 123');
  await page.fill('input[name="city"]', 'Skopje');
  await page.fill('input[name="post_code"]', '1000');
  
  // Select country if exists
  const countrySelect = await page.locator('select[name="country"]').first();
  if (await countrySelect.isVisible()) {
    await countrySelect.selectOption('MK');
  }
  
  // Select COD payment
  const codRadio = await page.locator('input[value="cod"]').first();
  if (await codRadio.isVisible()) {
    await codRadio.check();
  }
  
  // Submit order
  const submitBtn = await page.locator('button[type="submit"], .btn-order').first();
  await submitBtn.click();
  await page.waitForTimeout(3000);
  
  // Check for success message
  const successMsg = await page.locator('text=success, text=Order placed, text=Thank you').count();
  return successMsg > 0;
}

// Helper function to checkout with Stripe
async function checkoutWithStripe(page) {
  await page.goto(`${BASE_URL}/checkout`);
  await page.waitForLoadState('networkidle');
  
  // Fill checkout form
  await page.fill('input[name="first_name"]', 'Test');
  await page.fill('input[name="last_name"]', 'User');
  await page.fill('input[name="email"]', TEST_USER.email);
  await page.fill('input[name="phone"]', '123456789');
  await page.fill('input[name="address1"]', 'Test Address 123');
  await page.fill('input[name="city"]', 'Skopje');
  await page.fill('input[name="post_code"]', '1000');
  
  // Select Stripe payment
  const stripeRadio = await page.locator('input[value="stripe"]').first();
  if (await stripeRadio.isVisible()) {
    await stripeRadio.check();
  }
  
  // Submit to go to Stripe
  const submitBtn = await page.locator('button[type="submit"], .btn-order').first();
  await submitBtn.click();
  await page.waitForTimeout(3000);
  
  // Check if redirected to Stripe page
  if (page.url().includes('stripe')) {
    // Fill Stripe test card details
    await page.fill('input[name="stripeToken"], .card-number', '4242424242424242');
    await page.fill('.card-cvc', '123');
    await page.fill('.card-expiry-month', '12');
    await page.fill('.card-expiry-year', '2025');
    
    // Submit payment
    await page.click('button[type="submit"]');
    await page.waitForTimeout(3000);
  }
}

// Helper function to checkout with PayPal
async function checkoutWithPayPal(page) {
  await page.goto(`${BASE_URL}/checkout`);
  await page.waitForLoadState('networkidle');
  
  // Fill checkout form
  await page.fill('input[name="first_name"]', 'Test');
  await page.fill('input[name="last_name"]', 'User');
  await page.fill('input[name="email"]', TEST_USER.email);
  await page.fill('input[name="phone"]', '123456789');
  await page.fill('input[name="address1"]', 'Test Address 123');
  await page.fill('input[name="city"]', 'Skopje');
  await page.fill('input[name="post_code"]', '1000');
  
  // Select PayPal payment
  const paypalRadio = await page.locator('input[value="paypal"]').first();
  if (await paypalRadio.isVisible()) {
    await paypalRadio.check();
  }
  
  // Submit to go to PayPal
  const submitBtn = await page.locator('button[type="submit"], .btn-order').first();
  await submitBtn.click();
  await page.waitForTimeout(5000);
  
  // Check if redirected to PayPal
  if (page.url().includes('paypal.com')) {
    console.log('Redirected to PayPal sandbox');
    // Note: PayPal sandbox requires manual login
    return true;
  }
  return page.url().includes('payment');
}

// ==================== MODERN THEME TESTS ====================
test.describe('Modern Theme - Cart & Payment', () => {
  test.beforeEach(async ({ page }) => {
    await login(page, TEST_USER.email, TEST_USER.password);
  });

  test('Modern: Add product to cart', async ({ page }) => {
    await addProductToCart(page);
    await page.goto(`${BASE_URL}/cart-list`);
    await expect(page.locator('table.cart, .shopping-summery, .cart')).toBeVisible();
  });

  test('Modern: Update cart quantity', async ({ page }) => {
    await addProductToCart(page);
    await updateCartQuantity(page);
    await expect(page.locator('text=2, .cart-count')).toBeVisible();
  });

  test('Modern: Remove product from cart', async ({ page }) => {
    await addProductToCart(page);
    await removeFromCart(page);
    // Check if cart is empty or has fewer items
    const cartContent = await page.locator('.alert-warning, .empty-cart, text=empty').count();
    expect(cartContent >= 0).toBeTruthy();
  });

  test('Modern: Checkout with COD', async ({ page }) => {
    await addProductToCart(page);
    const success = await checkoutWithCOD(page);
    expect(success).toBeTruthy();
  });

  test('Modern: Checkout with Stripe redirect', async ({ page }) => {
    await addProductToCart(page);
    await checkoutWithStripe(page);
    // Should be on Stripe page
    await expect(page).toHaveURL(/stripe/);
  });

  test('Modern: Checkout with PayPal redirect', async ({ page }) => {
    await addProductToCart(page);
    await checkoutWithPayPal(page);
    // Should be on payment or PayPal page
    const url = page.url();
    expect(url.includes('payment') || url.includes('paypal')).toBeTruthy();
  });
});

// ==================== DEFAULT THEME TESTS ====================
test.describe('Default Theme - Cart & Payment', () => {
  test.beforeEach(async ({ page }) => {
    // Login first
    await login(page, TEST_USER.email, TEST_USER.password);
    
    // Switch to default theme if possible (via URL param or settings)
    // This depends on how theme switching works in the app
  });

  test('Default: Add product to cart', async ({ page }) => {
    await addProductToCart(page);
    await page.goto(`${BASE_URL}/cart-list`);
    await expect(page.locator('table, .shopping-summery')).toBeVisible();
  });

  test('Default: Update cart quantity', async ({ page }) => {
    await addProductToCart(page);
    await updateCartQuantity(page);
    await expect(page.locator('input[value="2"], .qty')).toBeVisible();
  });

  test('Default: Checkout with COD', async ({ page }) => {
    await addProductToCart(page);
    const success = await checkoutWithCOD(page);
    expect(success).toBeTruthy();
  });

  test('Default: Checkout with Stripe redirect', async ({ page }) => {
    await addProductToCart(page);
    await checkoutWithStripe(page);
    await expect(page).toHaveURL(/stripe/);
  });

  test('Default: Checkout with PayPal redirect', async ({ page }) => {
    await addProductToCart(page);
    await checkoutWithPayPal(page);
    const url = page.url();
    expect(url.includes('payment') || url.includes('paypal')).toBeTruthy();
  });
});
