<?php

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
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Category\Models\Category;
use Modules\Core\Models\Core;
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
 * @package App\Models
 * @property-read Collection|PostComment[] $allComments
 * @property-read int|null $all_comments_count
 * @property-read User|null $author
 * @property-read Collection|PostComment[] $fpost_comments
 * @property-read int|null $fpost_comments_count
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
 * @mixin Eloquent
 * @property-read int|null $post_comments_count
 * @property-read \Kalnoy\Nestedset\Collection|Category[] $categories
 * @property-read int|null $categories_count
 * @property-read Collection|PostComment[] $comments
 * @property-read int|null $comments_count
 * @property-read string $image_url
 * @property-read int|null $post_tag_count
 */
class Post extends Core implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $table = 'posts';

    protected $casts = [
        'added_by' => 'int',
    ];

    protected $fillable = [
        'title',
        'slug',
        'summary',
        'description',
        'quote',
        'photo',
        'author.name',
        'status',
    ];

    public const likeRows = [
        'title',
        'slug',
        'summary',
        'description',
        'quote',
        'photo',
        'added_by',
        'status',
    ];

    public static function Factory(): PostFactory
    {
        return PostFactory::new();
    }

    /**
     * @param string $slug
     *
     * @return LengthAwarePaginator
     */
    public static function getBlogByTag(string $slug): LengthAwarePaginator
    {
        return Post::where('tags', $slug)->paginate(8);
    }

    /**
     * @param string $slug
     *
     * @return Builder|Model
     */
    public static function getPostBySlug(string $slug): Model|Builder
    {
        return Post::whereSlug($slug)->with('comments')->firstOrFail();
    }

    /**
     * @return int
     */
    public static function countActivePost(): int
    {
        $data = Post::where('status', 'active')->count();
        if ($data) {
            return $data;
        }

        return 0;
    }

    /**
     * @return BelongsToMany
     */
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
            return $mediaItem->first()->getUrl();
        }

        return 'https://via.placeholder.com/640x480.png/003311?text=et';
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('preview')
            ->fit(Fit::Contain, 300, 300)
            ->nonQueued();
    }

    /**
     * @param string $slug
     *
     * @return mixed|string
     */
    public function incrementSlug(string $slug): mixed
    {
        $original = $slug;
        $count = 2;
        while (static::whereSlug($slug)->exists()) {
            $slug = "{$original}-" . $count++;
        }

        return $slug;
    }

    /**
     * Configure the factory to create a post with categories and tags.
     */
    public function withCategoriesAndTags(): PostFactory
    {
        return $this->afterCreating(function (Post $post) {
            $categories = Category::factory()->count(3)->create();
            $post->categories()->attach($categories);

            $tags = Tag::factory()->count(5)->create();
            $post->tags()->attach($tags);
        });
    }


    public function post_comments(): HasMany
    {
        return $this->hasMany(PostComment::class);
    }


}
