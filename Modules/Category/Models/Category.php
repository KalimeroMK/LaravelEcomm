<?php

declare(strict_types=1);

namespace Modules\Category\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection as SupportCollection;
use Kalnoy\Nestedset\NodeTrait;
use Kalnoy\Nestedset\QueryBuilder;
use Modules\Attribute\Models\AttributeFamily;
use Modules\Attribute\Models\AttributeValue;
use Modules\Category\Database\Factories\CategoryFactory;
use Modules\Core\Models\Core;
use Modules\Core\Traits\HasSlug;
use Modules\Post\Models\Post;
use Modules\Product\Models\Product;

/**
 * Class Category
 *
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property int $status
 * @property int|null $parent_id
 * @property int|null $_lft
 * @property int|null $_rgt
 * @property int|null $attribute_family_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read \Kalnoy\Nestedset\Collection<int, Category> $categories
 * @property-read int|null                                    $categories_count
 * @property-read Category|null                               $category
 * @property-read \Kalnoy\Nestedset\Collection<int, Category> $children
 * @property-read int|null                                    $children_count
 * @property-read \Kalnoy\Nestedset\Collection<int, Category> $childrenCategories
 * @property-read int|null                                    $children_categories_count
 * @property-read Category|null                               $parent
 * @property-read Collection<int, Post>                       $posts
 * @property-read int|null                                    $posts_count
 * @property-read Collection<int, Product>                    $products
 * @property-read int|null                                    $products_count
 * @property-read AttributeFamily|null                        $attributeFamily
 * @property-read Collection<int, AttributeValue>             $attributeValues
 *
 * @method static \Kalnoy\Nestedset\Collection<int, static> all($columns = ['*'])
 * @method static QueryBuilder<static>|Category ancestorsAndSelf($id, array $columns = [])
 * @method static QueryBuilder<static>|Category ancestorsOf($id, array $columns = [])
 * @method static QueryBuilder<static>|Category applyNestedSetScope(?string $table = null)
 * @method static QueryBuilder<static>|Category childrenAndSelf($id)
 * @method static QueryBuilder<static>|Category descendantsAndSelf($id)
 * @method static QueryBuilder<static>|Category descendantsOf($id)
 * @method static CategoryFactory factory($count = null, $state = [])
 * @method static QueryBuilder<static>|Category fixSubtree($root)
 * @method static QueryBuilder<static>|Category fixTree($root = null)
 * @method static \Kalnoy\Nestedset\Collection<int, static> get($columns = ['*'])
 * @method static QueryBuilder<static>|Category getNodeData($id, $required = false)
 * @method static QueryBuilder<static>|Category getPlainNodeData($id, $required = false)
 * @method static QueryBuilder<static>|Category getTotalErrors()
 * @method static QueryBuilder<static>|Category hasChildren()
 * @method static QueryBuilder<static>|Category hasParent()
 * @method static QueryBuilder<static>|Category isBroken()
 * @method static QueryBuilder<static>|Category isLeaf()
 * @method static QueryBuilder<static>|Category isRoot()
 * @method static QueryBuilder<static>|Category leaves(array $columns = [])
 * @method static QueryBuilder<static>|Category makeGap(int $cut, int $height)
 * @method static QueryBuilder<static>|Category moveNode($key, $position, $target)
 * @method static QueryBuilder<static>|Category newModelQuery()
 * @method static QueryBuilder<static>|Category newQuery()
 * @method static QueryBuilder<static>|Category orWhereAncestorOf(bool $id, bool $andSelf = false)
 * @method static QueryBuilder<static>|Category orWhereDescendantOf($id)
 * @method static QueryBuilder<static>|Category orWhereDescendantOrSelf(string $id)
 * @method static QueryBuilder<static>|Category orWhereIsAfter($id)
 * @method static QueryBuilder<static>|Category orWhereIsBefore($id)
 * @method static QueryBuilder<static>|Category parent($id)
 * @method static QueryBuilder<static>|Category parentId()
 * @method static QueryBuilder<static>|Category query()
 * @method static QueryBuilder<static>|Category rebuildSubtree($root, $data, $delete = false)
 * @method static QueryBuilder<static>|Category rebuildTree($data, $delete = false)
 * @method static QueryBuilder<static>|Category reversed()
 * @method static QueryBuilder<static>|Category root(array $columns = [])
 * @method static QueryBuilder<static>|Category whereAncestorOf($id, $andSelf = false, array $columns = [])
 * @method static QueryBuilder<static>|Category whereAncestorOrSelf($id)
 * @method static QueryBuilder<static>|Category whereAttributeFamilyId($value)
 * @method static QueryBuilder<static>|Category whereCreatedAt($value)
 * @method static QueryBuilder<static>|Category whereDeletedAt($value)
 * @method static QueryBuilder<static>|Category whereDescendantOf($id, array $columns = [], bool $andSelf = false)
 * @method static QueryBuilder<static>|Category whereDescendantOrSelf(string $id)
 * @method static QueryBuilder<static>|Category whereId($value)
 * @method static QueryBuilder<static>|Category whereIsAfter($id)
 * @method static QueryBuilder<static>|Category whereIsBefore($id)
 * @method static QueryBuilder<static>|Category whereIsLeaf()
 * @method static QueryBuilder<static>|Category whereIsRoot()
 * @method static QueryBuilder<static>|Category whereLft($value)
 * @method static QueryBuilder<static>|Category whereNodeBetween($from, $to, ?string $boolean = 'and')
 * @method static QueryBuilder<static>|Category whereNotDescendantOf($id)
 * @method static QueryBuilder<static>|Category whereParentId($value)
 * @method static QueryBuilder<static>|Category whereRgt($value)
 * @method static QueryBuilder<static>|Category whereSlug($value)
 * @method static QueryBuilder<static>|Category whereStatus($value)
 * @method static QueryBuilder<static>|Category whereTitle($value)
 * @method static QueryBuilder<static>|Category whereUpdatedAt($value)
 * @method static QueryBuilder<static>|Category withDepth()
 * @method static QueryBuilder<static>|Category withUniqueSlugConstraints(\Illuminate\Database\Eloquent\Model $model, string $attribute, array $config, string $slug)
 * @method static QueryBuilder<static>|Category withoutRoot()
 *
 * @mixin Eloquent
 */
