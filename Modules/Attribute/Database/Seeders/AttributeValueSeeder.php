<?php

declare(strict_types=1);

namespace Modules\Attribute\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Attribute\Models\Attribute;
use Modules\Attribute\Models\AttributeValue;
use Modules\Product\Models\Product;

class AttributeValueSeeder extends Seeder
{
    public function run(): void
    {
        $values = [
            'color' => ['Red', 'Purple'], // includes one option, one custom
            'size' => ['M', 'XXL'],       // M is predefined, XXL is custom
        ];

        $product = Product::first() ?? Product::factory()->create();

        foreach ($values as $code => $items) {
            $attribute = Attribute::where('code', $code)->first();
            if ($attribute) {
                foreach ($items as $value) {
                    AttributeValue::create([
                        'product_id' => $product->id,
                        'attribute_id' => $attribute->id,
                        'text_value' => $value,
                    ]);
                }
            }
        }
    }
}
