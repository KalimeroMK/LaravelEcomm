<?php

declare(strict_types=1);

/**
 * Created by Zoran Shefot Bogoevski.
 */

namespace Modules\Category\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Category\Database\Factories\CategoryProductFactory;
use Modules\Core\Models\Core;
use Modules\Product\Models\Product;

/**
 * Class CategoryProductFactory
 *
 * @property int $category_id
 * @property int $product_id
 * @property-read Category $category
 * @property-read Product $product
 *
 * @method static Builder<static>|CategoryProduct newModelQuery()
 * @method static Builder<static>|CategoryProduct newQuery()
 * @method static Builder<static>|CategoryProduct query()
 * @method static Builder<static>|CategoryProduct whereCategoryId($value)
 * @method static Builder<static>|CategoryProduct whereProductId($value)
 *
 * @mixin Eloquent
 */
class CategoryProduct extends Core
{
    protected $table = 'category_product';

    protected $casts = [
        'product_id' => 'int',
        'category_id' => 'int',
    ];

    protected $fillable = [
        'product_id',
        'category_id',
    ];

    public static function Factory(): CategoryProductFactory
    {
        return CategoryProductFactory::new();
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
