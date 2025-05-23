<?php

declare(strict_types=1);

namespace Modules\Complaint\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Complaint\Database\Factories\ComplaintReplaiesFactory;
use Modules\Core\Models\Core;
use Modules\User\Models\User;

/**
 * Class ComplaintReply
 *
 * @property int $id
 * @property int $complaint_id
 * @property int $user_id
 * @property string $reply_content
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Complaint                  $complaint
 * @property-read User                       $user
 *
 * @method static Builder<static>|ComplaintReply newModelQuery()
 * @method static Builder<static>|ComplaintReply newQuery()
 * @method static Builder<static>|ComplaintReply query()
 * @method static Builder<static>|ComplaintReply whereComplaintId($value)
 * @method static Builder<static>|ComplaintReply whereCreatedAt($value)
 * @method static Builder<static>|ComplaintReply whereId($value)
 * @method static Builder<static>|ComplaintReply whereReplyContent($value)
 * @method static Builder<static>|ComplaintReply whereUpdatedAt($value)
 * @method static Builder<static>|ComplaintReply whereUserId($value)
 *
 * @mixin Eloquent
 */
class ComplaintReply extends Core
{
    use HasFactory;

    protected $table = 'complaint_replies';

    protected $fillable
        = [
            'complaint_id',
            'user_id',
            'reply_content',
        ];

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

    protected function casts(): array
    {
        return [
            'complaint_id' => 'integer',
            'user_id' => 'integer',
            'reply_content' => 'string',
        ];
    }
}
