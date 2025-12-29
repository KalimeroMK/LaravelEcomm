<?php

declare(strict_types=1);

namespace Modules\Billing\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Modules\Billing\Database\Factories\PaymentFactory;
use Modules\Core\Models\Core;
use Modules\Order\Models\Order;
use Modules\User\Models\User;

/**
 * @property int $id
 * @property int|null $order_id
 * @property int|null $invoice_id
 * @property int $user_id
 * @property string $payment_method
 * @property string $status
 * @property float $amount
 * @property string $currency
 * @property string|null $transaction_id
 * @property string|null $transaction_reference
 * @property string|null $notes
 * @property array|null $metadata
 * @property Carbon|null $processed_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Order|null $order
 * @property-read Invoice|null $invoice
 * @property-read User $user
 */
class Payment extends Core
{
    use HasFactory;

    protected $table = 'payments';

    protected $casts = [
        'order_id' => 'int',
        'invoice_id' => 'int',
        'user_id' => 'int',
        'amount' => 'decimal:2',
        'metadata' => 'array',
        'processed_at' => 'datetime',
    ];

    protected $fillable = [
        'order_id',
        'invoice_id',
        'user_id',
        'payment_method',
        'status',
        'amount',
        'currency',
        'transaction_id',
        'transaction_reference',
        'notes',
        'metadata',
        'processed_at',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    protected static function newFactory(): PaymentFactory
    {
        return PaymentFactory::new();
    }
}
