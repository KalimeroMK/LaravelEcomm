<?php

/**
 * Created by Zoran Shefot Bogoevski.
 */

namespace Modules\Message\Models;

use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Core\Models\Core;
use Modules\Message\Database\Factories\MessageFactory;

/**
 * Class Message
 *
 * @property int $id
 * @property string $name
 * @property string $subject
 * @property string $email
 * @property string|null $photo
 * @property string|null $phone
 * @property string $message
 * @property Carbon|null $read_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @package App\Models
 * @method static Builder|Message newModelQuery()
 * @method static Builder|Message newQuery()
 * @method static Builder|Message query()
 * @method static Builder|Message whereCreatedAt($value)
 * @method static Builder|Message whereEmail($value)
 * @method static Builder|Message whereId($value)
 * @method static Builder|Message whereMessage($value)
 * @method static Builder|Message whereName($value)
 * @method static Builder|Message wherePhone($value)
 * @method static Builder|Message wherePhoto($value)
 * @method static Builder|Message whereReadAt($value)
 * @method static Builder|Message whereSubject($value)
 * @method static Builder|Message whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Message extends Core
{
    use HasFactory;

    protected $table = 'messages';

    protected $dates = [
        'read_at',
    ];

    protected $fillable = [
        'name',
        'subject',
        'email',
        'photo',
        'phone',
        'message',
        'read_at',
    ];

    /**
     * @return MessageFactory
     */
    public static function Factory(): MessageFactory
    {
        return MessageFactory::new();
    }
}
