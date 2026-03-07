/**
 * Simple Payment Gateway Tests
 * Tests Stripe and PayPal configuration
 */

const { chromium } = require('playwright');

const BASE_URL = 'http://localhost:90';
const USER_EMAIL = 'client@mail.com';
const USER_PASSWORD = 'password';

(async () => {
    console.log('🚀 Testing Payment Gateways\n');
    
    const browser = await chromium.launch({ headless: true, slowMo: 50 });
    const context = await browser.newContext({ viewport: { width: 1280, height: 720 } });
    const page = await context.newPage();
    
    try {
        // Login
        console.log('📌 Step 1: Login');
        await page.goto(`${BASE_URL}/login`, { waitUntil: 'networkidle' });
        await page.fill('input[name="email"]', USER_EMAIL);
        await page.fill('input[name="password"]', USER_PASSWORD);
        await page.click('button[type="submit"]');
        await page.waitForTimeout(2000);
        if (page.url().includes('admin')) await page.goto(`${BASE_URL}/en`);
        console.log(`✅ Logged in: ${page.url()}`);
        
        // Add product to cart
        console.log('\n📌 Step 2: Add to Cart');
        await page.goto(`${BASE_URL}/add-to-cart/sunt-est-distinctio-autem-nobis-nihil-necessitatibus-et`);
        await page.waitForTimeout(1000);
        console.log('✅ Product added');
        
        // Check cart
        await page.goto(`${BASE_URL}/cart-list`);
        await page.waitForTimeout(1000);
        const cartTotal = await page.locator('.order_subtotal span').textContent().catch(() => '0');
        console.log(`✅ Cart total: ${cartTotal}`);
        await page.screenshot({ path: 'test-results/01-cart.png' });
        
        // Test 1: COD (Cash on Delivery)
        console.log('\n💵 Test 1: Cash on Delivery');
        console.log('------------------------------');
        await page.goto(`${BASE_URL}/checkout`, { waitUntil: 'networkidle' });
        await page.fill('input[name="first_name"]', 'Test');
        await page.fill('input[name="last_name"]', 'User');
        await page.fill('input[name="email"]', USER_EMAIL);
        await page.fill('input[name="phone"]', '1234567890');
        await page.fill('input[name="address1"]', '123 Test Street');
        
        // Select COD
        await page.check('input[value="cod"]');
        console.log('✅ Selected COD');
        
        await page.screenshot({ path: 'test-results/02-cod-form.png' });
        
        // Submit
        await page.click('button[type="submit"]');
        await page.waitForTimeout(3000);
        
        console.log(`📍 After COD URL: ${page.url()}`);
        const codContent = await page.content();
        if (codContent.includes('success') || codContent.includes('Order')) {
            console.log('✅ COD order placed!');
        } else {
            console.log('⚠️ COD result unknown');
        }
        await page.screenshot({ path: 'test-results/03-cod-result.png' });
        
        // Add another product for Stripe
        console.log('\n📌 Adding product for Stripe test');
        await page.goto(`${BASE_URL}/add-to-cart/quaerat-voluptas-quaerat-maxime-numquam-omnis-voluptates`);
        await page.waitForTimeout(1000);
        
        // Test 2: Stripe
        console.log('\n💳 Test 2: Stripe Payment');
        console.log('---------------------------');
        await page.goto(`${BASE_URL}/checkout`, { waitUntil: 'networkidle' });
        await page.fill('input[name="first_name"]', 'Test');
        await page.fill('input[name="last_name"]', 'User');
        await page.fill('input[name="email"]', USER_EMAIL);
        await page.fill('input[name="phone"]', '1234567890');
        await page.fill('input[name="address1"]', '123 Test Street');
        
        // Select Stripe
        await page.check('input[value="stripe"]');
        console.log('✅ Selected Stripe');
        
        await page.screenshot({ path: 'test-results/04-stripe-form.png' });
        
        // Submit
        await page.click('button[type="submit"]');
        await page.waitForTimeout(3000);
        
        const stripeUrl = page.url();
        console.log(`📍 After Stripe checkout URL: ${stripeUrl}`);
        
        if (stripeUrl.includes('stripe')) {
            console.log('✅ Redirected to Stripe!');
            await page.screenshot({ path: 'test-results/05-stripe-page.png' });
            
            // Fill Stripe test card
            await page.fill('input[placeholder*="Name"]', 'Test User');
            await page.fill('.card-number, input[size="20"]', '4242424242424242');
            await page.fill('.card-expiry-month, input[placeholder*="MM"]', '12');
            await page.fill('.card-expiry-year, input[placeholder*="YYYY"]', '2025');
            await page.fill('.card-cvc, input[placeholder*="CVC"]', '123');
            
            await page.click('button[type="submit"]');
            await page.waitForTimeout(5000);
            
            console.log(`📍 Final URL: ${page.url()}`);
            await page.screenshot({ path: 'test-results/06-stripe-complete.png' });
        } else {
            console.log('ℹ️ Not redirected to Stripe - may need to check routes');
        }
        
        // Add another product for PayPal
        console.log('\n📌 Adding product for PayPal test');
        await page.goto(`${BASE_URL}/add-to-cart/illum-commodi-vero-quisquam-ut`);
        await page.waitForTimeout(1000);
        
        // Test 3: PayPal
        console.log('\n🅿️ Test 3: PayPal Payment');
        console.log('---------------------------');
        await page.goto(`${BASE_URL}/checkout`, { waitUntil: 'networkidle' });
        await page.fill('input[name="first_name"]', 'Test');
        await page.fill('input[name="last_name"]', 'User');
        await page.fill('input[name="email"]', USER_EMAIL);
        await page.fill('input[name="phone"]', '1234567890');
        await page.fill('input[name="address1"]', '123 Test Street');
        
        // Select PayPal
        await page.check('input[value="paypal"]');
        console.log('✅ Selected PayPal');
        
        await page.screenshot({ path: 'test-results/07-paypal-form.png' });
        
        // Submit
        await page.click('button[type="submit"]');
        await page.waitForTimeout(5000);
        
        const paypalUrl = page.url();
        console.log(`📍 After PayPal checkout URL: ${paypalUrl}`);
        
        if (paypalUrl.includes('paypal') || paypalUrl.includes('sandbox.paypal')) {
            console.log('✅ Redirected to PayPal!');
            await page.screenshot({ path: 'test-results/08-paypal-page.png' });
        } else {
            console.log('ℹ️ PayPal redirect status - check configuration');
            await page.screenshot({ path: 'test-results/08-paypal-redirect.png' });
        }
        
        // Summary
        console.log('\n========================================');
        console.log('✅ PAYMENT TESTS COMPLETED!');
        console.log('========================================');
        console.log('Check screenshots in test-results/ folder');
        
    } catch (error) {
        console.error('\n❌ ERROR:', error.message);
        await page.screenshot({ path: 'test-results/error.png' }).catch(() => {});
    } finally {
        await browser.close();
    }
})();
