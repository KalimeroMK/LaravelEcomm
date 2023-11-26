<?php

namespace Modules\Attribute\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Attribute\Models\Attribute;
use Modules\Attribute\Models\AttributeValue;

class AttributeValueSeeder extends Seeder
{
    public function run(): void
    {
        // Fetch some attributes
        $attributes = Attribute::all();

        foreach ($attributes as $attribute) {
            // Create a dummy value for each attribute type
            AttributeValue::create([
                'attribute_id' => $attribute->id,
                'url_value' => $attribute->type == Attribute::TYPE_URL ? 'http://example.com' : null,
                'hex_value' => $attribute->type == Attribute::TYPE_HEX ? '#FFFFFF' : null,
                'text_value' => $attribute->type == Attribute::TYPE_TEXT ? 'Sample Text' : null,
                // Add other values for different types
            ]);
        }
    }
}
