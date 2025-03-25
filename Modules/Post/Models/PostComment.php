<?php

declare(strict_types=1);

/**
 * Created by Zoran Shefot Bogoevski.
 */

namespace Modules\Post\Models;

use Carbon\Carbon;
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
 * @property int|null $user_id
 * @property int|null $post_id
 * @property string $comment
 * @property string $status
 * @property string|null $replied_comment
 * @property int|null $parent_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Post|null $post
 * @property User|null $user
 * @property-read Collection|PostComment[] $replies
 * @property-read int|null $replies_count
 *
 * @method static Builder|PostComment newModelQuery()
 * @method static Builder|PostComment newQuery()
 * @method static Builder|PostComment query()
 * @method static Builder|PostComment whereComment($value)
 * @method static Builder|PostComment whereCreatedAt($value)
 * @method static Builder|PostComment whereId($value)
 * @method static Builder|PostComment whereParentId($value)
 * @method static Builder|PostComment wherePostId($value)
 * @method static Builder|PostComment whereRepliedComment($value)
 * @method static Builder|PostComment whereStatus($value)
 * @method static Builder|PostComment whereUpdatedAt($value)
 * @method static Builder|PostComment whereUserId($value)
 *
 * @mixin Eloquent
 *
 * @property-read User|null $user_info
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
