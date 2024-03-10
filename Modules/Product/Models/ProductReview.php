<?php

/**
 * Created by Reliese Model.
 */

namespace Modules\Product\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
}
