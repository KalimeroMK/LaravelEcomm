<?php

declare(strict_types=1);

/**
 * Created by Zoran Shefot Bogoevski.
 */

namespace Modules\Post\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Category\Models\Category;
use Modules\Core\Models\Core;
use Modules\Core\Traits\HasSlug;
use Modules\Post\Database\Factories\PostFactory;
use Modules\Tag\Models\Tag;
use Modules\User\Models\User;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Class Post
 *
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string $summary
 * @property string|null $description
 * @property string|null $quote
 * @property string|null $photo
 * @property string|null $tags
 * @property int|null $post_cat_id
 * @property int|null $post_tag_id
 * @property int|null $added_by
 * @property string $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property User|null $user
 * @property Tag|null $post_tag
 * @property Collection|PostComment[] $post_comments
 * @property-read Collection|PostComment[]                $allComments
 * @property-read int|null                                $all_comments_count
 * @property-read User|null                               $author
 * @property-read Collection|PostComment[]                $fpost_comments
 * @property-read int|null                                $fpost_comments_count
 *
 * @method static Builder|Post newModelQuery()
 * @method static Builder|Post newQuery()
 * @method static Builder|Post query()
 * @method static Builder|Post whereAddedBy($value)
 * @method static Builder|Post whereCreatedAt($value)
 * @method static Builder|Post whereDescription($value)
 * @method static Builder|Post whereId($value)
 * @method static Builder|Post wherePhoto($value)
 * @method static Builder|Post wherePostCatId($value)
 * @method static Builder|Post wherePostTagId($value)
 * @method static Builder|Post whereQuote($value)
 * @method static Builder|Post whereSlug($value)
 * @method static Builder|Post whereStatus($value)
 * @method static Builder|Post whereSummary($value)
 * @method static Builder|Post whereTags($value)
 * @method static Builder|Post whereTitle($value)
 * @method static Builder|Post whereUpdatedAt($value)
 *
 * @mixin Eloquent
 *
 * @property-read int|null                                $post_comments_count
 * @property-read \Kalnoy\Nestedset\Collection|Category[] $categories
 * @property-read int|null                                $categories_count
 * @property-read Collection|PostComment[]                $comments
 * @property-read int|null                                $comments_count
 * @property-read string                                  $image_url
 * @property-read int|null                                $post_tag_count
 */
class Post extends Core implements HasMedia
{
    use HasFactory;
    use HasSlug;
    use InteractsWithMedia;

    public const likeRows
        = [
            'title',
            'slug',
            'summary',
            'description',
            'photo',
            'added_by',
            'status',
        ];

    protected $table = 'posts';

    protected $casts
        = [
            'added_by' => 'int',
        ];

    protected $fillable
        = [
            'title',
            'slug',
            'summary',
            'description',
            'photo',
            'author.name',
            'status',
        ];

    public static function Factory(): PostFactory
    {
        return PostFactory::new();
    }

    public static function getPostBySlug(string $slug): Model|Builder
    {
        return self::whereSlug($slug)->with('comments')->firstOrFail();
    }

    public static function countActivePost(): int
    {
        $data = self::where('status', 'active')->count();
        if ($data) {
            return $data;
        }

        return 0;
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'post_tag', 'post_id', 'tag_id');
    }

    public function postComments(): HasMany
    {
        return $this->hasMany(PostComment::class);
    }

    public function allComments(): HasMany
    {
        return $this->hasMany(PostComment::class)->where('status', 'active');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(PostComment::class)->whereNull('parent_id')->orderBy('id', 'DESC');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function getImageUrlAttribute(): ?string
    {
        $mediaItem = $this->getFirstMedia('post');
        if ($mediaItem) {
            return $mediaItem->getUrl();
        }

        return 'https://placehold.co/600x400@2x.png';
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('preview')
            ->fit(Fit::Contain, 300, 300)
            ->nonQueued();
    }

    public function post_comments(): HasMany
    {
        return $this->hasMany(PostComment::class);
    }
}
