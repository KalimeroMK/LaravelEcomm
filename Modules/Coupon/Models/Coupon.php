<?php

/**
 * Created by Zoran Shefot Bogoevski.
 */

namespace Modules\Coupon\Models;

use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Modules\Core\Models\Core;

/**
 * Class Coupon
 *
 * @property int $id
 * @property string $code
 * @property string $type
 * @property float $value
 * @property string $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|Coupon newModelQuery()
 * @method static Builder|Coupon newQuery()
 * @method static Builder|Coupon query()
 * @method static Builder|Coupon whereCode($value)
 * @method static Builder|Coupon whereCreatedAt($value)
 * @method static Builder|Coupon whereId($value)
 * @method static Builder|Coupon whereStatus($value)
 * @method static Builder|Coupon whereType($value)
 * @method static Builder|Coupon whereUpdatedAt($value)
 * @method static Builder|Coupon whereValue($value)
 *
 * @mixin Eloquent
 */
class Coupon extends Core
{
    protected $table = 'coupons';

    protected $casts = [
        'value' => 'float',
    ];

    protected $fillable = [
        'code',
        'type',
        'value',
        'status',
    ];

    /**
     * Calculates the discount based on the total amount and the coupon type.
     *
     * @param  float  $total  The total amount on which the discount is to be applied.
     * @return float The amount of discount.
     */
    public function discount(float $total): float
    {
        if ($this->type == 'fixed') {
            // Assuming 'value' is the fixed discount amount.
            return min($this->value, $total); // Ensure discount does not exceed the total.
        } elseif ($this->type == 'percent') {
            // Assuming 'value' is the percentage discount.
            return $total * ($this->value / 100);
        }

        return 0; // No discount applicable.
    }
}
