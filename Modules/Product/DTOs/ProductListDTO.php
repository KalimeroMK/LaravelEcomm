<?php

declare(strict_types=1);

namespace Modules\Product\DTOs;

class ProductListDTO
{
    public array $products = [];

    public function __construct($products)
    {
        foreach ($products as $product) {
            $arr = $product->toArray();
            // Map attributeValues to attributes array
            $arr['attributes'] = [];
            if (! empty($product->attributeValues)) {
                foreach ($product->attributeValues as $attributeValue) {
                    if ($attributeValue->attribute) {
                        // Find the actual value column (text_value, int_value, etc.)
                        $value = $attributeValue->text_value
                            ?? $attributeValue->string_value
                            ?? $attributeValue->integer_value
                            ?? $attributeValue->float_value
                            ?? $attributeValue->boolean_value
                            ?? $attributeValue->date_value
                            ?? $attributeValue->decimal_value
                            ?? $attributeValue->url_value
                            ?? $attributeValue->hex_value
                            ?? null;
                        $arr['attributes'][] = [
                            'attribute' => [
                                'name' => $attributeValue->attribute->name,
                                'label' => $attributeValue->attribute->display,
                                'type' => $attributeValue->attribute->type,
                            ],
                            'value' => $value,
                        ];
                    }
                }
            }
            $this->products[] = $arr;
        }
    }
}
