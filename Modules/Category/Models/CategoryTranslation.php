<?php

declare(strict_types=1);

namespace Modules\Category\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Models\Core;

/**
 * @property int $id
 * @property int $category_id
 * @property string $locale
 * @property string|null $title
 * @property string|null $slug
 * @property string|null $summary
 * @property string|null $description
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property-read \Modules\Category\Models\Category $category
 */
class CategoryTranslation extends Core
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'locale',
        'title',
        'slug',
        'summary',
        'description',
        'meta_title',
        'meta_description',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
