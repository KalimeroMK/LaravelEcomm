# Advanced E-commerce Platform in Laravel 12

### 🌐 Demo: https://e-comm.mk

---

## 📑 Table of Contents

- [🚀 Quick Start](#-quick-start)
- [✨ Features Overview](#-features-overview)
- [📸 Screenshots](#-screenshots)
- [📚 Documentation](#-documentation)
- [🤝 Contributing](#-contributing)
- [📄 License](#-license)

---

## 🚀 Quick Start

### Option 1: Docker (Recommended)

```bash
# 1. Clone and start
git clone https://github.com/KalimeroMK/LaravelEcomm.git
cd LaravelEcomm
docker-compose up -d

# 2. Install dependencies
docker exec e_comm_app composer install

# 3. Setup environment
cp .env.example .env
docker exec e_comm_app php artisan key:generate

# 4. Configure database in .env
DB_HOST=db
DB_DATABASE=homestead
DB_USERNAME=homestead
DB_PASSWORD=secret

# 5. Run migrations and seeders
docker exec e_comm_app php artisan migrate:fresh --seed

# 6. Create storage link
docker exec e_comm_app php artisan storage:link

# 7. Access the application
# Frontend: http://localhost:90
# Admin:    http://localhost:90/admin
# API:      http://localhost:90/api/v1
```

### Option 2: Local Development

```bash
# 1. Clone and install
git clone https://github.com/KalimeroMK/LaravelEcomm.git
cd LaravelEcomm
composer install
cp .env.example .env

# 2. Configure environment
php artisan key:generate
# Edit .env with your database credentials

# 3. Setup database
php artisan migrate:fresh --seed

# 4. Install frontend assets
npm install && npm run build

# 5. Create storage link
php artisan storage:link

# 6. Start server
php artisan serve
# Visit: http://localhost:8000
```

### Default Credentials

| Role | URL | Email | Password |
|------|-----|-------|----------|
| **Admin** | `/admin` | `superadmin@mail.com` | `password` |
| **Client** | `/login` | `client@mail.com` | `password` |

---

## ✨ Features Overview

### 🎨 Frontend Features

#### Multi-Theme System
- **Two Complete Themes**: Default theme (classic e-commerce) & Modern theme (contemporary design)
- **Easy Theme Switching**: Change active theme via admin settings (no code changes)
- **Theme Assets**: Organized CSS, JS, images per theme (`public/frontend/themes/{theme}/`)
- **View Fallback**: Automatic fallback to default theme if view missing in active theme
- **32+ Theme Views**: Complete page coverage (homepage, products, cart, checkout, user pages)

#### Internationalization (i18n)
- **URL Prefix Strategy**: `/en/`, `/mk/`, `/de/`, `/sq/` for language switching
- **Auto Locale Detection**: Detects browser language and redirects automatically
- **Database-Driven Languages**: Add/remove languages via admin without code changes
- **Translation Management**: Admin UI for managing translations
- **Model Translations**: Product, Category, Page, Post models support translations via `HasTranslations` trait
- **Automatic Fallback**: Falls back to default language if translation missing
- **RTL Support**: Right-to-left language support built-in

#### GeoLocalization & Currency
- **GeoIP Detection**: Auto-detect user country from IP address
- **Automatic Currency**: Detects and sets currency based on country
- **Real-Time Exchange Rates**: 20+ currencies with live rates
- **Currency Conversion API**: Convert prices between currencies on-the-fly
- **EU Detection**: GDPR compliance helpers for EU countries
- **Timezone Detection**: Auto-set timezone based on location

#### Product Catalog
- **Product Types**: Simple, Configurable, Bundle, Downloadable products
- **Advanced Attributes**: Bagisto-style attribute system (color, size, material swatches)
- **Visual Swatches**: Color swatches, button swatches, image swatches
- **Configurable Products**: Auto-generate variants from attribute combinations (e.g., T-Shirt: Red × S, M, L)
- **Layered Navigation**: AJAX-powered filtering with real-time product counts
- **Product Variants**: Manage stock, price, images per variant
- **Product Reviews**: Star ratings, review text, helpfulness voting
- **Wishlist**: Save products for later, share wishlist
- **Recently Viewed**: Track and display browsing history
- **Product Comparison**: Compare up to 4 products side-by-side
- **Stock Management**: Track inventory, low stock alerts, out-of-stock handling
- **Digital Downloads**: Support for downloadable products with secure links

#### Shopping Experience
- **Shopping Cart**: AJAX add/remove, quantity updates, mini-cart dropdown
- **Saved Carts**: Save cart for later, restore cart
- **Guest Checkout**: Checkout without registration
- **Multiple Addresses**: Save multiple shipping/billing addresses
- **Address Book**: Default addresses, address management
- **Order Tracking**: Track order status, shipping information
- **Order History**: View all orders, reorder previous orders
- **Coupon System**: Apply coupons in cart, see discount breakdown
- **Shipping Estimation**: Calculate shipping costs before checkout

#### Search & Discovery
- **Elasticsearch Integration**: Full-text search, fuzzy matching, suggestions
- **Advanced Filters**: Filter by price, brand, attributes, ratings
- **Auto-Complete**: Search suggestions as you type
- **Search Analytics**: Track popular searches, no-results queries
- **Category Navigation**: Multi-level categories, category tree
- **Breadcrumbs**: Navigation trail for easy back-tracking
- **Related Products**: AI-powered or manual related products
- **Up-Sells & Cross-Sells**: Product recommendations

#### Content Management (Frontend)
- **Blog System**: Categories, tags, featured images, SEO meta
- **CMS Pages**: Create custom pages (About, Contact, FAQ) via admin
- **Banners**: Homepage banners, promotional banners with click tracking
- **Menus**: Dynamic menu management, nested menus
- **Newsletter**: Subscribe form, double opt-in confirmation

#### User Account Features
- **User Dashboard**: Overview of orders, addresses, account info
- **Profile Management**: Update name, email, password, avatar
- **Order Management**: View orders, download invoices, track shipments
- **Address Book**: Multiple addresses, default shipping/billing
- **Wishlist Management**: Add/remove, move to cart
- **Review Management**: Edit/delete own reviews
- **Comment Management**: Manage blog comments
- **Social Login**: Login with Facebook, Google, Twitter, GitHub

#### Payment & Checkout
- **Payment Gateways**:
  - **Stripe**: Credit card payments (tested with Stripe Elements)
  - **PayPal**: Express checkout, sandbox support
  - **Cash on Delivery (COD)**: Pay on delivery option
- **Secure Checkout**: SSL support, PCI compliance helpers
- **Multi-Step Checkout**: Shipping, payment, review steps
- **Order Confirmation**: Email confirmation, PDF invoice
- **Failed Payment Handling**: Retry payment, cancel order

#### Marketing & Engagement
- **Product Sharing**: Share on social media (Facebook, Twitter, Pinterest)
- **Social Login**: One-click registration/login
- **Newsletter Subscription**: Footer signup, popup option
- **Abandoned Cart Recovery**: Automated email reminders
- **Product Recommendations**: AI-powered suggestions based on behavior
- **Promotional Banners**: Targeted banners based on user segment

#### SEO Features
- **Dynamic Meta Tags**: Auto-generated title, description per page
- **Open Graph**: Facebook sharing optimization
- **Twitter Cards**: Twitter sharing optimization
- **Structured Data**: Schema.org markup (Product, Organization, BreadcrumbList)
- **XML Sitemaps**: Auto-generated for products, categories, posts
- **SEO-Friendly URLs**: Slug-based URLs (`/product/nike-air-max`)
- **Canonical URLs**: Prevent duplicate content issues
- **Robots.txt**: Auto-generated with sitemap reference
- **Alt Tags**: Image SEO with automatic alt text

---

### ⚙️ Admin Dashboard

#### Dashboard & Analytics
- **Overview Dashboard**: Sales today, orders, users, revenue charts
- **Interactive Charts**: Chart.js integration (line, bar, pie charts)
- **Sales Reports**: Daily, weekly, monthly, yearly sales data
- **Revenue Tracking**: Total revenue, average order value
- **User Analytics**: New users, active users, user growth
- **Product Analytics**: Best sellers, low stock, views/clicks
- **Order Analytics**: Order statuses, payment methods, shipping methods
- **Export Reports**: Download reports as CSV, Excel, PDF
- **Real-time Updates**: Live data refresh for key metrics

#### Product Management
- **Product Grid**: Advanced filtering, sorting, bulk actions
- **Product Creation**: Wizard for creating products step-by-step
- **Attribute Management**: Create attributes, options, families
- **Variant Management**: Manage product variants (stock, price, images)
- **Media Manager**: Upload images, videos, documents (Unisharp File Manager)
- **Category Assignment**: Multi-category products, primary category
- **SEO Management**: Meta title, description, keywords per product
- **Stock Management**: Quantity, low stock threshold, backorders
- **Pricing**: Base price, sale price, cost price, tier pricing
- **Product Reviews**: Approve/reject reviews, reply to reviews
- **Product Import/Export**: Bulk import via CSV

#### Order Management
- **Order Grid**: Filter by status, date, customer, payment
- **Order Lifecycle**: 
  - Statuses: Pending, Processing, On Hold, Shipped, Delivered, Cancelled, Refunded, Failed
  - Payment Statuses: Pending, Paid, Failed, Refunded
- **Order Details**: Products, customer info, shipping, payment
- **Invoice Generation**: PDF invoices with customizable template
- **Shipment Tracking**: Add tracking numbers, shipping carriers
- **Refund Processing**: Partial/full refunds, store credit
- **Order Notes**: Internal notes, customer-visible notes
- **Print Order**: Print-friendly order page
- **Resend Email**: Resend order confirmation, invoice

#### Customer Management
- **Customer Grid**: Search, filter, export customers
- **Customer Profile**: Orders, addresses, activity history
- **Customer Groups**: Create groups (VIP, Wholesale, etc.)
- **Customer Segmentation**: Based on purchase history, location
- **Impersonation**: Login as customer to help troubleshoot
- **Address Management**: View/edit customer addresses

#### Content Management
- **Blog Posts**: Create, edit, schedule posts
- **Categories**: Hierarchical categories, SEO settings
- **Tags**: Tag management, tag cloud
- **Pages**: CMS pages (About, Contact, Terms, etc.)
- **Banners**: Homepage sliders, promotional banners
  - Click tracking
  - Impression tracking
  - Start/end dates
  - Target URLs
- **Media Library**: Central file management, image optimization
- **Menu Builder**: Drag-drop menu creation

#### Marketing Tools
- **Email Campaigns**: Create and send newsletter campaigns
- **Email Templates**: Customizable templates for all emails
- **Newsletter Management**: Subscribers, segments, send history
- **Abandoned Cart Emails**: 3-email sequence automation
  - Email 1: 1 hour after abandonment
  - Email 2: 24 hours after abandonment  
  - Email 3: 72 hours after abandonment
- **Coupon Management**:
  - Types: Percentage, Fixed amount, Free shipping
  - Restrictions: Minimum purchase, category restrictions, user restrictions
  - Usage limits: Per coupon, per user
  - Expiry dates
- **Promotions**: Catalog price rules, cart price rules

#### Email Marketing & Automation
- **Campaign Analytics**: Open rates, click rates, bounce rates, unsubscribes
- **Email Templates**: HTML templates with dynamic variables
- **Automated Emails**: Welcome series, birthday emails, re-engagement
- **Email Scheduling**: Schedule campaigns for future dates
- **A/B Testing**: Test different subject lines, content
- **Segmentation**: Target specific customer groups

#### User & Role Management
- **Admin Users**: Create/edit admin accounts
- **Roles**: Define roles (Super Admin, Admin, Editor, etc.)
- **Permissions**: Granular permissions per role
- **Permission Matrix**: Visual permission assignment
- **Activity Log**: Track admin actions, login history

#### System Configuration
- **General Settings**: Store name, logo, address, contact info
- **Currency Settings**: Default currency, exchange rates, formatting
- **Language Settings**: Active languages, default language
- **Email Settings**: SMTP configuration, email templates
- **Payment Settings**: Enable/disable gateways, sandbox mode
- **Shipping Settings**: Methods, zones, rates
- **Tax Settings**: Tax rates, tax classes, display options
- **SEO Settings**: Default meta tags, sitemap settings
- **Social Settings**: Social media links, API keys
- **Maintenance Mode**: Enable/disable with custom message

#### Reporting Module
- **8 Report Types**: Sales, Products, Customers, Inventory, Orders, Coupons, Revenue, Tax
- **Scheduled Reports**: Auto-generate and email reports
- **Custom Date Ranges**: Flexible reporting periods
- **Export Formats**: CSV, Excel, PDF
- **Report History**: Track generated reports
- **Visual Charts**: Graphical representation of data

---

### 🔐 Security & Performance

#### Security Features
- **Two-Factor Authentication (2FA)**: Google Authenticator integration
- **Role-Based Access Control (RBAC)**: Granular permissions
- **IP Blocking**: Block specific IP addresses or ranges
- **Login Attempt Limiting**: Prevent brute force attacks
- **Secure Password Policies**: Enforce strong passwords
- **Activity Logging**: Track all admin actions
- **Audit Trails**: Complete history of data changes
- **CSRF Protection**: Built-in Laravel CSRF tokens
- **XSS Protection**: Output escaping, Content Security Policy
- **SQL Injection Protection**: Parameterized queries

#### Performance Optimization
- **Redis Caching**: Application caching, session storage
- **Query Optimization**: Eager loading, query caching
- **Image Optimization**: Automatic image compression, WebP support
- **Lazy Loading**: Images load as user scrolls
- **CDN Support**: Serve static assets from CDN
- **Gzip Compression**: Compress responses
- **Browser Caching**: Cache headers for static assets
- **Database Indexing**: Optimized indexes for fast queries
- **Full-Page Caching**: Cache rendered pages for guests

---

### 🤖 AI & Automation

#### OpenAI Integration
- **Product Description Generator**: AI-generated product descriptions
- **Content Creation**: Blog post ideas, content suggestions
- **SEO Optimization**: Meta description generation
- **Translation Assistance**: AI-powered translation suggestions

#### Email Automation
- **Abandoned Cart Recovery**: 3-email sequence
- **Welcome Series**: Onboarding emails for new users
- **Post-Purchase Follow-up**: Request reviews, cross-sell
- **Re-engagement Campaigns**: Win back inactive customers
- **Birthday Emails**: Automated birthday wishes with coupon

#### Smart Recommendations
- **AI-Powered Suggestions**: Product recommendations based on behavior
- **Related Products**: Smart matching of related items
- **Frequently Bought Together**: Amazon-style recommendations
- **Recently Viewed**: Personalized browsing history
- **Trending Products**: Popular items in user's category

---

## 📸 Screenshots

<details>
<summary>Click to view screenshots</summary>

![Admin Dashboard](https://user-images.githubusercontent.com/29488275/90719413-13b82200-e2d4-11ea-8ca0-f0e5551c4c9d.png)
![Category Management](https://user-images.githubusercontent.com/29488275/90719470-3813fe80-e2d4-11ea-8f63-e6001855a945.png)
![Product Management](https://user-images.githubusercontent.com/29488275/90719534-61348f00-e2d4-11ea-8a81-409daee0ad94.png)
![Order Details](https://user-images.githubusercontent.com/29488275/90719557-71e50500-e2d4-11ea-97cf-befb1d525643.png)
![User Profile](https://user-images.githubusercontent.com/29488275/90719563-7a3d4000-e2d4-11ea-9e6a-56caac13b146.png)
![Blog Management](https://user-images.githubusercontent.com/29488275/90719572-81644e00-e2d4-11ea-9fe5-3325ab427f88.png)
![Frontend](https://user-images.githubusercontent.com/29488275/90719631-a1940d00-e2d4-11ea-89a3-eb36960d687d.png)

</details>

---

## 📚 Documentation

### Table of Contents

1. [Installation Guides](#installation-guides)
2. [API Documentation](#api-documentation)
3. [Module Documentation](#module-documentation)
4. [Command Reference](#command-reference)
5. [Testing](#testing)
6. [Recent Enhancements](#recent-enhancements)

---

### Installation Guides

#### Docker Detailed Setup

**Prerequisites:** Docker & Docker Compose

**Step-by-step:**

```bash
# Start all containers
docker-compose up -d

# Container access
docker exec -it e_comm_app sh      # App container
docker exec -it e_comm_mysql mysql -u homestead -p  # Database
docker exec -it e_comm_redis redis-cli               # Redis

# Useful commands
docker exec e_comm_app php artisan cache:clear
docker exec e_comm_app php artisan view:clear
docker exec e_comm_app php artisan migrate

# Container ports:
# - Web (FrankenPHP):  90 → 80
# - MySQL:            3311 → 3306
# - Redis:            6381 → 6379
# - Elasticsearch:    9200 → 9200
```

#### Email Configuration

```bash
# Configure in .env
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls

# Process abandoned cart emails
php artisan cart:process-abandoned-emails
```

#### Elasticsearch Setup

```bash
# Index products
docker-compose exec app php artisan product:index

# Rebuild from scratch
docker-compose exec app php artisan product:index --fresh
```

---

### API Documentation

**Postman Collection:** `LaravelEcomm.postman_collection.json`

**Base URL:** `http://localhost:90/api/v1`

#### Authentication

```bash
# Login
POST /api/v1/auth/login
{
    "email": "client@mail.com",
    "password": "password"
}
```

#### Multi-Language API

```bash
# List languages
GET /api/languages

# Get current locale
GET /api/languages/current
X-Locale: mk
```

#### Reporting API

```bash
# List report types
GET /api/admin/report-types

# Create report
POST /api/admin/reports
{
    "name": "Monthly Sales",
    "type": "sales",
    "format": "excel",
    "filters": {
        "date_from": "2026-01-01",
        "date_to": "2026-01-31"
    }
}

# Generate & Export
POST /api/admin/reports/{id}/generate
POST /api/admin/reports/{id}/export
{ "format": "csv" }
```

#### GeoLocalization API

```bash
# Get location from IP
GET /api/geolocation

# Convert currency
POST /api/currency/convert
{
    "amount": 100,
    "from": "USD",
    "to": "EUR"
}
```

---

### Module Documentation

#### Attribute System

```php
// Create attribute with options
$color = Attribute::factory()->create([
    'code' => 'color',
    'type' => 'select',
    'display' => 'color',
    'is_filterable' => true,
]);

$color->options()->create([
    'value' => 'red',
    'label' => 'Red',
    'color_hex' => '#FF0000',
]);

// Create configurable product
$product = Product::factory()->create([
    'type' => Product::TYPE_CONFIGURABLE,
]);
$product->configurableAttributes()->attach($color);

// Generate variants
app(ConfigurableProductService::class)->generateVariants($product);
```

#### SEO Configuration

```bash
# Generate sitemaps
php artisan seo:generate-sitemap

# Configuration in config/seo.php
```

---

### Command Reference

#### Cache Management

```bash
php artisan cache:clear          # Clear application cache
php artisan config:clear         # Clear config cache
php artisan view:clear           # Clear compiled views
php artisan route:clear          # Clear route cache
```

#### Database & Seeding

```bash
php artisan migrate:fresh --seed     # Fresh database with seeders
php artisan db:seed --class=DatabaseSeeder  # Run specific seeder
```

#### Product Management

```bash
php artisan product:index          # Index products in Elasticsearch
php artisan product:index --fresh  # Rebuild index
```

#### Email & Marketing

```bash
php artisan cart:process-abandoned-emails  # Process abandoned carts
php artisan newsletter:send-campaigns      # Send newsletter campaigns
```

#### Analytics & Reports

```bash
php artisan analytics:aggregate    # Aggregate analytics data
php artisan reports:generate       # Generate scheduled reports
```

---

### Testing

#### Running Tests

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --filter=OrderTest

# Run with coverage
php artisan test --coverage

# Run E2E tests (requires Playwright)
npx playwright test
```

#### Test Accounts

```
Admin:    superadmin@mail.com / password
Client:   client@mail.com / password
```

---

### Recent Enhancements

<details>
<summary>Click to expand recent updates</summary>

#### Latest: Cart/Checkout & Payment Fixes
- ✅ Modern theme views for cart, checkout, my-orders
- ✅ Fixed payment workflows (Stripe, PayPal, COD)
- ✅ Client orders access fixed
- ✅ E2E tests with Playwright

#### API Refactoring & Architecture
- Action-based architecture for all controllers
- Complete API coverage for all modules
- 540+ tests passing
- PHPStan compliance

#### Multi-Language, Reporting & GeoLocalization
- URL prefix strategy (`/en/`, `/mk/`, `/de/`)
- 8 Report types with scheduling
- GeoIP detection with currency conversion
- Real-time exchange rates

#### Modern Theme Implementation
- 32+ view files for modern theme
- Responsive design with comprehensive coverage
- Easy theme switching via settings

#### Attribute System (Bagisto-style)
- Polymorphic attributes for Products, Bundles, Categories
- Visual swatches (color, image, button)
- Configurable products with auto-variant generation
- Layered navigation with AJAX filtering

</details>

---

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

---

## 📄 License

This project is licensed under the MIT License.

---

## 🔗 Quick Links

| Resource | URL |
|----------|-----|
| **Demo** | https://e-comm.mk |
| **Admin** | http://localhost:90/admin |
| **API Docs** | `LaravelEcomm.postman_collection.json` |
| **Frontend** | http://localhost:90 |

---

<p align="center">Built with ❤️ using Laravel 12</p>
