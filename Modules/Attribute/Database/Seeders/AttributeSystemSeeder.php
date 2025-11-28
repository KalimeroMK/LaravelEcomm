<?php

declare(strict_types=1);

namespace Modules\Attribute\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Attribute\Models\Attribute;
use Modules\Attribute\Models\AttributeValue;
use Modules\Product\Models\Product;

class AttributeSystemSeeder extends Seeder
{
    public function run(): void
    {
        $color = Attribute::where('code', 'color')->first();
        $size = Attribute::where('code', 'size')->first();
        $isNew = Attribute::where('code', 'is_new')->first();

        /** @var Product|null $product */
        $product = Product::first();
        if (! $product instanceof Product) {
            $product = Product::factory()->create();
        }

        if (! $product instanceof Product) {
            return;
        }

        // Product doesn't have attributes() method, attributes are stored via AttributeValue
        // So we skip the attach call and just create AttributeValue records below

        $productId = $product->id;

        if ($color) {
            AttributeValue::factory()->create([
                'product_id' => $productId,
                'attribute_id' => $color->id,
                'text_value' => 'Red',
            ]);
        }
        if ($size) {
            AttributeValue::factory()->create([
                'product_id' => $productId,
                'attribute_id' => $size->id,
                'text_value' => 'Large',
            ]);
        }
        if ($isNew) {
            AttributeValue::factory()->create([
                'product_id' => $productId,
                'attribute_id' => $isNew->id,
                'boolean_value' => true,
            ]);
        }
    }
}
