<?php

/**
 * Created by Zoran Shefot Bogoevski.
 */

namespace Modules\Product\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Models\Core;
use Modules\Product\Database\Factories\ProductReviewFactory;
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
 * @property Product|null $product
 * @property User|null $user
 * @package App\Models
 * @method static Builder|ProductReview newModelQuery()
 * @method static Builder|ProductReview newQuery()
 * @method static Builder|ProductReview query()
 * @method static Builder|ProductReview whereCreatedAt($value)
 * @method static Builder|ProductReview whereId($value)
 * @method static Builder|ProductReview whereProductId($value)
 * @method static Builder|ProductReview whereRate($value)
 * @method static Builder|ProductReview whereReview($value)
 * @method static Builder|ProductReview whereStatus($value)
 * @method static Builder|ProductReview whereUpdatedAt($value)
 * @method static Builder|ProductReview whereUserId($value)
 * @mixin Eloquent
 */
class ProductReview extends Core
{
    use HasFactory;
    
    protected $table = 'product_reviews';
    
    protected $casts = [
        'user_id'    => 'int',
        'product_id' => 'int',
        'rate'       => 'int',
    ];
    
    protected $fillable = [
        'user_id',
        'product_id',
        'rate',
        'review',
        'status',
    ];
    
    /**
     * @return ProductReviewFactory
     */
    public static function Factory(): ProductReviewFactory
    {
        return ProductReviewFactory::new();
    }
    
    /**
     * @return LengthAwarePaginator
     */
    public static function getAllReview(): LengthAwarePaginator
    {
        return ProductReview::with(['user', 'product'])->paginate(10);
    }
    
    /**
     * @return LengthAwarePaginator
     */
    public static function getAllUserReview(): LengthAwarePaginator
    {
        return ProductReview::whereUserId(Auth::id())->with('user', 'product')->paginate(10);
    }
    
    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * @return BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
