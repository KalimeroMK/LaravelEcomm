<?php

declare(strict_types=1);

namespace Modules\Product\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Modules\Attribute\Models\Attribute;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductVariant;

/**
 * Service for managing configurable products and their variants
 */
readonly class ConfigurableProductService
{
    /**
     * Generate all possible variant combinations for a configurable product
     */
    public function generateVariants(Product $product, ?array $attributeCodes = null): Collection
    {
        if (! $product->isConfigurable()) {
            throw new InvalidArgumentException('Product must be configurable');
        }

        $codes = $attributeCodes ?? $product->configurable_attributes ?? [];

        if (empty($codes)) {
            return collect();
        }

        $attributes = Attribute::whereIn('code', $codes)
            ->with('options')
            ->get();

        $combinations = $this->generateCombinations($attributes);
        $variants = collect();

        DB::transaction(function () use ($product, $combinations, &$variants) {
            foreach ($combinations as $index => $combination) {
                $variant = $this->createVariant($product, $combination, $index === 0);
                $variants->push($variant);
            }
        });

        return $variants;
    }

    /**
     * Create a single variant from attribute combination
     */
    public function createVariant(Product $parent, array $combination, bool $isDefault = false): Product
    {
        // Generate variant name and SKU suffix
        $variantName = $this->generateVariantName($combination);
        $skuSuffix = $this->generateSkuSuffix($combination);

        // Check if variant already exists
        $existingVariant = $parent->variants()
            ->where('variant_sku_suffix', $skuSuffix)
            ->first();

        if ($existingVariant) {
            return $existingVariant;
        }

        // Create variant
        $variant = Product::create([
            'type' => Product::TYPE_VARIANT,
            'parent_id' => $parent->id,
            'title' => $parent->title.' - '.$variantName,
            'variant_name' => $variantName,
            'sku' => $parent->sku.$skuSuffix,
            'variant_sku_suffix' => $skuSuffix,
            'price' => $parent->price,
            'stock' => 0,
            'status' => 'active',
            'brand_id' => $parent->brand_id,
            'description' => $parent->description,
            'summary' => $parent->summary,
        ]);

        // Create attribute values for this variant
        foreach ($combination as $attributeCode => $value) {
            $attribute = Attribute::where('code', $attributeCode)->first();

            if (! $attribute) {
                continue;
            }

            $column = $attribute->getValueColumnName();
            $castedValue = $this->castValue($value, $attribute->type);

            $variant->attributeValues()->create([
                'attribute_id' => $attribute->id,
                'attributable_id' => $variant->id,
                'attributable_type' => Product::class,
                $column => $castedValue,
            ]);
        }

        // Create pivot record
        ProductVariant::create([
            'product_id' => $parent->id,
            'variant_product_id' => $variant->id,
            'attribute_combination' => $combination,
            'is_default' => $isDefault,
        ]);

        return $variant;
    }

    /**
     * Update variant prices in bulk
     */
    public function updateVariantPrices(Product $parent, array $prices): void
    {
        if (! $parent->isConfigurable()) {
            return;
        }

        foreach ($prices as $variantId => $price) {
            $variant = $parent->variants()->find($variantId);

            if ($variant) {
                $variant->update(['price' => $price]);
            }
        }
    }

    /**
     * Update variant stock in bulk
     */
    public function updateVariantStock(Product $parent, array $stocks): void
    {
        if (! $parent->isConfigurable()) {
            return;
        }

        foreach ($stocks as $variantId => $stock) {
            $variant = $parent->variants()->find($variantId);

            if ($variant) {
                $variant->update(['stock' => $stock]);
            }
        }
    }

    /**
     * Delete all variants of a configurable product
     */
    public function deleteVariants(Product $product): void
    {
        if (! $product->isConfigurable()) {
            return;
        }

        DB::transaction(function () use ($product) {
            // Delete variants
            $product->variants()->each(function ($variant) {
                $variant->attributeValues()->delete();
                $variant->delete();
            });

            // Delete pivot records
            ProductVariant::where('product_id', $product->id)->delete();
        });
    }

    /**
     * Get variant by selected attributes
     */
    public function findVariantByAttributes(Product $product, array $attributes): ?Product
    {
        if (! $product->isConfigurable()) {
            return null;
        }

        foreach ($product->variants as $variant) {
            $variantAttrs = [];

            foreach ($variant->attributeValues as $av) {
                $variantAttrs[$av->attribute->code] = $av->getValue();
            }

            // Check if all requested attributes match
            $matches = true;
            foreach ($attributes as $code => $value) {
                if (($variantAttrs[$code] ?? null) !== $value) {
                    $matches = false;
                    break;
                }
            }

            if ($matches) {
                return $variant;
            }
        }

        return null;
    }

    /**
     * Get available options for next attribute selection
     */
    public function getAvailableOptions(Product $product, array $selectedAttributes, string $nextAttributeCode): Collection
    {
        if (! $product->isConfigurable()) {
            return collect();
        }

        $variants = $product->variants;
        $availableOptions = collect();

        foreach ($variants as $variant) {
            $variantAttrs = [];

            foreach ($variant->attributeValues as $av) {
                $variantAttrs[$av->attribute->code] = $av->getValue();
            }

            // Check if matches selected attributes
            $matches = true;
            foreach ($selectedAttributes as $code => $value) {
                if (($variantAttrs[$code] ?? null) !== $value) {
                    $matches = false;
                    break;
                }
            }

            if ($matches && isset($variantAttrs[$nextAttributeCode])) {
                $availableOptions->push($variantAttrs[$nextAttributeCode]);
            }
        }

        return $availableOptions->unique()->values();
    }

    /**
     * Generate all combinations of attribute options
     */
    private function generateCombinations(Collection $attributes): Collection
    {
        if ($attributes->isEmpty()) {
            return collect();
        }

        $combinations = collect([[]]);

        foreach ($attributes as $attribute) {
            $values = $attribute->options->pluck('value')->toArray();

            if (empty($values)) {
                continue;
            }

            $newCombinations = collect();

            foreach ($combinations as $combination) {
                foreach ($values as $value) {
                    $newCombinations->push(array_merge($combination, [$attribute->code => $value]));
                }
            }

            $combinations = $newCombinations;
        }

        return $combinations;
    }

    /**
     * Generate human-readable variant name
     */
    private function generateVariantName(array $combination): string
    {
        return collect($combination)
            ->map(fn ($value) => ucfirst($value))
            ->join(' / ');
    }

    /**
     * Generate SKU suffix from combination
     */
    private function generateSkuSuffix(array $combination): string
    {
        $suffix = collect($combination)
            ->map(fn ($value) => mb_strtoupper(mb_substr($value, 0, 3)))
            ->join('-');

        return '-'.$suffix;
    }

    /**
     * Cast value based on attribute type
     */
    private function castValue(mixed $value, string $type): mixed
    {
        return match ($type) {
            'integer' => (int) $value,
            'float', 'decimal' => (float) $value,
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'date' => $value ? date('Y-m-d', strtotime($value)) : null,
            default => (string) $value,
        };
    }

}
