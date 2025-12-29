<?php

declare(strict_types=1);

namespace Modules\Billing\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Modules\Billing\Database\Factories\InvoiceFactory;
use Modules\Core\Models\Core;
use Modules\Order\Models\Order;
use Modules\User\Models\User;

/**
 * @property int $id
 * @property string $invoice_number
 * @property int|null $order_id
 * @property int $user_id
 * @property string $status
 * @property Carbon $issue_date
 * @property Carbon $due_date
 * @property Carbon|null $paid_date
 * @property float $subtotal
 * @property float $tax_amount
 * @property float $discount_amount
 * @property float $total_amount
 * @property string|null $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Order|null $order
 * @property-read User $user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Payment> $payments
 */
class Invoice extends Core
{
    use HasFactory;

    protected $table = 'invoices';

    protected $casts = [
        'order_id' => 'int',
        'user_id' => 'int',
        'issue_date' => 'date',
        'due_date' => 'date',
        'paid_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    protected $fillable = [
        'invoice_number',
        'order_id',
        'user_id',
        'status',
        'issue_date',
        'due_date',
        'paid_date',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'notes',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function isOverdue(): bool
    {
        return $this->status !== 'paid' && $this->due_date->isPast();
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    protected static function newFactory(): InvoiceFactory
    {
        return InvoiceFactory::new();
    }
}
