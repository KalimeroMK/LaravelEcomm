<?php

declare(strict_types=1);

namespace Modules\Reporting\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Core\Models\Core;
use Modules\User\Models\User;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string $type
 * @property string $format
 * @property array|null $filters
 * @property array|null $columns
 * @property array|null $grouping
 * @property array|null $sorting
 * @property int $created_by
 * @property bool $is_template
 * @property bool $is_public
 * @property int $sort_order
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read User $creator
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ReportSchedule> $schedules
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ReportExecution> $executions
 */
class Report extends Core
{
    use HasFactory;

    public const TYPE_SALES = 'sales';
    public const TYPE_PRODUCTS = 'products';
    public const TYPE_CUSTOMERS = 'customers';
    public const TYPE_INVENTORY = 'inventory';
    public const TYPE_ORDERS = 'orders';
    public const TYPE_COUPONS = 'coupons';
    public const TYPE_REVENUE = 'revenue';
    public const TYPE_TAX = 'tax';

    public const FORMAT_HTML = 'html';
    public const FORMAT_PDF = 'pdf';
    public const FORMAT_EXCEL = 'excel';
    public const FORMAT_CSV = 'csv';

    public const TYPES = [
        self::TYPE_SALES,
        self::TYPE_PRODUCTS,
        self::TYPE_CUSTOMERS,
        self::TYPE_INVENTORY,
        self::TYPE_ORDERS,
        self::TYPE_COUPONS,
        self::TYPE_REVENUE,
        self::TYPE_TAX,
    ];

    public const FORMATS = [
        self::FORMAT_HTML,
        self::FORMAT_PDF,
        self::FORMAT_EXCEL,
        self::FORMAT_CSV,
    ];

    protected $fillable = [
        'name',
        'slug',
        'description',
        'type',
        'format',
        'filters',
        'columns',
        'grouping',
        'sorting',
        'created_by',
        'is_template',
        'is_public',
        'sort_order',
    ];

    protected $casts = [
        'filters' => 'array',
        'columns' => 'array',
        'grouping' => 'array',
        'sorting' => 'array',
        'is_template' => 'boolean',
        'is_public' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(ReportSchedule::class);
    }

    public function executions(): HasMany
    {
        return $this->hasMany(ReportExecution::class)->orderBy('started_at', 'desc');
    }

    public function activeSchedules(): HasMany
    {
        return $this->hasMany(ReportSchedule::class)->where('is_active', true);
    }

    public function lastExecution(): ?ReportExecution
    {
        return $this->executions()->first();
    }

    public function scopeTemplates(Builder $query): Builder
    {
        return $query->where('is_template', true);
    }

    public function scopePublic(Builder $query): Builder
    {
        return $query->where('is_public', true);
    }

    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where(function ($q) use ($userId): void {
            $q->where('created_by', $userId)
              ->orWhere('is_public', true);
        });
    }

    public function isScheduled(): bool
    {
        return $this->schedules()->where('is_active', true)->exists();
    }

    public function getAvailableColumns(): array
    {
        return match ($this->type) {
            self::TYPE_SALES => [
                'date' => 'Date',
                'order_id' => 'Order ID',
                'customer' => 'Customer',
                'product' => 'Product',
                'quantity' => 'Quantity',
                'unit_price' => 'Unit Price',
                'total' => 'Total',
                'status' => 'Status',
                'payment_method' => 'Payment Method',
            ],
            self::TYPE_PRODUCTS => [
                'sku' => 'SKU',
                'name' => 'Product Name',
                'category' => 'Category',
                'stock' => 'Stock',
                'price' => 'Price',
                'sold' => 'Units Sold',
                'revenue' => 'Revenue',
            ],
            self::TYPE_CUSTOMERS => [
                'name' => 'Name',
                'email' => 'Email',
                'orders' => 'Total Orders',
                'spent' => 'Total Spent',
                'last_order' => 'Last Order',
                'registered' => 'Registered Date',
            ],
            self::TYPE_ORDERS => [
                'order_number' => 'Order Number',
                'date' => 'Date',
                'customer' => 'Customer',
                'items' => 'Items',
                'subtotal' => 'Subtotal',
                'discount' => 'Discount',
                'shipping' => 'Shipping',
                'tax' => 'Tax',
                'total' => 'Total',
                'status' => 'Status',
            ],
            default => [],
        };
    }

    public function getDefaultFilters(): array
    {
        return match ($this->type) {
            self::TYPE_SALES, self::TYPE_ORDERS, self::TYPE_REVENUE => [
                'date_from' => now()->subDays(30)->format('Y-m-d'),
                'date_to' => now()->format('Y-m-d'),
                'status' => [],
                'payment_method' => [],
            ],
            self::TYPE_PRODUCTS => [
                'category_id' => [],
                'stock_status' => 'all', // in_stock, low_stock, out_of_stock
                'date_from' => null,
                'date_to' => null,
            ],
            self::TYPE_CUSTOMERS => [
                'date_from' => null,
                'date_to' => null,
                'min_orders' => null,
                'min_spent' => null,
            ],
            default => [],
        };
    }
}
