<?php

declare(strict_types=1);

namespace Modules\Newsletter\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Modules\Core\Models\Core;

/**
 * Class EmailTemplate
 *
 * @property int $id
 * @property string $name
 * @property string $subject
 * @property string $html_content
 * @property string $text_content
 * @property string $template_type
 * @property bool $is_active
 * @property bool $is_default
 * @property array|null $settings
 * @property array|null $preview_data
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|EmailAnalytics[] $emailAnalytics
 *
 * @method static Builder<static>|EmailTemplate newModelQuery()
 * @method static Builder<static>|EmailTemplate newQuery()
 * @method static Builder<static>|EmailTemplate query()
 * @method static Builder<static>|EmailTemplate whereId($value)
 * @method static Builder<static>|EmailTemplate whereName($value)
 * @method static Builder<static>|EmailTemplate whereSubject($value)
 * @method static Builder<static>|EmailTemplate whereHtmlContent($value)
 * @method static Builder<static>|EmailTemplate whereTextContent($value)
 * @method static Builder<static>|EmailTemplate whereTemplateType($value)
 * @method static Builder<static>|EmailTemplate whereIsActive($value)
 * @method static Builder<static>|EmailTemplate whereIsDefault($value)
 * @method static Builder<static>|EmailTemplate whereSettings($value)
 * @method static Builder<static>|EmailTemplate wherePreviewData($value)
 * @method static Builder<static>|EmailTemplate whereCreatedAt($value)
 * @method static Builder<static>|EmailTemplate whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
class EmailTemplate extends Core
{
    use HasFactory;

    protected $table = 'email_templates';

    protected $casts = [
        'is_active' => 'bool',
        'is_default' => 'bool',
        'settings' => 'array',
        'preview_data' => 'array',
    ];

    protected $fillable = [
        'name',
        'subject',
        'html_content',
        'text_content',
        'template_type',
        'is_active',
        'is_default',
        'settings',
        'preview_data',
    ];

    /**
     * Get default template for type
     */
    public static function getDefaultForType(string $type): ?self
    {
        return static::where('template_type', $type)
            ->where('is_default', true)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Get template types
     */
    public static function getTemplateTypes(): array
    {
        return [
            'newsletter' => 'Newsletter',
            'abandoned_cart' => 'Abandoned Cart',
            'welcome' => 'Welcome Email',
            'order_confirmation' => 'Order Confirmation',
            'promotional' => 'Promotional',
            'custom' => 'Custom',
        ];
    }

    public function emailAnalytics(): HasMany
    {
        return $this->hasMany(EmailAnalytics::class, 'email_type', 'template_type');
    }

    /**
     * Scope for active templates
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for specific template type
     */
    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('template_type', $type);
    }

    /**
     * Set as default template
     */
    public function setAsDefault(): void
    {
        // Remove default from other templates of same type
        static::where('template_type', $this->template_type)
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);

        $this->update(['is_default' => true]);
    }

    /**
     * Get template preview URL
     */
    public function getPreviewUrl(): string
    {
        return route('admin.email-templates.preview', $this->id);
    }
}
