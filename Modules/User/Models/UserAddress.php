<?php

declare(strict_types=1);

namespace Modules\User\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Modules\Core\Models\Core;

/**
 * Class UserAddress
 *
 * @property int $id
 * @property int $user_id
 * @property string $type
 * @property bool $is_default
 * @property string $first_name
 * @property string $last_name
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $company
 * @property string $country
 * @property string $city
 * @property string|null $state
 * @property string $address1
 * @property string|null $address2
 * @property string $post_code
 * @property string|null $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 *
 * @method static Builder<static>|UserAddress forUser(int $userId)
 * @method static Builder<static>|UserAddress default()
 * @method static Builder<static>|UserAddress shipping()
 * @method static Builder<static>|UserAddress billing()
 */
class UserAddress extends Core
{
    use HasFactory;

    protected $table = 'user_addresses';

    protected $casts = [
        'is_default' => 'boolean',
    ];

    protected $fillable = [
        'user_id',
        'type',
        'is_default',
        'first_name',
        'last_name',
        'email',
        'phone',
        'company',
        'country',
        'city',
        'state',
        'address1',
        'address2',
        'post_code',
        'notes',
    ];

    /**
     * Get the user that owns this address.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: Get addresses for a specific user.
     */
    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: Get default addresses.
     */
    public function scopeDefault(Builder $query): Builder
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope: Get shipping addresses.
     */
    public function scopeShipping(Builder $query): Builder
    {
        return $query->whereIn('type', ['shipping', 'both']);
    }

    /**
     * Scope: Get billing addresses.
     */
    public function scopeBilling(Builder $query): Builder
    {
        return $query->whereIn('type', ['billing', 'both']);
    }

    /**
     * Get full name attribute.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Get full address attribute.
     */
    public function getFullAddressAttribute(): string
    {
        $address = $this->address1;
        
        if ($this->address2) {
            $address .= ", {$this->address2}";
        }
        
        $address .= ", {$this->city}";
        
        if ($this->state) {
            $address .= ", {$this->state}";
        }
        
        $address .= " {$this->post_code}, {$this->country}";
        
        return $address;
    }

    /**
     * Set as default address for this user and type.
     */
    public function setAsDefault(): void
    {
        // Remove default from other addresses of same type for this user
        self::forUser($this->user_id)
            ->where('type', $this->type)
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);
        
        $this->update(['is_default' => true]);
    }
}
