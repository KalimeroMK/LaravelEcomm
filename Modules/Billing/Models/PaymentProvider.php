<?php

declare(strict_types=1);

namespace Modules\Billing\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Modules\Core\Models\Core;

/**
 * @property int $id
 * @property string $name
 * @property string|null $public_key
 * @property string|null $secret_key
 * @property int $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder<static>|PaymentProvider newModelQuery()
 * @method static Builder<static>|PaymentProvider newQuery()
 * @method static Builder<static>|PaymentProvider query()
 * @method static Builder<static>|PaymentProvider whereCreatedAt($value)
 * @method static Builder<static>|PaymentProvider whereId($value)
 * @method static Builder<static>|PaymentProvider whereName($value)
 * @method static Builder<static>|PaymentProvider wherePublicKey($value)
 * @method static Builder<static>|PaymentProvider whereSecretKey($value)
 * @method static Builder<static>|PaymentProvider whereStatus($value)
 * @method static Builder<static>|PaymentProvider whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
class PaymentProvider extends Core
{
    protected $table = 'payment_provider';

    protected $fillable
        = [
            'name',
            'public_key',
            'secret_key',
            'status',
        ];
}
