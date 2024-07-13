<?php

namespace Modules\Attribute\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Attribute\Models\Attribute;
use Modules\Attribute\Models\AttributeValue;

class AttributeValueSeeder extends Seeder
{
    public function run(): void
    {
        $attributes = Attribute::all();

        foreach ($attributes as $attribute) {
            // Create a dummy value for each attribute type
            AttributeValue::create([
                'attribute_id' => $attribute->id,
                'attributable_type' => 'App\Models\Product', // or any other model class you are using
                'attributable_id' => 1, // assuming there is a product with ID 1
                'url_value' => $attribute->type === Attribute::TYPE_URL ? 'http://example.com' : null,
                'hex_value' => $attribute->type === Attribute::TYPE_HEX ? '#FFFFFF' : null,
                'text_value' => $attribute->type === Attribute::TYPE_TEXT ? 'Sample Text' : null,
                'date_value' => $attribute->type === Attribute::TYPE_DATE ? now() : null,
                'time_value' => $attribute->type === Attribute::TYPE_TIME ? now() : null,
                'float_value' => $attribute->type === Attribute::TYPE_FLOAT ? 1.23 : null,
                'string_value' => $attribute->type === Attribute::TYPE_STRING ? 'Sample String' : null,
                'boolean_value' => $attribute->type === Attribute::TYPE_BOOLEAN ? true : null,
                'integer_value' => $attribute->type === Attribute::TYPE_INTEGER ? 123 : null,
                'decimal_value' => $attribute->type === Attribute::TYPE_DECIMAL ? 12.3456 : null,
            ]);
        }
    }
}
