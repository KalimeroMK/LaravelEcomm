<?php

declare(strict_types=1);

namespace Modules\Product\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Models\Core;

/**
 * @property int $id
 * @property int $product_id
 * @property string $locale
 * @property string|null $name
 * @property string|null $summary
 * @property string|null $description
 * @property string|null $slug
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property-read \Modules\Product\Models\Product $product
 */
class ProductTranslation extends Core
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'locale',
        'name',
        'summary',
        'description',
        'slug',
        'meta_title',
        'meta_description',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
