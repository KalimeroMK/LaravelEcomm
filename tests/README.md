# Browser Tests for Laravel E-commerce Application

This directory contains comprehensive browser tests for all modules in the Laravel e-commerce application using Pest 4.

## Test Structure

### Admin Module Tests
- **AdminDashboardTest.php** - Tests admin dashboard functionality
- **Analytics Dashboard** - Tests analytics and reporting features
- **Email Analytics** - Tests email marketing analytics

### User Module Tests
- **UserAuthenticationTest.php** - Tests user registration, login, logout
- **User Profile** - Tests user profile management

### Product Module Tests
- **ProductManagementTest.php** - Tests product listing, search, filtering
- **Product Details** - Tests product detail pages
- **Category/Brand Filtering** - Tests product filtering by category and brand

### Cart Module Tests
- **CartManagementTest.php** - Tests cart functionality
- **Add/Remove Items** - Tests adding and removing products from cart
- **Quantity Updates** - Tests cart item quantity management

### Order Module Tests
- **OrderManagementTest.php** - Tests order creation and management
- **Order History** - Tests order tracking and status updates
- **Admin Order Management** - Tests admin order processing

### Newsletter Module Tests
- **NewsletterManagementTest.php** - Tests newsletter subscription
- **Email Analytics** - Tests newsletter analytics and reporting
- **Export Functionality** - Tests data export features

### Category Module Tests
- **CategoryManagementTest.php** - Tests category CRUD operations
- **Category Display** - Tests category pages and product filtering

### Brand Module Tests
- **BrandManagementTest.php** - Tests brand CRUD operations
- **Brand Display** - Tests brand pages and product filtering

### Coupon Module Tests
- **CouponManagementTest.php** - Tests coupon creation and validation
- **Coupon Application** - Tests coupon usage and validation
- **Expiration Handling** - Tests expired coupon handling

### Bundle Module Tests
- **BundleManagementTest.php** - Tests product bundle functionality
- **Bundle Creation** - Tests bundle creation and management
- **Bundle Display** - Tests bundle detail pages

### Frontend Module Tests
- **FrontendTest.php** - Tests frontend pages and functionality
- **Homepage** - Tests homepage content and layout
- **Search** - Tests search functionality
- **Blog** - Tests blog pages and posts

### Role & Permission Tests
- **RolePermissionTest.php** - Tests role and permission management
- **Access Control** - Tests protected route access
- **Permission Assignment** - Tests role and permission assignment

### Settings Module Tests
- **SettingsManagementTest.php** - Tests application settings
- **General Settings** - Tests general configuration
- **Payment Settings** - Tests payment configuration
- **Email Settings** - Tests email configuration

### Billing Module Tests
- **BillingManagementTest.php** - Tests billing and invoicing
- **Invoice Management** - Tests invoice creation and management
- **Payment Processing** - Tests payment handling

### Shipping Module Tests
- **ShippingManagementTest.php** - Tests shipping methods and zones
- **Shipping Calculation** - Tests shipping cost calculation
- **Order Tracking** - Tests order tracking functionality

### Message Module Tests
- **MessageManagementTest.php** - Tests contact messages and communication
- **Message Handling** - Tests message processing and replies

### Banner Module Tests
- **BannerManagementTest.php** - Tests banner management
- **Banner Display** - Tests banner positioning and display

### Post Module Tests
- **PostManagementTest.php** - Tests blog post management
- **Post Display** - Tests blog post display and filtering

### Tenant Module Tests
- **TenantManagementTest.php** - Tests multi-tenant functionality
- **Tenant Management** - Tests tenant creation and management

### Google2FA Module Tests
- **TwoFactorAuthenticationTest.php** - Tests two-factor authentication
- **2FA Setup** - Tests 2FA enablement and configuration
- **Recovery Codes** - Tests recovery code functionality

### Core Module Tests
- **CoreFunctionalityTest.php** - Tests core application functionality
- **System Management** - Tests system administration features
- **Cache Management** - Tests cache operations
- **Maintenance Mode** - Tests maintenance mode functionality

### All Modules Test
- **AllModulesTest.php** - Comprehensive test covering all modules
- **Route Testing** - Tests all module routes
- **API Testing** - Tests API endpoints
- **Authentication Testing** - Tests authentication across modules

## Running Tests

### Run All Tests
```bash
docker-compose exec app ./vendor/bin/pest
```

### Run Specific Test Suite
```bash
docker-compose exec app ./vendor/bin/pest tests/Feature/Admin
docker-compose exec app ./vendor/bin/pest tests/Feature/User
docker-compose exec app ./vendor/bin/pest tests/Feature/Product
```

### Run Specific Test File
```bash
docker-compose exec app ./vendor/bin/pest tests/Feature/Admin/Browser/AdminDashboardTest.php
```

### Run Tests with Coverage
```bash
docker-compose exec app ./vendor/bin/pest --coverage
```

## Test Configuration

- **phpunit.xml** - PHPUnit configuration
- **Pest.php** - Pest configuration
- **TestCase.php** - Base test case class
- **CreatesApplication.php** - Application creation trait

## Test Database

Tests use an in-memory SQLite database for fast execution. The database is refreshed between tests using the `RefreshDatabase` trait.

## Test Data

Tests use Laravel factories to create test data. Factories are located in the respective module directories.

## Browser Testing

These tests simulate browser interactions using Laravel's HTTP testing capabilities. They test:

- Route accessibility
- Form submissions
- Authentication flows
- Data validation
- Error handling
- Response content

## Best Practices

1. **Isolation** - Each test is independent and doesn't affect others
2. **Cleanup** - Database is refreshed between tests
3. **Realistic Data** - Tests use realistic test data
4. **Comprehensive Coverage** - Tests cover all major functionality
5. **Error Scenarios** - Tests include error handling scenarios
6. **Authentication** - Tests cover both authenticated and guest access
7. **Authorization** - Tests verify proper access control
8. **Data Validation** - Tests verify form validation and data integrity
9. **API Testing** - Tests cover API endpoints and responses
10. **Integration** - Tests verify module integration and communication

## Maintenance

- Update tests when adding new features
- Ensure tests pass before deploying
- Add tests for bug fixes
- Review test coverage regularly
- Update test data as needed
- Keep tests simple and focused
- Use descriptive test names
- Group related tests logically
