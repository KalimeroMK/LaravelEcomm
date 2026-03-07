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
- **Multi-Theme Support**: Default & Modern themes with easy switching
- **Responsive Design**: Mobile-first, modern UI/UX
- **Multi-Language**: URL prefix strategy (`/en/`, `/mk/`, `/de/`)
- **GeoIP Localization**: Auto-detect country, currency, timezone
- **Advanced Search**: Elasticsearch integration with filters
- **Shopping Cart**: Real-time updates, wishlist, saved carts
- **Product Reviews**: Rating system with comments
- **Coupons & Promotions**: Fixed, percentage, free shipping
- **Payment Methods**: PayPal, Stripe, Cash on Delivery
- **Blog System**: Categories, tags, SEO-friendly URLs
- **User Dashboard**: Orders, addresses, wishlist, reviews

### ⚙️ Admin Dashboard
- **Analytics**: Interactive charts, sales reports, user behavior
- **Product Management**: Attributes, variants, stock, media
- **Order Management**: Full lifecycle, PDF invoices, statuses
- **Content Management**: Blog, banners, pages, SEO
- **Marketing Tools**: Email campaigns, newsletters, abandoned cart
- **User Management**: Roles, permissions, impersonation
- **System Settings**: Email, payment, SEO configuration

### 🔐 Security & Performance
- **Two-Factor Authentication**: Google 2FA
- **Role-Based Access**: Granular permissions
- **Redis Caching**: Performance optimization
- **Security**: IP blocking, activity logging, audit trails

### 🤖 AI & Automation
- **OpenAI Integration**: Product descriptions, content generation
- **Email Automation**: Abandoned cart, welcome sequences
- **Smart Recommendations**: AI-powered product suggestions

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
