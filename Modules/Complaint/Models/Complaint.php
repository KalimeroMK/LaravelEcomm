<?php

namespace Modules\Complaint\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Complaint\Database\Factories\ComplaintFactory;
use Modules\Core\Models\Core;
use Modules\Order\Models\Order;
use Modules\User\Models\User;

/**
 * Class Complaint
 *
 * @property int                         $id
 * @property int                         $user_id
 * @property int                         $order_id
 * @property string                      $status
 * @property string                      $description
 * @property Carbon|null                 $created_at
 * @property Carbon|null                 $updated_at
 *
 * @property Order                       $order
 * @property User                        $user
 * @property Collection|ComplaintReply[] $complaint_replies
 *
 * @package App\Models
 */
class Complaint extends Core
{
    use HasFactory;

    protected $table = 'complaints';

    protected $casts
        = [
            'user_id' => 'int',
            'order_id' => 'int'
        ];

    protected $fillable
        = [
            'user_id',
            'order_id',
            'status',
            'description'
        ];

    public static function Factory(): ComplaintFactory
    {
        return ComplaintFactory::new();
    }


    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function complaint_replies(): HasMany
    {
        return $this->hasMany(ComplaintReply::class);
    }

}
