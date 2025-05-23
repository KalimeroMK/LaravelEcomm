<?php

declare(strict_types=1);

/**
 * Created by Zoran Bogoevski Model.
 */

namespace Modules\Page\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Models\Core;
use Modules\Core\Traits\HasSlug;
use Modules\Page\Database\Factories\PageFactory;
use Modules\User\Models\User;

/**
 * Class Page
 *
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string $content
 * @property bool $is_active
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read User $user
 *
 * @method static Builder<static>|Page newModelQuery()
 * @method static Builder<static>|Page newQuery()
 * @method static Builder<static>|Page query()
 * @method static Builder<static>|Page whereContent($value)
 * @method static Builder<static>|Page whereCreatedAt($value)
 * @method static Builder<static>|Page whereId($value)
 * @method static Builder<static>|Page whereIsActive($value)
 * @method static Builder<static>|Page whereSlug($value)
 * @method static Builder<static>|Page whereTitle($value)
 * @method static Builder<static>|Page whereUpdatedAt($value)
 * @method static Builder<static>|Page whereUserId($value)
 *
 * @mixin Eloquent
 */
class Page extends Core
{
    use HasFactory;
    use HasSlug;

    protected $table = 'pages';

    protected $casts
        = [
            'is_active' => 'bool',
            'user_id' => 'int',
        ];

    protected $fillable
        = [
            'title',
            'slug',
            'content',
            'is_active',
            'user_id',
        ];

    public static function Factory(): PageFactory
    {
        return PageFactory::new();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
