<?php

declare(strict_types=1);

namespace Modules\Attribute\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;
use Modules\Attribute\Database\Factories\AttributeGroupFactory;

/**
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Attribute> $attributes
 * @property-read int|null                   $attributes_count
 *
 * @method static Builder<static>|AttributeGroup newModelQuery()
 * @method static Builder<static>|AttributeGroup newQuery()
 * @method static Builder<static>|AttributeGroup query()
 * @method static Builder<static>|AttributeGroup whereCreatedAt($value)
 * @method static Builder<static>|AttributeGroup whereId($value)
 * @method static Builder<static>|AttributeGroup whereName($value)
 * @method static Builder<static>|AttributeGroup whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
class AttributeGroup extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public static function Factory(): AttributeGroupFactory
    {
        return AttributeGroupFactory::new();
    }

    public function attributes(): BelongsToMany
    {
        return $this->belongsToMany(Attribute::class, 'attribute_attribute_group', 'attribute_group_id',
            'attribute_id');
    }
}
