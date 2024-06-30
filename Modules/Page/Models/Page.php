<?php

/**
 * Created by Zoran Bogoevski Model.
 */

namespace Modules\Page\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\User\Models\User;

/**
 * Class Page
 *
 * @property int         $id
 * @property string      $title
 * @property string      $slug
 * @property string      $content
 * @property bool        $is_active
 * @property int         $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property User        $user
 *
 * @package App\Models
 */
class Page extends Model
{
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
