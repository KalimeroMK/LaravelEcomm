# Promotion Engine Analysis - Bagisto Gap #6

## Overview
This document analyzes the current state of the promotion engine compared to Bagisto's capabilities.

## Current Implementation

### ✅ What We Have

#### 1. Basic Coupon System (`Modules/Coupon`)
- **Fields:** code, type (fixed/percent), value, status, expires_at
- **Features:**
  - Simple coupon creation with code
  - Fixed amount discount ($10 off)
  - Percentage discount (10% off)
  - Expiration dates
  - Basic session-based coupon storage in cart

#### 2. Coupon Application Flow
- `CouponStoreAction` - Applies coupon to session
- `FrontController::couponStore()` - Web endpoint
- Discount calculated in checkout: `session('coupon.value')`

#### 3. Bundle System (`Modules/Bundle`)
- Fixed-price bundles (e.g., "3 products for $99")
- Products grouped together
- Separate from coupon system

---

## ❌ What's Missing (Gap Analysis)

### 1. Cart Price Rules
| Feature | Status | Description |
|---------|--------|-------------|
| Buy X Get Y (BOGO) | ❌ Missing | Buy 2 get 1 free |
| Buy X Get Y Discount | ❌ Missing | Buy 2 get 50% off on third |
| Quantity-based discounts | ❌ Missing | 10% off when buying 5+ items |
| Cart value rules | ❌ Missing | 20% off when cart > $100 |
| Customer group rules | ❌ Missing | VIP customers get extra 10% |
| Product-specific rules | ❌ Missing | 15% off all electronics |
| Category-specific rules | ❌ Missing | 20% off clothing category |
| Brand-specific rules | ❌ Missing | 10% off Nike products |
| Time-based rules | ❌ Missing | Weekend sale: extra 15% off |

### 2. Catalog Price Rules
| Feature | Status | Description |
|---------|--------|-------------|
| Automatic discounts | ❌ Missing | No code needed, visible on product page |
| Category-wide sales | ❌ Missing | All electronics 20% off |
| Brand-wide sales | ❌ Missing | All Nike products 15% off |
| New customer discounts | ❌ Missing | First purchase 10% off |
| VIP/wholesale pricing | ❌ Missing | Different prices per customer group |

### 3. Advanced Coupon Features
| Feature | Status | Description |
|---------|--------|-------------|
| Minimum order amount | ❌ Missing | Code only works if cart > $50 |
| Maximum discount cap | ❌ Missing | "50% off up to $100 max" |
| Usage limits (global) | ❌ Missing | "First 100 customers only" |
| Usage limits per user | ❌ Missing | "One per customer" |
| Usage tracking | ❌ Missing | Track how many times used |
| Product restrictions | ❌ Missing | "Excludes electronics" |
| Category restrictions | ❌ Missing | "Clothing only" |
| Customer group restrictions | ❌ Missing | "Wholesale customers only" |
| Stackable coupons | ❌ Missing | Allow multiple coupons |
| Coupon combinations | ❌ Missing | "Cannot combine with other offers" |
| Free shipping coupons | ❌ Missing | "FREESHIP" code |
| Gift vouchers | ❌ Missing | $50 gift card codes |

### 4. Loyalty & Rewards
| Feature | Status | Description |
|---------|--------|-------------|
| Points system | ❌ Missing | Earn points per purchase |
| Points redemption | ❌ Missing | Redeem points for discounts |
| Tier levels | ❌ Missing | Bronze/Silver/Gold tiers |
| Birthday rewards | ❌ Missing | Special birthday discount |
| Referral rewards | ❌ Missing | Refer friend, get $10 |

### 5. Cross-sell/Upsell Rules
| Feature | Status | Description |
|---------|--------|-------------|
| Product bundles | ⚠️ Basic | Fixed-price bundles exist |
| Dynamic bundles | ❌ Missing | "Buy laptop + bag, save $20" |
| Related product discounts | ❌ Missing | "Complete the look: 10% off accessories" |
| Frequently bought together | ❌ Missing | Amazon-style recommendations |

---

## Database Schema Gaps

### Current `coupons` Table
```php
- id
- code
- type (fixed, percent)
- value
- status
- expires_at
- timestamps
```

