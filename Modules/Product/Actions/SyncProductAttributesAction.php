<?php

declare(strict_types=1);

namespace Modules\Product\Actions;

use Modules\Attribute\Models\Attribute;
use Modules\Product\Models\Product;

readonly class SyncProductAttributesAction
{
    /**
     * Sync product attribute values (including custom values)
     */
    public static function execute(Product $product, array $attributes): void
    {
        // Remove old attribute values
        $product->attributeValues()->delete();

        foreach ($attributes as $code => $data) {
            $attribute = Attribute::where('code', $code)->first();
            if (! $attribute) {
                continue;
            }

            // Prefer option, then custom value
            $finalValue = $data['option'] ?? $data['value'] ?? null;
            if ($finalValue === null || $finalValue === '') {
                continue;
            }

            // Determine column and cast value based on attribute type
            $type = $attribute->type;
            $column = match ($type) {
                'date' => 'date_value',
                'float' => 'float_value',
                'boolean' => 'boolean_value',
                'integer' => 'integer_value',
                'decimal' => 'decimal_value',
                'string' => 'string_value',
                'text' => 'text_value',
                'url' => 'url_value',
                'hex' => 'hex_value',
                default => 'text_value',
            };

            // Cast value according to type
            $castedValue = match ($type) {
                'integer' => (int) $finalValue,
                'float', 'decimal' => (float) $finalValue,
                'boolean' => filter_var($finalValue, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
                'date' => $finalValue ? date('Y-m-d', strtotime($finalValue)) : null,
                default => (string) $finalValue,
            };

            $product->attributeValues()->create([
                'attribute_id' => $attribute->id,
                $column => $castedValue,
            ]);
        }
    }
}
