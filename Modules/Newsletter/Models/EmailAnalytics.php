<?php

declare(strict_types=1);

namespace Modules\Newsletter\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Modules\Core\Models\Core;
use Modules\User\Models\User;

/**
 * Class EmailAnalytics
 *
 * @property int $id
 * @property string $email_type
 * @property string $email_subject
 * @property string $recipient_email
 * @property int|null $user_id
 * @property string|null $campaign_id
 * @property Carbon $sent_at
 * @property Carbon|null $opened_at
 * @property Carbon|null $clicked_at
 * @property string|null $clicked_url
 * @property bool $bounced
 * @property bool $unsubscribed
 * @property Carbon|null $unsubscribed_at
 * @property array|null $metadata
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User|null $user
 *
 * @method static Builder<static>|EmailAnalytics newModelQuery()
 * @method static Builder<static>|EmailAnalytics newQuery()
 * @method static Builder<static>|EmailAnalytics query()
 * @method static Builder<static>|EmailAnalytics whereId($value)
 * @method static Builder<static>|EmailAnalytics whereEmailType($value)
 * @method static Builder<static>|EmailAnalytics whereEmailSubject($value)
 * @method static Builder<static>|EmailAnalytics whereRecipientEmail($value)
 * @method static Builder<static>|EmailAnalytics whereUserId($value)
 * @method static Builder<static>|EmailAnalytics whereCampaignId($value)
 * @method static Builder<static>|EmailAnalytics whereSentAt($value)
 * @method static Builder<static>|EmailAnalytics whereOpenedAt($value)
 * @method static Builder<static>|EmailAnalytics whereClickedAt($value)
 * @method static Builder<static>|EmailAnalytics whereClickedUrl($value)
 * @method static Builder<static>|EmailAnalytics whereBounced($value)
 * @method static Builder<static>|EmailAnalytics whereUnsubscribed($value)
 * @method static Builder<static>|EmailAnalytics whereUnsubscribedAt($value)
 * @method static Builder<static>|EmailAnalytics whereMetadata($value)
 * @method static Builder<static>|EmailAnalytics whereCreatedAt($value)
 * @method static Builder<static>|EmailAnalytics whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
class EmailAnalytics extends Core
{
    use HasFactory;

    protected $table = 'email_analytics';

    protected $casts = [
        'user_id' => 'int',
        'sent_at' => 'datetime',
        'opened_at' => 'datetime',
        'clicked_at' => 'datetime',
        'bounced' => 'bool',
        'unsubscribed' => 'bool',
        'unsubscribed_at' => 'datetime',
        'metadata' => 'array',
    ];

    protected $fillable = [
        'email_type',
        'email_subject',
        'recipient_email',
        'user_id',
        'campaign_id',
        'sent_at',
        'opened_at',
        'clicked_at',
        'clicked_url',
        'bounced',
        'unsubscribed',
        'unsubscribed_at',
        'metadata',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mark email as opened
     */
    public function markAsOpened(): void
    {
        if (! $this->opened_at) {
            $this->update(['opened_at' => now()]);
        }
    }

    /**
     * Mark email as clicked
     */
    public function markAsClicked(?string $url = null): void
    {
        $this->update([
            'clicked_at' => now(),
            'clicked_url' => $url,
        ]);
    }

    /**
     * Mark email as bounced
     */
    public function markAsBounced(): void
    {
        $this->update(['bounced' => true]);
    }

    /**
     * Mark email as unsubscribed
     */
    public function markAsUnsubscribed(): void
    {
        $this->update([
            'unsubscribed' => true,
            'unsubscribed_at' => now(),
        ]);
    }

    /**
     * Scope for specific email type
     */
    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('email_type', $type);
    }

    /**
     * Scope for specific campaign
     */
    public function scopeOfCampaign(Builder $query, string $campaignId): Builder
    {
        return $query->where('campaign_id', $campaignId);
    }

    /**
     * Scope for date range
     */
    public function scopeDateRange(Builder $query, Carbon $from, Carbon $to): Builder
    {
        return $query->whereBetween('sent_at', [$from, $to]);
    }

    /**
     * Scope for opened emails
     */
    public function scopeOpened(Builder $query): Builder
    {
        return $query->whereNotNull('opened_at');
    }

    /**
     * Scope for clicked emails
     */
    public function scopeClicked(Builder $query): Builder
    {
        return $query->whereNotNull('clicked_at');
    }

    /**
     * Scope for bounced emails
     */
    public function scopeBounced(Builder $query): Builder
    {
        return $query->where('bounced', true);
    }

    /**
     * Scope for unsubscribed emails
     */
    public function scopeUnsubscribed(Builder $query): Builder
    {
        return $query->where('unsubscribed', true);
    }
}
