<?php

declare(strict_types=1);

/**
 * Created by Zoran Shefot Bogoevski.
 */

namespace Modules\Tag\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;
use Modules\Core\Models\Core;
use Modules\Core\Traits\HasSlug;
use Modules\Post\Models\Post;
use Modules\Product\Models\Product;
use Modules\Tag\Database\Factories\TagFactory;

/**
 * Class Tag
 *
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Post>      $posts
 * @property-read int|null                   $posts_count
 * @property-read Collection<int, Product>   $product
 * @property-read int|null                   $product_count
 *
 * @method static Builder<static>|Tag newModelQuery()
 * @method static Builder<static>|Tag newQuery()
 * @method static Builder<static>|Tag query()
 * @method static Builder<static>|Tag whereCreatedAt($value)
 * @method static Builder<static>|Tag whereId($value)
 * @method static Builder<static>|Tag whereSlug($value)
 * @method static Builder<static>|Tag whereStatus($value)
 * @method static Builder<static>|Tag whereTitle($value)
 * @method static Builder<static>|Tag whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
class Tag extends Core
{
    use HasFactory;
    use HasSlug;

    protected $table = 'tags';

    protected $fillable
        = [
            'title',
            'slug',
            'status',
        ];

    public static function Factory(): TagFactory
    {
        return TagFactory::new();
    }

    public function product(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_tag');
    }

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_tag');
    }
}
