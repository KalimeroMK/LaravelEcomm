<?php

declare(strict_types=1);

namespace Modules\Category\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Kalnoy\Nestedset\NodeTrait;
use Kalnoy\Nestedset\QueryBuilder;
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
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read \Kalnoy\Nestedset\Collection<int, Category> $categories
 * @property-read int|null $categories_count
 * @property-read Category|null $category
 * @property-read \Kalnoy\Nestedset\Collection<int, Category> $children
 * @property-read int|null $children_count
 * @property-read \Kalnoy\Nestedset\Collection<int, Category> $childrenCategories
 * @property-read int|null $children_categories_count
 * @property-read Category|null $parent
 * @property-read Collection<int, Post> $posts
 * @property-read int|null $posts_count
 * @property-read Collection<int, Product> $products
 * @property-read int|null $products_count
 *
 * @method static \Kalnoy\Nestedset\Collection<int, static> all($columns = ['*'])
 * @method static QueryBuilder<static>|Category ancestorsAndSelf($id, array $columns = [])
 * @method static QueryBuilder<static>|Category ancestorsOf($id, array $columns = [])
 * @method static QueryBuilder<static>|Category applyNestedSetScope(?string $table = null)
 * @method static QueryBuilder<static>|Category countErrors()
 * @method static QueryBuilder<static>|Category d()
 * @method static QueryBuilder<static>|Category defaultOrder(string $dir = 'asc')
 * @method static QueryBuilder<static>|Category descendantsAndSelf($id, array $columns = [])
 * @method static QueryBuilder<static>|Category descendantsOf($id, array $columns = [], $andSelf = false)
 * @method static QueryBuilder<static>|Category fixSubtree($root)
 * @method static QueryBuilder<static>|Category fixTree($root = null)
 * @method static \Kalnoy\Nestedset\Collection<int, static> get($columns = ['*'])
 * @method static QueryBuilder<static>|Category getNodeData($id, $required = false)
 * @method static QueryBuilder<static>|Category getPlainNodeData($id, $required = false)
 * @method static QueryBuilder<static>|Category getTotalErrors()
 * @method static QueryBuilder<static>|Category hasChildren()
 * @method static QueryBuilder<static>|Category hasParent()
 * @method static QueryBuilder<static>|Category isBroken()
 * @method static QueryBuilder<static>|Category leaves(array $columns = [])
 * @method static QueryBuilder<static>|Category makeGap(int $cut, int $height)
 * @method static QueryBuilder<static>|Category moveNode($key, $position)
 * @method static QueryBuilder<static>|Category newModelQuery()
 * @method static QueryBuilder<static>|Category newQuery()
 * @method static Builder<static>|Category onlyTrashed()
 * @method static QueryBuilder<static>|Category orWhereAncestorOf(bool $id, bool $andSelf = false)
 * @method static QueryBuilder<static>|Category orWhereDescendantOf($id)
 * @method static QueryBuilder<static>|Category orWhereNodeBetween($values)
 * @method static QueryBuilder<static>|Category orWhereNotDescendantOf($id)
 * @method static QueryBuilder<static>|Category query()
 * @method static QueryBuilder<static>|Category rebuildSubtree($root, array $data, $delete = false)
 * @method static QueryBuilder<static>|Category rebuildTree(array $data, $delete = false, $root = null)
 * @method static QueryBuilder<static>|Category reversed()
 * @method static QueryBuilder<static>|Category root(array $columns = [])
 * @method static QueryBuilder<static>|Category whereAncestorOf($id, $andSelf = false, $boolean = 'and')
 * @method static QueryBuilder<static>|Category whereAncestorOrSelf($id)
 * @method static QueryBuilder<static>|Category whereCreatedAt($value)
 * @method static QueryBuilder<static>|Category whereDeletedAt($value)
 * @method static QueryBuilder<static>|Category whereDescendantOf($id, $boolean = 'and', $not = false, $andSelf = false)
 * @method static QueryBuilder<static>|Category whereDescendantOrSelf(string $id, string $boolean = 'and', string $not = false)
 * @method static QueryBuilder<static>|Category whereId($value)
 * @method static QueryBuilder<static>|Category whereIsAfter($id, $boolean = 'and')
 * @method static QueryBuilder<static>|Category whereIsBefore($id, $boolean = 'and')
 * @method static QueryBuilder<static>|Category whereIsLeaf()
 * @method static QueryBuilder<static>|Category whereIsRoot()
 * @method static QueryBuilder<static>|Category whereLft($value)
 * @method static QueryBuilder<static>|Category whereNodeBetween($values, $boolean = 'and', $not = false, $query = null)
 * @method static QueryBuilder<static>|Category whereNotDescendantOf($id)
 * @method static QueryBuilder<static>|Category whereParentId($value)
 * @method static QueryBuilder<static>|Category whereRgt($value)
 * @method static QueryBuilder<static>|Category whereSlug($value)
 * @method static QueryBuilder<static>|Category whereStatus($value)
 * @method static QueryBuilder<static>|Category whereTitle($value)
 * @method static QueryBuilder<static>|Category whereUpdatedAt($value)
 * @method static QueryBuilder<static>|Category withDepth(string $as = 'depth')
 * @method static Builder<static>|Category withTrashed()
 * @method static QueryBuilder<static>|Category withoutRoot()
 * @method static Builder<static>|Category withoutTrashed()
 *
 * @mixin Eloquent
 */
class Category extends Core
{
    use HasSlug;
    use NodeTrait, SoftDeletes;

    protected $table = 'categories';

    protected $casts
        = [
            'status' => 'int',
            'parent_id' => 'int',
            '_lft' => 'int',
            '_rgt' => 'int',
        ];

    protected $fillable
        = [
            'title',
            'slug',
            'status',
            'parent_id',
            '_lft',
            '_rgt',
        ];

    public static function Factory(): CategoryFactory
    {
        return CategoryFactory::new();
    }

    /**
     * @return array<array-key, array{title: string, id: int}>
     */
    public static function getTree(): array
    {
        $categories = self::get()->toTree();
        $allCats = [];
        $traverse = function ($categories, string $prefix = '') use (&$traverse, &$allCats): array {
            foreach ($categories as $category) {
                $allCats[] = ['title' => $prefix.' '.$category->title, 'id' => $category->id];
                $traverse($category->children, $prefix.'-');
            }

            return $allCats;
        };

        return $traverse($categories);
    }

    public static function countActiveCategory(): int
    {
        $data = self::where('status', 'active')->count();
        if ($data) {
            return $data;
        }

        return 0;
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function categories(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function childrenCategories(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->with('categories');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function getParentsNames(): string
    {
        if ($this->parent) {
            return $this->parent->getParentsNames();
        }

        return $this->title ?? '';
    }

    public function makeAllSearchableUsing(Builder $query): Builder
    {
        return $query->with('products');
    }
}
