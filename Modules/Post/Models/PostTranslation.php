<?php

declare(strict_types=1);

namespace Modules\Post\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Models\Core;

/**
 * @property int $id
 * @property int $post_id
 * @property string $locale
 * @property string|null $title
 * @property string|null $slug
 * @property string|null $summary
 * @property string|null $content
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property string|null $meta_keywords
 * @property-read \Modules\Post\Models\Post $post
 */
class PostTranslation extends Core
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'locale',
        'title',
        'slug',
        'summary',
        'content',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}
