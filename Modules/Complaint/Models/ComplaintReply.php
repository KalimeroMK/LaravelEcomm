<?php

namespace Modules\Complaint\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Complaint\Database\Factories\ComplaintReplaiesFactory;
use Modules\Core\Models\Core;
use Modules\User\Models\User;

/**
 * Class ComplaintReply
 *
 * @property int         $id
 * @property int         $complaint_id
 * @property int         $user_id
 * @property string      $reply_content
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Complaint   $complaint
 * @property User        $user
 *
 * @package App\Models
 */
class ComplaintReply extends Core
{
    use hasFactory;

    protected $table = 'complaint_replies';

    protected $fillable
        = [
            'complaint_id',
            'user_id',
            'reply_content',
        ];

    protected function casts(): array
    {
        return [
            'complaint_id' => 'integer',
            'user_id' => 'integer',
            'reply_content' => 'string',
        ];
    }

    public static function Factory(): ComplaintReplaiesFactory
    {
        return ComplaintReplaiesFactory::new();
    }

    public function complaint(): BelongsTo
    {
        return $this->belongsTo(Complaint::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
