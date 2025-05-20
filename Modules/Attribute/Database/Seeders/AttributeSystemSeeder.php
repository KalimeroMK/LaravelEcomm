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

        $product = Product::first();
        if (! $product) {
            $product = Product::factory()->create();
        }
        $attachIds = [];
        if ($color) {
            $attachIds[] = $color->id;
        }
        if ($size) {
            $attachIds[] = $size->id;
        }
        if ($isNew) {
            $attachIds[] = $isNew->id;
        }
        if (! empty($attachIds)) {
            $product->attributes()->attach($attachIds);
        }

        if ($color) {
            AttributeValue::factory()->create([
                'product_id' => $product->id,
                'attribute_id' => $color->id,
                'text_value' => 'Red',
            ]);
        }
        if ($size) {
            AttributeValue::factory()->create([
                'product_id' => $product->id,
                'attribute_id' => $size->id,
                'text_value' => 'Large',
            ]);
        }
        if ($isNew) {
            AttributeValue::factory()->create([
                'product_id' => $product->id,
                'attribute_id' => $isNew->id,
                'boolean_value' => true,
            ]);
        }
    }
}
