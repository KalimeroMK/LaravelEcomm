# 🧪 API Testing Guide - E-commerce Workflow

## 📋 Overview

This guide covers comprehensive testing of the entire e-commerce API workflow, from product selection to payment completion. The test suite ensures all critical business logic functions correctly and securely.

## 🏗️ Test Architecture

### **Test Modules Created:**

1. **CartApiTest** - Cart operations (add, update, remove, view)
2. **OrderApiTest** - Order creation and management
3. **PaymentApiTest** - Payment processing and validation
4. **ProductApiTest** - Product listing, search, and filtering
5. **EcommerceWorkflowTest** - End-to-end workflow integration

## 🚀 Running the Tests

### **1. Individual Module Tests**

```bash
# Test Cart module
php artisan test Modules/Cart/Tests/Feature/CartApiTest.php

# Test Order module
php artisan test Modules/Order/Tests/Feature/OrderApiTest.php

# Test Payment module
php artisan test Modules/Billing/Tests/Feature/PaymentApiTest.php

# Test Product module
php artisan test Modules/Product/Tests/Feature/ProductApiTest.php
```

### **2. Integration Test**

```bash
# Test complete e-commerce workflow
php artisan test Modules/Core/Tests/Feature/EcommerceWorkflowTest.php
```

### **3. All API Tests**

```bash
# Run all API tests
php artisan test --filter="ApiTest"

# Run with coverage report
php artisan test --coverage --filter="ApiTest"
```

## 🔄 Complete E-commerce Workflow

### **Step 1: Product Selection**

```php
// User browses products
GET /api/products
GET /api/products?search=iPhone
GET /api/products?featured=true
GET /api/products?min_price=100&max_price=500
```

**Tests Covered:**

-   Product listing with pagination
-   Product search functionality
-   Price range filtering
-   Featured products filtering
-   Product sorting (price, date, name)

### **Step 2: Add to Cart**

```php
// User adds products to cart
POST /api/carts
{
    "product_id": 1,
    "quantity": 2,
    "user_id": 1,
    "price": 100.00,
    "amount": 200.00
}
```

**Tests Covered:**

-   Add product to cart
-   Quantity validation
-   Stock validation
-   Price calculation
-   User authorization

### **Step 3: Cart Management**

```php
// User manages cart items
GET /api/carts                    // View cart
PUT /api/carts/{id}              // Update quantity
DELETE /api/carts/{id}           // Remove item
```

**Tests Covered:**

-   View cart contents
-   Update item quantities
-   Remove items from cart
-   Cart total calculation
-   User isolation (can't see other users' carts)

### **Step 4: Order Creation**

```php
// User creates order from cart
POST /api/orders
{
    "user_id": 1,
    "sub_total": 200.00,
    "shipping_id": 1,
    "total_amount": 215.00,
    "quantity": 2,
    "payment_method": "stripe",
    "payment_status": "pending",
    "status": "pending"
}
```

**Tests Covered:**

-   Order creation from cart items
-   Total calculation (subtotal + shipping)
-   Order status management
-   User authorization
-   Cart-to-order linking

### **Step 5: Payment Processing**

```php
// User processes payment
POST /api/stripe
{
    "order_id": 1,
    "amount": 215.00,
    "currency": "usd",
    "payment_method": "stripe",
    "description": "Order payment for #12345"
}
```

**Tests Covered:**

-   Payment initiation
-   Amount validation
-   Order existence validation
-   User authorization
-   Payment status updates

## 🧪 Test Scenarios

### **Happy Path Testing**

1. **Complete Purchase Flow**

    - Browse products → Add to cart → Create order → Process payment
    - Verify all data consistency
    - Check status updates

2. **Multiple Products**
    - Add different products with varying quantities
    - Verify total calculations
    - Check shipping costs

### **Edge Case Testing**

1. **Stock Validation**

    - Try to add more than available stock
    - Verify error responses
    - Check stock updates

2. **Invalid Data**

    - Non-existent products
    - Invalid quantities
    - Missing required fields
    - Wrong payment amounts

3. **Security Testing**
    - User authorization
    - Cross-user data access prevention
    - API token validation

### **Business Logic Testing**

1. **Price Calculations**

    - Product prices × quantities
    - Shipping costs
    - Total amounts
    - Tax calculations (if applicable)

2. **Status Management**
    - Order status progression
    - Payment status updates
    - Cart status changes

## 📊 Test Data Setup

### **Required Factories**

