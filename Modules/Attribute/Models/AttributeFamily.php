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
use Modules\Attribute\Database\Factories\AttributeFamilyFactory;

/**
 * Class AttributeFamily
 *
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string|null $description
 * @property bool $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Attribute> $attributes
 * @property-read Collection<int, AttributeGroup> $groups
 *
 * @method static Builder<static>|AttributeFamily newModelQuery()
 * @method static Builder<static>|AttributeFamily newQuery()
 * @method static Builder<static>|AttributeFamily query()
 * @method static Builder<static>|AttributeFamily whereCode($value)
 * @method static Builder<static>|AttributeFamily whereIsActive($value)
 * @method static Builder<static>|AttributeFamily active()
 *
 * @mixin Eloquent
 */
class AttributeFamily extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public static function factory(): AttributeFamilyFactory
    {
        return AttributeFamilyFactory::new();
    }

    /**
     * Get default family (first active or create one)
     */
    public static function getDefault(): self
    {
        return self::active()->first() ?? self::create([
            'name' => 'Default',
            'code' => 'default',
            'description' => 'Default attribute family',
        ]);
    }

    /**
     * Get all attributes in this family
     *
     * @return BelongsToMany<Attribute, AttributeFamily>
     */
    public function attributes(): BelongsToMany
    {
        return $this->belongsToMany(Attribute::class, 'attribute_family_attributes')
            ->withPivot(['attribute_group_id', 'position', 'is_required'])
            ->orderBy('attribute_family_attributes.position');
    }

    /**
     * Get attribute groups with their attributes
     *
     * @return Collection<int, AttributeGroup>
     */
    public function groupsWithAttributes(): Collection
    {
        // The family pivot (attribute_family_attributes.attribute_group_id) is
        // the source of truth for which group each family attribute belongs to.
        $familyAttributes = $this->attributes()->get();

        $groupIds = $familyAttributes
            ->pluck('pivot.attribute_group_id')
            ->filter()
            ->unique();

        return AttributeGroup::whereIn('id', $groupIds)
            ->orderBy('id')
            ->get()
            ->each(function (AttributeGroup $group) use ($familyAttributes): void {
                $group->setRelation(
                    'attributes',
                    $familyAttributes
                        ->filter(fn ($attribute) => (int) $attribute->pivot->attribute_group_id === (int) $group->id)
                        ->values()
                );
            });
    }

    /**
     * Get attributes by group
     *
     * @return Collection<int, Attribute>
     */
    public function attributesByGroup(AttributeGroup $group): Collection
    {
        return $this->attributes()
            ->wherePivot('attribute_group_id', $group->id)
            ->orderBy('attribute_family_attributes.position')
            ->get();
    }

    /**
     * Scope for active families
     *
     * @param Builder<AttributeFamily> $query
     * @return Builder<AttributeFamily>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Check if attribute is required in this family
     */
    public function isAttributeRequired(Attribute $attribute): bool
    {
        /** @var Attribute|null $attr */
        $attr = $this->attributes()
            ->where('attributes.id', $attribute->id)
            ->first();

        if ($attr === null) {
            return false;
        }

        /** @var \stdClass|null $pivot */
        $pivot = $attr->getAttribute('pivot');
        if ($pivot === null) {
            return false;
        }

        return (bool) ($pivot->is_required ?? false);
    }
}
