<?php

namespace Modules\Newsletter\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Core\Models\Core;
use Modules\Newsletter\Database\Factories\NewsletterFactory;

/**
 * Class Newsletter
 *
 * @property int $id
 * @property string $email
 * @property string|null $token
 * @property bool $is_validated
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class Newsletter extends Core
{
    use HasFactory;

    protected $table = 'newsletters';

    protected $casts = [
        'is_validated' => 'bool',
    ];

    protected $hidden = [
        'token',
    ];

    protected $fillable = [
        'email',
        'token',
        'is_validated',
    ];

    /**
     * @return NewsletterFactory
     */
    public static function Factory(): NewsletterFactory
    {
        return NewsletterFactory::new();
    }

}