class Category extends Core
{
    use HasSlug;
    use NodeTrait;
    use SoftDeletes;

    protected $table = 'categories';

    protected $fillable
        = [
            'title',
            'slug',
            'status',
            'summary',
            'parent_id',
            'attribute_family_id',
        ];

    protected $casts = [
        'status' => 'int',
        'parent_id' => 'int',
        'attribute_family_id' => 'int',
    ];

    public static function Factory(): CategoryFactory
    {
        return CategoryFactory::new();
    }

    /**
     * Count active categories
     */
    public static function countActiveCategory(): int
    {
        return self::where('status', 1)->count();
    }

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function categories(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * Alias for children relation (used by NodeTrait)
     *
     * @return \Kalnoy\Nestedset\Relations\DescendantsRelation<Category, Category>
     */
    public function childrenCategories()
    {
        return $this->children();
    }

    /**
     * Attribute family for this category
     */
    public function attributeFamily(): BelongsTo
    {
        return $this->belongsTo(AttributeFamily::class);
    }

    /**
     * Attribute values for this category (polymorphic)
     *
     * @return HasMany<AttributeValue, Category>
     */
    public function attributeValues(): HasMany
    {
        return $this->hasMany(AttributeValue::class, 'attributable_id')
            ->where('attributable_type', self::class);
    }

    /**
     * Get attribute value by code
     */
    public function getAttributeValueByCode(string $attributeCode): mixed
    {
        $attributeValue = $this->attributeValues()
            ->whereHas('attribute', function ($q) use ($attributeCode) {
                $q->where('code', $attributeCode);
            })
            ->first();

        return $attributeValue?->getValue();
    }

    /**
     * Get all attributes from the attribute family
     */
    public function getAttributeFamilyAttributes(): SupportCollection
    {
        // Avoid querying if no attribute family is assigned
        if (! array_key_exists('attribute_family_id', $this->attributes) || $this->attributes['attribute_family_id'] === null) {
            return SupportCollection::make();
        }

        return $this->attributeFamily?->attributes ?? SupportCollection::make();
    }

    /**
     * Check if category has attribute family
     */
    public function hasAttributeFamily(): bool
    {
        return array_key_exists('attribute_family_id', $this->attributes) && $this->attributes['attribute_family_id'] !== null;
    }
}
