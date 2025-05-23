<?php

declare(strict_types=1);

/**
 * Created by Zoran Shefot Bogoevski.
 */

namespace Modules\Post\Models;

use Eloquent;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\Core\Models\Core;
use Modules\Post\Database\Factories\PostCommentFactory;
use Modules\User\Models\User;

/**
 * Class PostComment
 *
 * @property int $id
 * @property string $comment
 * @property string $status
 * @property string|null $replied_comment
 * @property int|null $parent_id
 * @property int|null $user_id
 * @property int|null $post_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Post|null                    $post
 * @property-read Collection<int, PostComment> $replies
 * @property-read int|null                     $replies_count
 * @property-read User|null                    $user
 * @property-read User|null                    $user_info
 *
 * @method static Builder<static>|PostComment newModelQuery()
 * @method static Builder<static>|PostComment newQuery()
 * @method static Builder<static>|PostComment query()
 * @method static Builder<static>|PostComment whereComment($value)
 * @method static Builder<static>|PostComment whereCreatedAt($value)
 * @method static Builder<static>|PostComment whereId($value)
 * @method static Builder<static>|PostComment whereParentId($value)
 * @method static Builder<static>|PostComment wherePostId($value)
 * @method static Builder<static>|PostComment whereRepliedComment($value)
 * @method static Builder<static>|PostComment whereStatus($value)
 * @method static Builder<static>|PostComment whereUpdatedAt($value)
 * @method static Builder<static>|PostComment whereUserId($value)
 *
 * @mixin Eloquent
 */
class PostComment extends Core
{
    use HasFactory;

    protected $table = 'post_comments';

    protected $casts = [
        'user_id' => 'int',
        'post_id' => 'int',
        'parent_id' => 'int',
    ];

    protected $fillable = [
        'user_id',
        'post_id',
        'comment',
        'status',
        'replied_comment',
        'parent_id',
    ];

    public static function Factory(): PostCommentFactory
    {
        return PostCommentFactory::new();
    }

    public static function getAllUserComments(): LengthAwarePaginator
    {
        return self::where('user_id', auth()->user()->id)->with('user_info')->paginate(10);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function user_info(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->where('status', 'active');
    }
}
