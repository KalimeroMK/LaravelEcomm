<?php

declare(strict_types=1);

namespace Modules\Page\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Models\Core;

/**
 * @property int $id
 * @property int $page_id
 * @property string $locale
 * @property string|null $title
 * @property string|null $slug
 * @property string|null $description
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property string|null $meta_keywords
 * @property-read \Modules\Page\Models\Page $page
 */
class PageTranslation extends Core
{
    use HasFactory;

    protected $fillable = [
        'page_id',
        'locale',
        'title',
        'slug',
        'description',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }
}
