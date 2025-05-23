<?php

declare(strict_types=1);

/**
 * Created by Zoran Shefot Bogoevski.
 */

namespace Modules\Category\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Models\Core;
use Modules\Post\Models\Post;

/**
 * Class CategoryProductFactory
 *
 * @property int $category_id
 * @property int $post_id
 * @property-read Category $category
 * @property-read Post $post
 *
 * @method static Builder<static>|CategoryPost newModelQuery()
 * @method static Builder<static>|CategoryPost newQuery()
 * @method static Builder<static>|CategoryPost query()
 * @method static Builder<static>|CategoryPost whereCategoryId($value)
 * @method static Builder<static>|CategoryPost wherePostId($value)
 *
 * @mixin Eloquent
 */
class CategoryPost extends Core
{
    protected $table = 'category_post';

    protected $casts
        = [
            'post_id' => 'int',
            'category_id' => 'int',
        ];

    protected $fillable
        = [
            'post_id',
            'category_id',
        ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}
