<?php

declare(strict_types=1);

/**
 * Created by Zoran Shefot Bogoevski.
 */

namespace Modules\Order\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Kalimeromk\Filterable\Traits\Filterable;
use Modules\Cart\Models\Cart;
use Modules\Core\Models\Core;
use Modules\Order\Database\Factories\OrderFactory;
use Modules\Shipping\Models\Shipping;
use Modules\User\Models\User;

/**
 * Class Order
 *
 * @property int $id
 * @property string|null $order_number
 * @property int|null $user_id
 * @property float $sub_total
 * @property int|null $shipping_id
 * @property float $total_amount
 * @property int $quantity
 * @property string $payment_method
 * @property string $payment_status
 * @property string $status
 * @property int|null $payer_id
 * @property string|null $transaction_reference
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $country
 * @property string|null $city
 * @property string|null $state
 * @property string|null $address1
 * @property string|null $address2
 * @property string|null $post_code
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Cart> $cart_info
 * @property-read int|null              $cart_info_count
 * @property-read Collection<int, Cart> $carts
 * @property-read int|null              $carts_count
 * @property-read Shipping|null         $shipping
 * @property-read User|null             $user
 *
 * @method static Builder<static>|Order filter(array $filters = [])
 * @method static Builder<static>|Order newModelQuery()
 * @method static Builder<static>|Order newQuery()
 * @method static Builder<static>|Order query()
 * @method static Builder<static>|Order whereCreatedAt($value)
 * @method static Builder<static>|Order whereId($value)
 * @method static Builder<static>|Order whereOrderNumber($value)
 * @method static Builder<static>|Order wherePayerId($value)
 * @method static Builder<static>|Order wherePaymentMethod($value)
 * @method static Builder<static>|Order wherePaymentStatus($value)
 * @method static Builder<static>|Order whereQuantity($value)
 * @method static Builder<static>|Order whereShippingId($value)
 * @method static Builder<static>|Order whereStatus($value)
 * @method static Builder<static>|Order whereSubTotal($value)
 * @method static Builder<static>|Order whereTotalAmount($value)
 * @method static Builder<static>|Order whereTransactionReference($value)
 * @method static Builder<static>|Order whereUpdatedAt($value)
 * @method static Builder<static>|Order whereUserId($value)
 *
 * @mixin Eloquent
 */
class Order extends Core
{
    use Filterable;
    use HasFactory;

    public const likeRows
        = [
            'user.name',
            'user.email',
            'order_number',
            'payment_method',
            'payment_status',
            'status',
            'email',
            'phone',
            'country',
            'post_code',
            'total_amount',

        ];

    protected $table = 'orders';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string>
     */
    protected array $dates
        = [
            'created_at',
            'updated_at',
        ];

    protected $casts
        = [
            'user_id' => 'int',
            'sub_total' => 'float',
            'shipping_id' => 'int',
            'total_amount' => 'float',
            'quantity' => 'int',
            'payer_id' => 'int',
            'shipped_at' => 'datetime',
        ];

    protected $fillable
        = [
            'order_number',
            'user_id',
            'sub_total',
            'shipping_id',
            'total_amount',
            'quantity',
            'payment_method',
            'payment_status',
            'status',
            'payer_id',
            'transaction_reference',
            'post_code',
            'tracking_number',
            'tracking_carrier',
            'shipped_at',
            'first_name',
            'last_name',
            'email',
            'phone',
            'country',
            'city',
            'state',
            'address1',
            'address2',
        ];

    public static function Factory(): OrderFactory
    {
        return OrderFactory::new();
    }

    /**
     * @return Builder|Builder[]|Collection|Model|null
     */
    public static function getAllOrder(int $id): Model|Collection|Builder|array|null
    {
        return self::with('cart_info')->find($id);
    }

    public static function countActiveOrder(): int
    {
        $data = self::count();
        if ($data) {
            return $data;
        }

        return 0;
    }

    public function shipping(): BelongsTo
    {
        return $this->belongsTo(Shipping::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The customer's return/refund request for this order, if any.
     */
    public function orderReturn(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(OrderReturn::class);
    }

    /**
     * A return can be requested once the order shipped/arrived and
     * no return exists yet.
     */
    public function canRequestReturn(): bool
    {
        return in_array($this->status, ['shipped', 'delivered'], true)
            && $this->orderReturn === null;
    }

    /**
     * Reduce product stock for every line on this order. Called once when
     * the order is created; clamped so stock never goes negative.
     */
    public function decrementStock(): void
    {
        foreach ($this->carts()->with('product')->get() as $line) {
            $product = $line->product;

            if (! $product) {
                continue;
            }

            $product->stock = max(0, (int) $product->stock - (int) $line->quantity);
            $product->save();
        }
    }

    /**
     * Give the stock back, e.g. when the order is cancelled.
     */
    public function restoreStock(): void
    {
        foreach ($this->carts()->with('product')->get() as $line) {
            $line->product?->increment('stock', (int) $line->quantity);
        }
    }

    /**
     * Notify the customer behind this order: the account owner when there is
     * one, otherwise the guest checkout email.
     */
    public function notifyCustomer(\Illuminate\Notifications\Notification $notification): void
    {
        if ($this->user) {
            $this->user->notify($notification);

            return;
        }

        if ($this->email) {
            \Illuminate\Support\Facades\Notification::route('mail', $this->email)
                ->notify($notification);
        }
    }

    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    public function cart_info(): HasMany
    {
        return $this->hasMany(Cart::class, 'order_id', 'id');
    }
}
