<?php

declare(strict_types=1);

/**
 * Created by Zoran Shefot Bogoevski.
 */

namespace Modules\Post\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Modules\Category\Models\Category;
use Modules\Core\Models\Core;
use Modules\Core\Traits\HasSlug;
use Modules\Post\Database\Factories\PostFactory;
use Modules\Tag\Models\Tag;
use Modules\User\Models\User;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Class Post
 *
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string $summary
 * @property string|null $description
 * @property string $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, PostComment>                $allComments
 * @property-read User                                        $author
 * @property-read \Kalnoy\Nestedset\Collection<int, Category> $categories
 * @property-read int|null                                    $categories_count
 * @property-read Collection<int, PostComment>                $comments
 * @property-read int|null                                    $comments_count
 * @property-read MediaCollection<int, Media>                 $media
 * @property-read int|null                                    $media_count
 * @property-read Collection<int, PostComment>                $post_comments
 * @property-read Collection<int, Tag>                        $tags
 *
 * @method static Builder<static>|Post newModelQuery()
 * @method static Builder<static>|Post newQuery()
 * @method static Builder<static>|Post query()
 * @method static Builder<static>|Post whereCreatedAt($value)
 * @method static Builder<static>|Post whereDescription($value)
 * @method static Builder<static>|Post whereId($value)
 * @method static Builder<static>|Post whereSlug($value)
 * @method static Builder<static>|Post whereStatus($value)
 * @method static Builder<static>|Post whereSummary($value)
 * @method static Builder<static>|Post whereTitle($value)
 * @method static Builder<static>|Post whereUpdatedAt($value)
 * @method static Builder<static>|Post whereUserId($value)
 *
 * @mixin Eloquent
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
            'quote',
            'author.name',
            'status',
            'user_id',
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

    /**
     * Get quote attribute with fallback to null.
     */
    public function getQuoteAttribute(): ?string
    {
        return $this->attributes['quote'] ?? null;
    }

    public function getImageUrlAttribute(): ?string
    {
        $mediaItem = $this->getFirstMedia('post');
        if ($mediaItem instanceof Media) {
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
