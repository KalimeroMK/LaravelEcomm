<?php

declare(strict_types=1);

namespace Modules\Product\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Modules\Order\Models\Order;
use Modules\User\Models\User;

/**
 * Class OrderDownload
 *
 * Tracks customer downloads for purchased products
 *
 * @property int $id
 * @property int $order_id
 * @property int $product_download_id
 * @property int $user_id
 * @property int $downloads_count
 * @property Carbon|null $last_downloaded_at
 * @property Carbon|null $expires_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Order $order
 * @property-read ProductDownload $productDownload
 * @property-read User $user
 *
 * @method static Builder|OrderDownload forUser(int $userId)
 * @method static Builder|OrderDownload valid()
 */
class OrderDownload extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_download_id',
        'user_id',
        'downloads_count',
        'last_downloaded_at',
        'expires_at',
    ];

    protected $casts = [
        'downloads_count' => 'integer',
        'last_downloaded_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Get the order.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the product download.
     */
    public function productDownload(): BelongsTo
    {
        return $this->belongsTo(ProductDownload::class);
    }

    /**
     * Get the user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: Get downloads for specific user.
     */
    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: Get valid (not expired) downloads.
     */
    public function scopeValid(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    /**
     * Check if download is expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    /**
     * Check if download limit is reached.
     */
    public function isLimitReached(): bool
    {
        $maxDownloads = $this->productDownload->product->max_downloads;
        
        if ($maxDownloads === null) {
            return false;
        }

        return $this->downloads_count >= $maxDownloads;
    }

    /**
     * Check if download is allowed.
     */
    public function canDownload(): bool
    {
        return !$this->isExpired() && !$this->isLimitReached();
    }

    /**
     * Record a download.
     */
    public function recordDownload(): void
    {
        $this->increment('downloads_count');
        $this->update(['last_downloaded_at' => now()]);
    }
}
