<?php

declare(strict_types=1);

/**
 * Created by Reliese Model.
 */

namespace Modules\Product\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Modules\User\Models\User;

/**
 * Class ProductReview
 *
 * @property int $id
 * @property int $rate
 * @property string|null $review
 * @property string $status
 * @property int|null $user_id
 * @property int|null $product_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Product|null $product
 * @property-read User|null    $user
 *
 * @method static Builder<static>|ProductReview newModelQuery()
 * @method static Builder<static>|ProductReview newQuery()
 * @method static Builder<static>|ProductReview query()
 * @method static Builder<static>|ProductReview whereCreatedAt($value)
 * @method static Builder<static>|ProductReview whereId($value)
 * @method static Builder<static>|ProductReview whereProductId($value)
 * @method static Builder<static>|ProductReview whereRate($value)
 * @method static Builder<static>|ProductReview whereReview($value)
 * @method static Builder<static>|ProductReview whereStatus($value)
 * @method static Builder<static>|ProductReview whereUpdatedAt($value)
 * @method static Builder<static>|ProductReview whereUserId($value)
 *
 * @mixin Eloquent
 */
class ProductReview extends Model
{
    protected $table = 'product_reviews';

    protected $casts = [
        'user_id' => 'int',
        'product_id' => 'int',
        'rate' => 'int',
    ];

    protected $fillable = [
        'user_id',
        'product_id',
        'rate',
        'review',
        'status',
    ];

    /**
     * Get all reviews.
     *
     * @return Collection<int, self>
     */
    public static function getAllReview(): Collection
    {
        return self::with('product', 'user')->get();
    }

    /**
     * Get all reviews by user.
     *
     * @return Collection<int, self>
     */
    public static function getAllUserReview(): Collection
    {
        return self::where('user_id', auth()->id())->get();
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
