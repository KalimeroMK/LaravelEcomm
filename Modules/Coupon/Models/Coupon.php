<?php

/**
 * Created by Zoran Shefot Bogoevski.
 */

namespace Modules\Coupon\Models;

use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
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
 * @package App\Models
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
     * @param $code
     *
     * @return Model|Builder|Coupon|null
     */
    public static function findByCode($code): Model|Builder|Coupon|null
    {
        return self::where('code', $code)->first();
    }

    /**
     * @param $total
     *
     * @return float|int
     */
    public function discount($total): float|int
    {
        if ($this->type == "fixed") {
            return $this->value;
        } elseif ($this->type == "percent") {
            return ($this->value / 100) * $total;
        } else {
            return 0;
        }
    }
}
