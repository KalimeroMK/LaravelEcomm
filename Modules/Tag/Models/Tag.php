<?php

/**
 * Created by Zoran Shefot Bogoevski.
 */

namespace Modules\Tag\Models;

use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
 * @property Collection|Post[] $posts
 * @property-read int|null $posts_count
 *
 * @method static Builder|Tag newModelQuery()
 * @method static Builder|Tag newQuery()
 * @method static Builder|Tag query()
 * @method static Builder|Tag whereCreatedAt($value)
 * @method static Builder|Tag whereId($value)
 * @method static Builder|Tag whereSlug($value)
 * @method static Builder|Tag whereStatus($value)
 * @method static Builder|Tag whereTitle($value)
 * @method static Builder|Tag whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
class Tag extends Core
{
    use HasFactory;
    use HasSlug;

    protected $table = 'tags';

    protected $fillable = [
        'title',
        'slug',
        'status',
    ];

    public static function Factory(): TagFactory
    {
        return TagFactory::new();
    }

    public function product(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_tag', 'tag_id', 'post_id');
    }
}
