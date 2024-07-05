<?php

/**
 * Created by Zoran Bogoevski Model.
 */

namespace Modules\Page\Models;

use Carbon\Carbon;
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
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property User $user
 *
 * @package App\Models
 */
class Page extends Core
{
    use HasSlug;
    use hasFactory;

    protected $table = 'pages';

    protected $casts
        = [
            'is_active' => 'bool',
            'user_id' => 'int'
        ];

    protected $fillable
        = [
            'title',
            'slug',
            'content',
            'is_active',
            'user_id'
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
