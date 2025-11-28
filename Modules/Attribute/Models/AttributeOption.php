<?php

declare(strict_types=1);

namespace Modules\Attribute\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $attribute_id
 * @property string $value
 * @property string|null $label
 * @property int $sort_order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Attribute                  $attribute
 *
 * @method static Builder<static>|AttributeOption newModelQuery()
 * @method static Builder<static>|AttributeOption newQuery()
 * @method static Builder<static>|AttributeOption query()
 * @method static Builder<static>|AttributeOption whereAttributeId($value)
 * @method static Builder<static>|AttributeOption whereCreatedAt($value)
 * @method static Builder<static>|AttributeOption whereId($value)
 * @method static Builder<static>|AttributeOption whereLabel($value)
 * @method static Builder<static>|AttributeOption whereSortOrder($value)
 * @method static Builder<static>|AttributeOption whereUpdatedAt($value)
 * @method static Builder<static>|AttributeOption whereValue($value)
 *
 * @mixin Eloquent
 */
class AttributeOption extends Model
{
    protected $table = 'attribute_options';

    protected $fillable = [
        'attribute_id',
        'value',
        'label',
        'sort_order',
    ];

    /**
     * @return BelongsTo<Attribute, AttributeOption>
     */
    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class);
    }
}
