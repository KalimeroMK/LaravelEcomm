<?php

declare(strict_types=1);

/**
 * Created by Zoran Shefot Bogoevski.
 */

namespace Modules\Billing\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Cart\Models\Cart;
use Modules\Core\Models\Core;
use Modules\Product\Models\Product;
use Modules\User\Models\User;

/**
 * Class Wishlist
 *
 * @property int $id
 * @property float $price
 * @property int $quantity
 * @property float $amount
 * @property int $product_id
 * @property int|null $cart_id
 * @property int|null $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Cart|null $cart
 * @property-read Product $product
 * @property-read User|null $user
 *
 * @method static Builder<static>|Wishlist newModelQuery()
 * @method static Builder<static>|Wishlist newQuery()
 * @method static Builder<static>|Wishlist query()
 * @method static Builder<static>|Wishlist whereAmount($value)
 * @method static Builder<static>|Wishlist whereCartId($value)
 * @method static Builder<static>|Wishlist whereCreatedAt($value)
 * @method static Builder<static>|Wishlist whereId($value)
 * @method static Builder<static>|Wishlist wherePrice($value)
 * @method static Builder<static>|Wishlist whereProductId($value)
 * @method static Builder<static>|Wishlist whereQuantity($value)
 * @method static Builder<static>|Wishlist whereUpdatedAt($value)
 * @method static Builder<static>|Wishlist whereUserId($value)
 *
 * @mixin Eloquent
 */
class Wishlist extends Core
{
    protected $table = 'wishlists';

    protected $casts = [
        'product_id' => 'int',
        'cart_id' => 'int',
        'user_id' => 'int',
        'price' => 'float',
        'quantity' => 'int',
        'amount' => 'float',
    ];

    protected $fillable = [
        'product_id',
        'cart_id',
        'user_id',
        'price',
        'quantity',
        'amount',
    ];

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
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