```php
// User factory
User::factory()->create()

// Product factory
Product::factory()->create([
    'status' => 'active',
    'price' => 100.00,
    'stock' => 10
])

// Shipping factory
Shipping::factory()->create([
    'status' => 'active',
    'price' => 15.00
])

// Cart factory
Cart::factory()->create([
    'user_id' => $user->id,
    'product_id' => $product->id,
    'quantity' => 2,
    'price' => $product->price
])

// Order factory
Order::factory()->create([
    'user_id' => $user->id,
    'sub_total' => 200.00,
    'total_amount' => 215.00,
    'payment_status' => 'pending'
])
```

### **Test Database**

-   Uses `RefreshDatabase` trait
-   Fresh database for each test
-   Isolated test data
-   No cross-test contamination

## 🔍 Test Assertions

### **Database Assertions**

```php
// Verify data exists
$this->assertDatabaseHas('carts', [
    'user_id' => $user->id,
    'product_id' => $product->id
]);

// Verify data doesn't exist
$this->assertDatabaseMissing('carts', ['id' => $cart->id]);

// Verify specific values
$this->assertEquals(200.00, $order->sub_total);
```

### **API Response Assertions**

```php
// Status code
$response->assertStatus(201);

// JSON structure
$response->assertJsonStructure([
    'data' => ['id', 'title', 'price']
]);

// JSON content
$response->assertJson([
    'data' => ['title' => 'Test Product']
]);
```

## 🚨 Common Test Issues & Solutions

### **1. Authentication Issues**

```php
// Problem: User not authenticated
// Solution: Use actingAs() in setUp()
$this->actingAs($this->user);

// Problem: Missing API token
// Solution: Include Authorization header
'Authorization' => 'Bearer ' . $this->token
```

### **2. Database Issues**

```php
// Problem: Foreign key constraints
// Solution: Create related models first
$product = Product::factory()->create();
$cart = Cart::factory()->create(['product_id' => $product->id]);

// Problem: Missing required fields
// Solution: Check model fillable arrays
protected $fillable = ['product_id', 'quantity', 'user_id', 'price'];
```

### **3. API Route Issues**

```php
// Problem: Route not found
// Solution: Check route registration in module
Route::apiResource('carts', CartController::class);

// Problem: Method not allowed
// Solution: Check HTTP method (GET, POST, PUT, DELETE)
```

## 📈 Performance Testing

### **Load Testing Scenarios**

```bash
# Test with multiple concurrent users
php artisan test --parallel

# Test database performance
php artisan test --filter="PerformanceTest"

# Test memory usage
php artisan test --filter="MemoryTest"
```

### **Benchmark Testing**

```php
// Measure response times
$start = microtime(true);
$response = $this->getJson('/api/products');
$end = microtime(true);

$this->assertLessThan(0.5, $end - $start); // Should respond in <500ms
```

## 🔧 Test Configuration

### **Environment Setup**

```bash
# Test environment
APP_ENV=testing
DB_CONNECTION=sqlite
DB_DATABASE=:memory:

# Cache configuration
CACHE_DRIVER=array
QUEUE_CONNECTION=sync
```

### **Test Database**

```php
// config/database.php
'testing' => [
    'driver' => 'sqlite',
    'database' => ':memory:',
    'prefix' => '',
],
```

## 📝 Test Documentation

### **Generating Test Reports**

```bash
# HTML coverage report
php artisan test --coverage-html coverage/

# XML coverage report (for CI/CD)
php artisan test --coverage-clover coverage.xml

# Console coverage report
php artisan test --coverage-text
```

### **Test Naming Convention**

```php
// Format: action_should_result_when_condition
public function user_can_add_product_to_cart()
public function user_cannot_add_invalid_product()
public function order_calculates_totals_correctly()
```

## 🤝 Contributing to Tests

### **Adding New Tests**

1. **Follow naming convention**
2. **Use descriptive test names**
3. **Test one behavior per test**
4. **Include edge cases**
5. **Add proper assertions**

### **Test Maintenance**

1. **Update tests when API changes**
2. **Keep test data realistic**
3. **Remove obsolete tests**
4. **Maintain test performance**

## 🎯 Test Coverage Goals

### **Minimum Coverage Requirements**

-   **API Endpoints**: 100%
-   **Business Logic**: 95%
-   **Error Handling**: 90%
-   **Security**: 100%
-   **Data Validation**: 95%

### **Coverage Reports**

```bash
# Check current coverage
php artisan test --coverage-text

# Generate detailed report
php artisan test --coverage-html coverage/
```

---

**Last Updated**: January 2025  
**Version**: 1.0.0  
**Maintained By**: Development Team
