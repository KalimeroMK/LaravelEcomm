<?php

declare(strict_types=1);

/**
 * Created by Zoran Shefot Bogoevski.
 */

namespace Modules\Category\Models;

use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Models\Core;
use Modules\Post\Models\Post;
use Modules\Product\Models\Product;

/**
 * Class CategoryProductFactory
 *
 * @property int $id
 * @property int $product_id
 * @property int $category_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Category $category
 * @property Product $product
 *
 * @method static Builder|CategoryProduct newModelQuery()
 * @method static Builder|CategoryProduct newQuery()
 * @method static Builder|CategoryProduct query()
 * @method static Builder|CategoryProduct whereCategoryId($value)
 * @method static Builder|CategoryProduct whereCreatedAt($value)
 * @method static Builder|CategoryProduct whereId($value)
 * @method static Builder|CategoryProduct whereProductId($value)
 * @method static Builder|CategoryProduct whereUpdatedAt($value)
 *
 * @mixin Eloquent
 *
 * @property int $post_id
 * @property-read Post   $post
 *
 * @method static Builder|CategoryPost wherePostId($value)
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