### Required Fields for Advanced Promotions
```php
// Cart/Catalog Rule Fields
- name (for admin reference)
- description
- rule_type (cart_price_rule, catalog_price_rule, coupon)
- customer_groups (json)
- channels (json)
- start_date
- end_date
- is_active
- priority (which rule applies first)
- stop_processing (stop if this applies)

// Conditions (JSON)
- conditions [
    "cart_total > 100",
    "product_count >= 3",
    "category IN [1,2,3]",
    "brand = 5",
    "customer_group = vip"
  ]

// Actions
- action_type (percent, fixed, buy_x_get_y, free_shipping)
- action_value
- discount_amount
- discount_quantity (for BOGO)
- discount_step (buy X)

// Limits
- usage_limit (global)
- usage_limit_per_customer
- times_used (counter)
- min_amount
- max_discount_amount

// Product Restrictions
- applicable_products (json)
- applicable_categories (json)
- applicable_brands (json)
- excluded_products (json)
- excluded_categories (json)
```

---

## Implementation Priority

### Phase 1: Enhanced Coupons (High Priority)
1. Add minimum_amount field
2. Add usage_limit and usage_limit_per_user
3. Add usage tracking table (coupon_user)
4. Add product/category/brand restrictions
5. Add free shipping coupon type

### Phase 2: Cart Price Rules (High Priority)
1. Create cart_price_rules table
2. Build rule engine for conditions
3. Implement BOGO (Buy X Get Y)
4. Implement quantity-based discounts
5. Customer group-based discounts

### Phase 3: Catalog Price Rules (Medium Priority)
1. Create catalog_price_rules table
2. Automatic discounts on product pages
3. Category-wide sales
4. Customer-specific pricing

### Phase 4: Loyalty System (Medium Priority)
1. Create loyalty_points table
2. Points earning rules
3. Points redemption
4. Tier levels

### Phase 5: Advanced Features (Lower Priority)
1. Gift cards/vouchers
2. Referral system
3. Dynamic bundles
4. AI-powered recommendations

---

## Files to Modify/Create

### Database Migrations
```
Modules/Coupon/Database/Migrations/
├── 2026_03_06_100000_add_advanced_coupon_fields.php
├── 2026_03_06_100001_create_coupon_usage_table.php
├── 2026_03_06_100002_create_cart_price_rules_table.php
├── 2026_03_06_100003_create_catalog_price_rules_table.php
└── 2026_03_06_100004_create_loyalty_points_table.php
```

### Models
```
Modules/Coupon/Models/
├── Coupon.php (extend with new fields)
├── CouponUsage.php (track usage per user)
├── CartPriceRule.php (cart conditions)
└── CatalogPriceRule.php (automatic discounts)
```

### Actions
```
Modules/Coupon/Actions/
├── ValidateCouponAction.php
├── ApplyCartPriceRulesAction.php
├── ApplyCatalogPriceRulesAction.php
└── CalculateDiscountsAction.php
```

### Controllers
```
Modules/Coupon/Http/Controllers/
├── CartPriceRuleController.php
└── CatalogPriceRuleController.php
```

### Admin Views
```
Modules/Coupon/Resources/views/
├── cart-price-rules/
│   ├── index.blade.php
│   ├── create.blade.php
│   └── edit.blade.php
├── catalog-price-rules/
│   ├── index.blade.php
│   ├── create.blade.php
│   └── edit.blade.php
└── reports/
    └── usage.blade.php
```

---

## Comparison with Bagisto

| Bagisto Feature | Our Status | Gap |
|-----------------|------------|-----|
| Cart Price Rules | ❌ | Full implementation needed |
| Catalog Price Rules | ❌ | Full implementation needed |
| Coupon Codes | ⚠️ Partial | Needs usage limits, restrictions |
| Buy X Get Y | ❌ | Not implemented |
| Free Shipping | ❌ | Not implemented |
| Customer Group Pricing | ❌ | Not implemented |
| Loyalty Points | ❌ | Not implemented |
| Gift Cards | ❌ | Not implemented |

---

## Summary

**Current State:** Basic coupon system with fixed/percentage discounts and expiration dates.

**Missing:**
- 80% of advanced promotion features
- Cart price rules (BOGO, conditions)
- Catalog price rules (automatic discounts)
- Usage tracking and limits
- Customer group-based pricing
- Loyalty system

**Estimated Effort:** 
- Phase 1: 2-3 days
- Phase 2: 4-5 days  
- Phase 3: 3-4 days
- Phase 4: 3-4 days
- **Total: 12-16 days for full feature parity**

**Recommendation:** Start with Phase 1 (Enhanced Coupons) as it builds on existing functionality and provides immediate value.
