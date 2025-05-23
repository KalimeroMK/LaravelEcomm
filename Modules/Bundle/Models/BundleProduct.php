<?php

declare(strict_types=1);

/**
 * Created by Zoran Bogoevski.
 */

namespace Modules\Bundle\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Bundle\Database\Factories\BundleProductFactory;
use Modules\Product\Models\Product;

/**
 * Class BundleProduct
 *
 * @property int $product_id
 * @property int $bundle_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Bundle                     $bundle
 * @property-read Product                    $product
 *
 * @method static Builder<static>|BundleProduct newModelQuery()
 * @method static Builder<static>|BundleProduct newQuery()
 * @method static Builder<static>|BundleProduct query()
 * @method static Builder<static>|BundleProduct whereBundleId($value)
 * @method static Builder<static>|BundleProduct whereCreatedAt($value)
 * @method static Builder<static>|BundleProduct whereProductId($value)
 * @method static Builder<static>|BundleProduct whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
class BundleProduct extends Model
{
    public $incrementing = false;

    protected $table = 'bundle_product';

    protected $casts = [
        'bundle_id' => 'int',
        'product_id' => 'int',
    ];

    public static function Factory(): BundleProductFactory
    {
        return BundleProductFactory::new();
    }

    public function bundle(): BelongsTo
    {
        return $this->belongsTo(Bundle::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
