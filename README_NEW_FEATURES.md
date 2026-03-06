# New Features Implementation Plan

## Summary: Multi-language, GeoIP & Advanced Reporting

This document outlines the implementation plan for three major feature areas.

---

## 1. Multi-language Product Content

### Current State
✅ **UI Translations**: 7 languages supported (EN, MK, DE, ES, FR, IT, AR)
❌ **Product Content**: Names, descriptions, attributes are NOT translatable
❌ **URL Localization**: No locale-based URLs (/en/product, /mk/product)

### Required Implementation

#### Database
```php
// product_translations table
- product_id, locale, name, description, slug, meta_title, meta_description
```

#### Action Classes
- `GetProductTranslationAction` - Get translation for product/locale
- `SetProductTranslationAction` - Create/update translation
- `GetProductInLocaleAction` - Get product with auto-translation fallback
- `SyncProductTranslationsAction` - Bulk sync translations

#### API Endpoints
```
GET  /api/products/{id}/translations/{locale}
POST /api/products/{id}/translations
GET  /api/products/{id}/localized (uses X-Locale header)
```

#### Estimated Effort: 3-4 days

---

## 2. GeoIP-based Localization

### Current State
❌ **Not implemented** - No GeoIP detection

### Required Implementation

#### Database
```php
// geoip_cache table
- ip_address, country_code, country_name, region, city, currency_code
```

#### Action Classes
- `DetectLocationAction` - Detect location from IP
- `GetCurrencyByCountryAction` - Map country to currency
- `GetLanguageByCountryAction` - Map country to language
- `ConvertPriceAction` - Convert prices between currencies
- `ApplyGeoLocalizationAction` - Apply all localization settings

#### Middleware
- `GeoIPMiddleware` - Auto-detect and set locale/currency

#### API Endpoints
```
GET  /api/geoip/current              - Get detected location
POST /api/geoip/preference           - Set manual override
POST /api/geoip/convert-price        - Convert price between currencies
```

#### Services
- `MaxMindGeoIPService` - MaxMind integration
- `IPApiGeoIPService` - Free ip-api alternative

#### Estimated Effort: 2-3 days

---

## 3. Advanced Reporting

### Current State
✅ **Basic Analytics**: Sales, orders, users overview
✅ **Product Stats**: Clicks, impressions
✅ **Email Analytics**: Opens, clicks, bounces
✅ **Basic Export**: Products to Excel/CSV
❌ **Custom Reports**: No drag-and-drop builder
❌ **Scheduled Reports**: No automated email delivery
❌ **Predictive Analytics**: No ML-based predictions

### Required Implementation

#### Database
```php
// reports table
- name, type, filters (json), columns (json), created_by, is_scheduled

// report_results table (cache)
- report_id, result_data, generated_at, expires_at

// scheduled_reports table
- report_id, frequency, recipients, next_run_at
```

#### Action Classes
- `BuildReportAction` - Build query from report config
- `GenerateReportAction` - Generate with caching
- `ExportReportAction` - Export to XLSX/CSV/PDF
- `ScheduleReportAction` - Schedule automated reports
- `SendScheduledReportAction` - Email scheduled reports
- `GetReportAnalyticsAction` - Get chart data
- `PredictSalesAction` - Simple trend-based predictions

#### Exporters
- `ExcelReportExporter` - Laravel Excel integration
- `CsvReportExporter` - CSV export
- `PdfReportExporter` - PDF with charts

#### Command
```bash
php artisan reports:send-scheduled
```

#### API Endpoints
```
GET  /api/reports                    - List reports
POST /api/reports                    - Create report
GET  /api/reports/{id}               - Get report data
POST /api/reports/{id}/export        - Export report
POST /api/reports/{id}/schedule      - Schedule report
GET  /api/reports/analytics          - Get chart data
GET  /api/reports/predictions        - Get predictions
```

#### Estimated Effort: 4-5 days

---

## Priority Recommendation

| Priority | Feature | Effort | Impact |
|----------|---------|--------|--------|
| 1 | Multi-language Product Content | 3-4 days | High |
| 2 | Advanced Reporting | 4-5 days | High |
| 3 | GeoIP Localization | 2-3 days | Medium |

**Total Estimated Time: 9-12 days**

---

## Architecture Principles

All features follow the established architecture:

1. **Thin Controllers**: Web/API controllers delegate to Actions
2. **Reusable Actions**: Same actions used by Web and API
3. **DTOs**: Data transfer between layers
4. **Services**: External integrations (GeoIP, Export)
5. **Caching**: Performance optimization for reports and GeoIP

---

## Files to Create/Modify

### Multi-language
```
Modules/Product/Database/Migrations/xxxx_create_product_translations_table.php
Modules/Product/Models/ProductTranslation.php
Modules/Product/Actions/GetProductTranslationAction.php
Modules/Product/Actions/SetProductTranslationAction.php
Modules/Product/Actions/GetProductInLocaleAction.php
Modules/Product/Http/Controllers/ProductTranslationController.php
Modules/Product/Http/Controllers/Api/ProductTranslationController.php
```

### GeoIP
```
config/geoip.php
Modules/Core/Database/Migrations/xxxx_create_geoip_cache_table.php
Modules/Core/Actions/DetectLocationAction.php
Modules/Core/Actions/ApplyGeoLocalizationAction.php
Modules/Core/Services/GeoIP/MaxMindGeoIPService.php
Modules/Core/Services/GeoIP/IPApiGeoIPService.php
Modules/Core/Http/Middleware/GeoIPMiddleware.php
Modules/Core/Http/Controllers/Api/GeoIPController.php
```

### Reporting
```
Modules/Admin/Database/Migrations/xxxx_create_reports_tables.php
Modules/Admin/Models/Report.php
Modules/Admin/Models/ScheduledReport.php
Modules/Admin/Actions/BuildReportAction.php
Modules/Admin/Actions/ExportReportAction.php
Modules/Admin/Actions/ScheduleReportAction.php
Modules/Admin/Actions/PredictSalesAction.php
Modules/Admin/Services/ReportQueryBuilder.php
Modules/Admin/Exports/ReportExport.php
Modules/Admin/Console/SendScheduledReportsCommand.php
Modules/Admin/Http/Controllers/ReportController.php
Modules/Admin/Http/Controllers/Api/ReportController.php
```
