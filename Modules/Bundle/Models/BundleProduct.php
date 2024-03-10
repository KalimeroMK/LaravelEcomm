<?php

/**
 * Created by Zoran Bogoevski.
 */

namespace Modules\Bundle\Models;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Bundle\Database\Factories\BundleProductFactory;
use Modules\Product\Models\Product;

/**
 * Class BundleProduct
 *
 * @property int $bundle_id
 * @property int $product_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Bundle $bundle
 * @property Product $product
 *
 * @package App\Models
 */
class BundleProduct extends Model
{
    protected $table = 'bundle_product';
    public $incrementing = false;

    protected $casts = [
        'bundle_id' => 'int',
        'product_id' => 'int'
    ];


    /**
     * @return BundleProductFactory
     */
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
