<?php

declare(strict_types=1);

namespace Modules\Coupon\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Core\Models\Core;
use Modules\Coupon\Database\Factories\CouponFactory;

/**
 * Class Coupon
 *
 * @property int $id
 * @property string $code
 * @property string|null $name
 * @property string|null $description
 * @property string $type (fixed, percent, free_shipping)
 * @property float $value
 * @property float|null $minimum_amount
 * @property float|null $maximum_discount
 * @property int|null $usage_limit
 * @property int|null $usage_limit_per_user
 * @property int $times_used
 * @property \Illuminate\Support\Carbon|null $starts_at
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property string $status
 * @property bool $is_public
 * @property bool $is_stackable
 * @property bool $free_shipping
 * @property array|null $applicable_products
 * @property array|null $applicable_categories
 * @property array|null $applicable_brands
 * @property array|null $excluded_products
 * @property array|null $excluded_categories
 * @property array|null $excluded_brands
 * @property array|null $customer_groups
 * @property array|null $customer_ids
 * @property-read \Illuminate\Database\Eloquent\Collection<int, CouponUsage> $usages
 *
 * @method static Builder|Coupon active()
 * @method static Builder|Coupon validForDate(\Illuminate\Support\Carbon $date)
 * @method static Builder|Coupon public()
 * @method static Builder|Coupon byCode(string $code)
 */
class Coupon extends Core
{
    use HasFactory;

    public const TYPE_FIXED = 'fixed';
    public const TYPE_PERCENT = 'percent';
    public const TYPE_FREE_SHIPPING = 'free_shipping';

    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';

    protected $table = 'coupons';

    protected $casts = [
        'value' => 'float',
        'minimum_amount' => 'float',
        'maximum_discount' => 'float',
        'usage_limit' => 'integer',
        'usage_limit_per_user' => 'integer',
        'times_used' => 'integer',
        'expires_at' => 'datetime',
        'starts_at' => 'datetime',
        'is_public' => 'boolean',
        'is_stackable' => 'boolean',
        'free_shipping' => 'boolean',
        'applicable_products' => 'array',
        'applicable_categories' => 'array',
        'applicable_brands' => 'array',
        'excluded_products' => 'array',
        'excluded_categories' => 'array',
        'excluded_brands' => 'array',
        'customer_groups' => 'array',
        'customer_ids' => 'array',
    ];

    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
        'value',
        'minimum_amount',
        'maximum_discount',
        'usage_limit',
        'usage_limit_per_user',
        'times_used',
        'starts_at',
        'expires_at',
        'status',
        'is_public',
        'is_stackable',
        'free_shipping',
        'applicable_products',
        'applicable_categories',
        'applicable_brands',
        'excluded_products',
        'excluded_categories',
        'excluded_brands',
        'customer_groups',
        'customer_ids',
    ];

    public static function Factory(): CouponFactory
    {
        return CouponFactory::new();
    }

    /**
     * Get usages relation
     */
    public function usages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    /**
     * Scope: Active coupons
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope: Public coupons
     */
    public function scopePublic(Builder $query): Builder
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope: Valid for given date
     */
    public function scopeValidForDate(Builder $query, \Illuminate\Support\Carbon $date): Builder
    {
        return $query->where(function ($q) use ($date): void {
            $q->whereNull('starts_at')
                ->orWhere('starts_at', '<=', $date);
        })->where(function ($q) use ($date): void {
            $q->whereNull('expires_at')
                ->orWhere('expires_at', '>=', $date);
        });
    }

    /**
     * Scope: Find by code
     */
    public function scopeByCode(Builder $query, string $code): Builder
    {
        return $query->where('code', strtoupper($code));
    }

    /**
     * Check if coupon is currently valid
     */
    public function isValid(): bool
    {
        if ($this->status !== self::STATUS_ACTIVE) {
            return false;
        }

        $now = now();

        if ($this->starts_at && $this->starts_at->isFuture()) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Check if usage limit reached
     */
    public function isUsageLimitReached(): bool
    {
        if ($this->usage_limit === null) {
            return false;
        }

        return $this->times_used >= $this->usage_limit;
    }

    /**
     * Check if per-user limit reached
     */
    public function isUsageLimitReachedForUser(?int $userId, ?string $sessionId = null): bool
    {
        if ($this->usage_limit_per_user === null) {
            return false;
        }

        if ($userId) {
            $usageCount = CouponUsage::countUsageForUser($this->id, $userId);
        } elseif ($sessionId) {
            $usageCount = CouponUsage::countUsageForSession($this->id, $sessionId);
        } else {
            return false;
        }

        return $usageCount >= $this->usage_limit_per_user;
    }

    /**
     * Check if coupon applies to a product
     */
    public function isApplicableToProduct(int $productId, ?int $categoryId = null, ?int $brandId = null): bool
    {
        // Check excluded first
        if ($this->excluded_products && in_array($productId, $this->excluded_products, true)) {
            return false;
        }

        if ($categoryId && $this->excluded_categories && in_array($categoryId, $this->excluded_categories, true)) {
            return false;
        }

        if ($brandId && $this->excluded_brands && in_array($brandId, $this->excluded_brands, true)) {
            return false;
        }

        // Check applicable
        if ($this->applicable_products && !in_array($productId, $this->applicable_products, true)) {
            return false;
        }

        if ($categoryId && $this->applicable_categories && !in_array($categoryId, $this->applicable_categories, true)) {
            return false;
        }

        if ($brandId && $this->applicable_brands && !in_array($brandId, $this->applicable_brands, true)) {
            return false;
        }

        return true;
    }

    /**
     * Check if coupon is applicable to customer
     */
    public function isApplicableToCustomer(?int $userId, ?int $customerGroupId = null): bool
    {
        // If specific customer IDs are set
        if ($this->customer_ids && $userId && !in_array($userId, $this->customer_ids, true)) {
            return false;
        }

        // If specific customer groups are set
        if ($this->customer_groups && $customerGroupId && !in_array($customerGroupId, $this->customer_groups, true)) {
            return false;
        }

        return true;
    }

    /**
     * Calculate discount amount
     */
    public function calculateDiscount(float $subtotal, ?float $shippingCost = 0): float
    {
        // Check minimum amount
        if ($this->minimum_amount !== null && $subtotal < $this->minimum_amount) {
            return 0;
        }

        $discount = 0;

        if ($this->type === self::TYPE_FREE_SHIPPING) {
            $discount = $shippingCost ?? 0;
        } elseif ($this->type === self::TYPE_FIXED) {
            $discount = min($this->value, $subtotal);
        } elseif ($this->type === self::TYPE_PERCENT) {
            $discount = $subtotal * ($this->value / 100);
        }

        // Apply maximum discount cap
        if ($this->maximum_discount !== null && $discount > $this->maximum_discount) {
            $discount = $this->maximum_discount;
        }

        return round($discount, 2);
    }

    /**
     * Increment usage counter
     */
    public function incrementUsage(): void
    {
        $this->increment('times_used');
    }

    /**
     * Get formatted discount text
     */
    public function getDiscountText(): string
    {
        if ($this->type === self::TYPE_FREE_SHIPPING) {
            return 'Free Shipping';
        }

        if ($this->type === self::TYPE_FIXED) {
            return '$' . number_format($this->value, 2) . ' off';
        }

        return $this->value . '% off';
    }

    /**
     * Check if this is a free shipping coupon
     */
    public function isFreeShipping(): bool
    {
        return $this->type === self::TYPE_FREE_SHIPPING || $this->free_shipping;
    }
}
