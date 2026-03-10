<?php

declare(strict_types=1);

namespace Modules\Attribute\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Modules\Attribute\Models\Attribute;
use Modules\Product\Models\Product;

/**
 * Service for layered navigation and attribute filtering
 */
readonly class LayeredNavigationService
{
    /**
     * Get filterable attributes with their options and counts
     *
     * @param array<int>|null $categoryIds
     * @return Collection<int, Attribute>
     */
    public function getFilterableAttributes(?array $categoryIds = null): Collection
    {
        $query = Attribute::where('is_filterable', true)
            ->with('options');

        /** @var Collection<int, Attribute> $attributes */
        $attributes = $query->get();

        return $attributes->map(function (Attribute $attribute) use ($categoryIds) {
            /** @var Collection<int, \Modules\Attribute\Models\AttributeOption> $optionsWithCounts */
            $optionsWithCounts = $this->getOptionsWithCounts($attribute, $categoryIds);
            $attribute->setAttribute('options_with_counts', $optionsWithCounts);

            return $attribute;
        });
    }

    /**
     * Get options with product counts for an attribute
     *
     * @param array<int>|null $categoryIds
     * @return Collection<int, \Modules\Attribute\Models\AttributeOption>
     */
    public function getOptionsWithCounts(Attribute $attribute, ?array $categoryIds = null): Collection
    {
        /** @var Collection<string, int> $counts */
        $counts = $attribute->attributeValues()
            ->selectRaw('text_value as value, COUNT(DISTINCT attributable_id) as count')
            ->where('attributable_type', Product::class)
            ->whereHas('attributable', function (Builder $query) use ($categoryIds) {
                $query->where('status', 'active');

                if ($categoryIds) {
                    $query->whereHas('categories', function (Builder $q) use ($categoryIds) {
                        $q->whereIn('categories.id', $categoryIds);
                    });
                }
            })
            ->groupBy('text_value')
            ->pluck('count', 'value');

        return $attribute->options->map(function ($option) use ($counts) {
            /** @var int $count */
            $count = $counts[$option->value] ?? 0;
            $option->setAttribute('product_count', $count);

            return $option;
        })->filter(fn ($option) => ($option->getAttribute('product_count') ?? 0) > 0);
    }

    /**
     * Apply filters to product query
     *
     * @param Builder<Product> $query
     * @param array<string, mixed> $filters
     * @return Builder<Product>
     */
    public function applyFilters(Builder $query, array $filters): Builder
    {
        // Price filter
        if (isset($filters['price_min']) || isset($filters['price_max'])) {
            $this->applyPriceFilter($query, $filters['price_min'] ?? null, $filters['price_max'] ?? null);
        }

        // Attribute filters
        foreach ($filters as $key => $values) {
            if (in_array($key, ['price_min', 'price_max', 'sort', 'page', 'per_page'])) {
                continue;
            }

            if (! empty($values)) {
                $this->applyAttributeFilter($query, $key, $values);
            }
        }

        return $query;
    }

    /**
     * Get available filters based on current filter state
     *
     * @param array<string, mixed> $currentFilters
     * @param array<int>|null $categoryIds
     * @return array<string, mixed>
     */
    public function getAvailableFilters(array $currentFilters = [], ?array $categoryIds = null): array
    {
        $attributes = $this->getFilterableAttributes($categoryIds);

        /** @var Collection<int, array<string, mixed>> $attributeFilters */
        $attributeFilters = $attributes->map(function (Attribute $attribute) {
            /** @var Collection<int, \Modules\Attribute\Models\AttributeOption>|null $optionsWithCounts */
            $optionsWithCounts = $attribute->getAttribute('options_with_counts');

            return [
                'id' => $attribute->id,
                'name' => $attribute->name,
                'code' => $attribute->code,
                'display' => $attribute->display,
                'type' => $this->getFilterType($attribute),
                'options' => $optionsWithCounts?->map(function (\Modules\Attribute\Models\AttributeOption $option) {
                    return [
                        'value' => $option->value,
                        'label' => $option->label ?? $option->value,
                        'count' => $option->getAttribute('product_count') ?? 0,
                        'color_hex' => $option->getAttribute('color_hex'),
                        'image' => $option->getAttribute('image'),
                    ];
                }) ?? collect(),
            ];
        });

        // Get price range
        $priceRange = $this->getPriceRange($categoryIds);

        return [
            'attributes' => $attributeFilters,
            'price_range' => $priceRange,
        ];
    }

    /**
     * Get price range for products
     *
     * @param array<int>|null $categoryIds
     * @return array<string, float>
     */
    public function getPriceRange(?array $categoryIds = null): array
    {
        $query = Product::where('status', 'active');

        if ($categoryIds) {
            $query->whereHas('categories', function (Builder $q) use ($categoryIds) {
                $q->whereIn('categories.id', $categoryIds);
            });
        }

        /** @var \stdClass|null $result */
        $result = $query->selectRaw('MIN(price) as min, MAX(price) as max')->first();

        return [
            'min' => (float) ($result->min ?? 0),
            'max' => (float) ($result->max ?? 1000),
        ];
    }

    /**
     * Build filter URL
     *
     * @param array<string, mixed> $filters
     */
    public function buildFilterUrl(string $baseUrl, array $filters): string
    {
        $query = http_build_query($filters);

        return $baseUrl.($query ? '?'.$query : '');
    }

    /**
     * Parse filter parameters from request
     *
     * @param array<string, mixed> $params
     * @return array<string, mixed>
     */
    public function parseFilters(array $params): array
    {
        $filters = [];

        foreach ($params as $key => $value) {
            // Skip pagination and sort params
            if (in_array($key, ['page', 'sort', 'per_page', 'view'])) {
                continue;
            }

            // Handle array values (multiple selections)
            if (is_string($value) && str_contains($value, ',')) {
                $value = explode(',', $value);
            }

            $filters[$key] = $value;
        }

        return $filters;
    }

    /**
     * Get active filters for display
     *
     * @param array<string, mixed>|\Illuminate\Http\Request $params
     * @return Collection<int, array<string, mixed>>
     */
    public function getActiveFilters(array|\Illuminate\Http\Request $params): Collection
    {
        if ($params instanceof \Illuminate\Http\Request) {
            $params = $params->all();
        }

        /** @var Collection<int, array<string, mixed>> $active */
        $active = collect();
        $parsed = $this->parseFilters($params);

        foreach ($parsed as $code => $values) {
            if ($code === 'price_min' || $code === 'price_max') {
                continue;
            }

            $attribute = Attribute::where('code', $code)->first();

            if (! $attribute) {
                continue;
            }

            $values = is_array($values) ? $values : [$values];

            foreach ($values as $value) {
                $option = $attribute->options->firstWhere('value', $value);
                $active->push([
                    'attribute_name' => $attribute->name,
                    'attribute_code' => $code,
                    'value' => $value,
                    'label' => $option !== null ? $option->label : $value,
                ]);
            }
        }

        return $active;
    }

    /**
     * Get filter type for display
     */
    public function getFilterType(Attribute $attribute): string
    {
        return match ($attribute->display) {
            'color' => 'swatch',
            'button' => 'button',
            'multiselect', 'multi_select' => 'multiselect',
            default => 'default',
        };
    }

    /**
     * Apply price range filter
     *
     * @param Builder<Product> $query
     */
    private function applyPriceFilter(Builder $query, ?float $min, ?float $max): void
    {
        if ($min !== null) {
            $query->where('price', '>=', $min);
        }

        if ($max !== null) {
            $query->where('price', '<=', $max);
        }
    }

    /**
     * Apply attribute filter
     *
     * @param Builder<Product> $query
     * @param mixed $values
     */
    private function applyAttributeFilter(Builder $query, string $attributeCode, mixed $values): void
    {
        $values = is_array($values) ? $values : [$values];

        $query->whereHas('attributeValues', function (Builder $q) use ($attributeCode, $values) {
            $q->whereHas('attribute', function (Builder $qa) use ($attributeCode) {
                $qa->where('code', $attributeCode);
            });

            $q->where(function (Builder $qv) use ($values) {
                foreach ($values as $value) {
                    $qv->orWhere('text_value', $value)
                        ->orWhere('string_value', $value);
                }
            });
        });
    }
}
