<?php

declare(strict_types=1);

namespace Modules\Product\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Class ProductDownload
 *
 * @property int $id
 * @property int $product_id
 * @property string $file_name
 * @property string $file_path
 * @property string $original_name
 * @property string|null $mime_type
 * @property int|null $file_size
 * @property int $sort_order
 * @property bool $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Product $product
 * @property-read \Illuminate\Database\Eloquent\Collection<int, OrderDownload> $orderDownloads
 *
 * @method static Builder|ProductDownload active()
 * @method static Builder|ProductDownload forProduct(int $productId)
 */
class ProductDownload extends Model
{
    use HasFactory;

    protected static function newFactory(): \Modules\Product\Database\Factories\ProductDownloadFactory
    {
        return \Modules\Product\Database\Factories\ProductDownloadFactory::new();
    }

    protected $fillable = [
        'product_id',
        'file_name',
        'file_path',
        'original_name',
        'mime_type',
        'file_size',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the product that owns this download.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get order downloads (tracking).
     */
    public function orderDownloads(): HasMany
    {
        return $this->hasMany(OrderDownload::class);
    }

    /**
     * Scope: Get only active downloads.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Get downloads for specific product.
     */
    public function scopeForProduct(Builder $query, int $productId): Builder
    {
        return $query->where('product_id', $productId);
    }

    /**
     * Get formatted file size.
     */
    public function getFormattedFileSizeAttribute(): string
    {
        $bytes = $this->file_size;
        
        if ($bytes === null) {
            return 'Unknown';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $unitIndex = 0;

        while ($bytes >= 1024 && $unitIndex < count($units) - 1) {
            $bytes /= 1024;
            $unitIndex++;
        }

        return round($bytes, 2) . ' ' . $units[$unitIndex];
    }

    /**
     * Get download URL (temporary signed URL).
     */
    public function getDownloadUrl(int $orderId, int $userId): string
    {
        return route('product.download', [
            'download' => $this->id,
            'order' => $orderId,
            'signature' => hash('sha256', $this->id . ':' . $orderId . ':' . $userId . ':' . config('app.key')),
        ]);
    }
}
