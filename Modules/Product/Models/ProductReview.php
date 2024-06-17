<?php

/**
 * Created by Reliese Model.
 */

namespace Modules\Product\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Modules\User\Models\User;

/**
 * Class ProductReview
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $product_id
 * @property int $rate
 * @property string|null $review
 * @property string $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Product|null $product
 * @property User|null $user
 *
 * @package App\Models
 */
class ProductReview extends Model
{
    protected $table = 'product_reviews';

    protected $casts = [
        'user_id' => 'int',
        'product_id' => 'int',
        'rate' => 'int'
    ];

    protected $fillable = [
        'user_id',
        'product_id',
        'rate',
        'review',
        'status'
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

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
}
