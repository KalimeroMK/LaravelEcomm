<?php

namespace Modules\Category\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use JeroenG\Explorer\Application\Aliased;
use JeroenG\Explorer\Application\Explored;
use JeroenG\Explorer\Application\IndexSettings;
use Kalnoy\Nestedset\NodeTrait;
use Kalnoy\Nestedset\QueryBuilder;
use Laravel\Scout\Searchable;
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
 * @property int|null $status
 * @property int|null $parent_id
 * @property int|null $_lft
 * @property int|null $_rgt
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Category|null $category
 * @property Collection|Category[] $categories
 * @property-read int|null                                $categories_count
 * @property-read Collection|Product[]                    $products
 * @property-read int|null                                $products_count
 *
 * @method static Builder|Category newModelQuery()
 * @method static Builder|Category newQuery()
 * @method static Builder|Category query()
 * @method static Builder|Category whereCreatedAt($value)
 * @method static Builder|Category whereId($value)
 * @method static Builder|Category whereLft($value)
 * @method static Builder|Category whereParentId($value)
 * @method static Builder|Category whereRgt($value)
 * @method static Builder|Category whereSlug($value)
 * @method static Builder|Category whereStatus($value)
 * @method static Builder|Category whereTitle($value)
 * @method static Builder|Category whereUpdatedAt($value)
 *
 * @mixin Eloquent
 *
 * @property-read \Kalnoy\Nestedset\Collection|Category[] $children
 * @property-read int|null                                $children_count
 * @property-read Category|null                           $parent
 *
 * @method static QueryBuilder|Category ancestorsAndSelf($id, array $columns = [])
 * @method static QueryBuilder|Category ancestorsOf($id, array $columns = [])
 * @method static QueryBuilder|Category applyNestedSetScope(?string $table = null)
 * @method static QueryBuilder|Category countErrors()
 * @method static QueryBuilder|Category d()
 * @method static QueryBuilder|Category defaultOrder(string $dir = 'asc')
 * @method static QueryBuilder|Category descendantsAndSelf($id, array $columns = [])
 * @method static QueryBuilder|Category descendantsOf($id, array $columns = [], $andSelf = false)
 * @method static QueryBuilder|Category fixSubtree($root)
 * @method static QueryBuilder|Category fixTree($root = null)
 * @method static QueryBuilder|Category getNodeData($id, $required = false)
 * @method static QueryBuilder|Category getPlainNodeData($id, $required = false)
 * @method static QueryBuilder|Category getTotalErrors()
 * @method static QueryBuilder|Category hasChildren()
 * @method static QueryBuilder|Category hasParent()
 * @method static QueryBuilder|Category isBroken()
 * @method static QueryBuilder|Category leaves(array $columns = [])
 * @method static QueryBuilder|Category makeGap(int $cut, int $height)
 * @method static QueryBuilder|Category moveNode($key, $position)
 * @method static QueryBuilder|Category orWhereAncestorOf(bool $id, bool $andSelf = false)
 * @method static QueryBuilder|Category orWhereDescendantOf($id)
 * @method static QueryBuilder|Category orWhereNodeBetween($values)
 * @method static QueryBuilder|Category orWhereNotDescendantOf($id)
 * @method static QueryBuilder|Category rebuildSubtree($root, array $data, $delete = false)
 * @method static QueryBuilder|Category rebuildTree(array $data, $delete = false, $root = null)
 * @method static QueryBuilder|Category reversed()
 * @method static QueryBuilder|Category root(array $columns = [])
 * @method static QueryBuilder|Category whereAncestorOf($id, $andSelf = false, $boolean = 'and')
 * @method static QueryBuilder|Category whereAncestorOrSelf($id)
 * @method static QueryBuilder|Category whereDescendantOf($id, $boolean = 'and', $not = false, $andSelf = false)
 * @method static QueryBuilder|Category whereDescendantOrSelf(string $id, string $boolean = 'and', string $not = false)
 * @method static QueryBuilder|Category whereIsAfter($id, $boolean = 'and')
 * @method static QueryBuilder|Category whereIsBefore($id, $boolean = 'and')
 * @method static QueryBuilder|Category whereIsLeaf()
 * @method static QueryBuilder|Category whereIsRoot()
 * @method static QueryBuilder|Category whereNodeBetween($values, $boolean = 'and', $not = false)
 * @method static QueryBuilder|Category whereNotDescendantOf($id)
 * @method static QueryBuilder|Category withDepth(string $as = 'depth')
 * @method static QueryBuilder|Category withoutRoot()
 *
 * @property-read \Kalnoy\Nestedset\Collection|Category[] $child_cat
 * @property-read int|null                                $child_cat_count
 * @property-read \Kalnoy\Nestedset\Collection|Category[] $childrenCategories
 * @property-read int|null                                $children_categories_count
 * @property-read Category|null                           $parent_info
 *
 * @method static \Kalnoy\Nestedset\Collection|static[] get($columns = ['*'])
 * @method static \Kalnoy\Nestedset\Collection|static[] all($columns = ['*'])
 */
class Category extends Core implements Aliased, Explored, IndexSettings
{
    use HasSlug;
    use NodeTrait, Searchable, SoftDeletes {
        Searchable::usesSoftDelete insteadof NodeTrait;
    }

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
        $traverse = function ($categories, $prefix = '') use (&$traverse, &$allCats) {
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
        $data = Category::where('status', 'active')->count();
        if ($data) {
            return $data;
        }

        return 0;
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function childrenCategories(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')->with('categories');
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
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function getParentsNames(): string
    {
        if ($this->parent) {
            return $this->parent->getParentsNames();
        } else {
            return $this->title;
        }
    }

    public function makeAllSearchableUsing(Builder $query): Builder
    {
        return $query->with('products');
    }

    /**
     * Converts the model instance to an array format suitable for search indexing.
     *
     * @return array<string, mixed> Returns an array where keys are column names and values are column values.
     */
    public function toSearchableArray(): array
    {
        return $this->toArray();
    }

    /**
     * Defines the mapping for the search engine.
     *
     * @return array<string, array<string, mixed>> Returns an array of settings for each model attribute.
     */
    public function mappableAs(): array
    {
        return [
            'properties' => [
                'id' => [
                    'type' => 'integer',
                ],
                'title' => [
                    'type' => 'text',
                    'analyzer' => 'standard',
                ],
            ],
        ];
    }

    /**
     * Configuration settings for the search index.
     *
     * @return array<string, mixed> Returns an array where keys are configuration settings and values are the settings' values.
     */
    public function indexSettings(): array
    {
        return [
            'number_of_shards' => 1,
            'number_of_replicas' => 0,
            'analysis' => [
                'analyzer' => [
                    'default' => [
                        'type' => 'standard',
                    ],
                ],
            ],
        ];
    }
}
