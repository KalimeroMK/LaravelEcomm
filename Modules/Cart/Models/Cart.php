<?php

    /**
     * Created by Zoran Shefot Bogoevski.
     */

    namespace Modules\Cart\Models;

    use Carbon\Carbon;
    use Database\Factories\CartFactory;
    use Eloquent;
    use Illuminate\Database\Eloquent\Builder;
    use Illuminate\Database\Eloquent\Collection;
    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Relations\BelongsTo;
    use Illuminate\Database\Eloquent\Relations\HasMany;
    use Modules\Billing\Models\Wishlist;
    use Modules\Order\Models\Order;
    use Modules\Product\Models\Product;
    use Modules\User\Models\User;

    /**
     * Class Cart
     *
     * @property int $id
     * @property int $product_id
     * @property int|null $order_id
     * @property int|null $user_id
     * @property float $price
     * @property string $status
     * @property int $quantity
     * @property float $amount
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @property Order|null $order
     * @property Product $product
     * @property User|null $user
     * @property Collection|Wishlist[] $wishlists
     * @package App\Models
     * @property-read int|null $wishlists_count
     * @method static Builder|Cart newModelQuery()
     * @method static Builder|Cart newQuery()
     * @method static Builder|Cart query()
     * @method static Builder|Cart whereAmount($value)
     * @method static Builder|Cart whereCreatedAt($value)
     * @method static Builder|Cart whereId($value)
     * @method static Builder|Cart whereOrderId($value)
     * @method static Builder|Cart wherePrice($value)
     * @method static Builder|Cart whereProductId($value)
     * @method static Builder|Cart whereQuantity($value)
     * @method static Builder|Cart whereStatus($value)
     * @method static Builder|Cart whereUpdatedAt($value)
     * @method static Builder|Cart whereUserId($value)
     * @mixin Eloquent
     */
    class Cart extends Model
    {
        use HasFactory;

        protected $table = 'carts';

        protected $casts = [
            'product_id' => 'int',
            'order_id'   => 'int',
            'user_id'    => 'int',
            'price'      => 'float',
            'quantity'   => 'int',
            'amount'     => 'float',
        ];

        protected $fillable = [
            'product_id',
            'order_id',
            'user_id',
            'price',
            'status',
            'quantity',
            'amount',
        ];

        /**
         * @return CartFactory
         */
        public static function factory(): CartFactory
        {
            return new CartFactory();
        }

        /**
         * @return BelongsTo
         */
        public function order(): BelongsTo
        {
            return $this->belongsTo(Order::class);
        }

        /**
         * @return BelongsTo
         */
        public function product(): BelongsTo
        {
            return $this->belongsTo(Product::class);
        }

        /**
         * @return BelongsTo
         */
        public function user(): BelongsTo
        {
            return $this->belongsTo(User::class);
        }

        /**
         * @return HasMany
         */
        public function wishlists(): HasMany
        {
            return $this->hasMany(Wishlist::class);
        }
    }
