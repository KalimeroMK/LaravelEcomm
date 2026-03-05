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
        // Get or create a sample product
        /** @var Product|null $product */
        $product = Product::first();
        if (! $product instanceof Product) {
            $product = Product::factory()->create(['title' => 'Sample Product with Attributes']);
        }

        if (! $product instanceof Product) {
            $this->command->warn('No products found. Skipping attribute values seeding.');

            return;
        }

        // Define attribute values to assign
        $values = [
            'color' => ['red', 'blue', 'green'], // Multiple colors for configurable product simulation
            'size' => ['s', 'm', 'l'],           // Multiple sizes
            'material' => ['cotton'],
            'brand' => ['Nike'],
            'weight' => 0.5,                      // Decimal value
            'is_new' => true,                     // Boolean value
        ];

        foreach ($values as $code => $items) {
            $attribute = Attribute::where('code', $code)->first();

            if (! $attribute) {
                continue;
            }

            // Handle array values (multiple values for same attribute)
            $itemsArray = is_array($items) ? $items : [$items];

            foreach ($itemsArray as $value) {
                $data = [
                    'attribute_id' => $attribute->id,
                    'attributable_id' => $product->id,
                    'attributable_type' => Product::class,
                ];

                // Set value based on attribute type
                switch ($attribute->type) {
                    case 'boolean':
                        $data['boolean_value'] = (bool) $value;
                        break;
                    case 'integer':
                        $data['integer_value'] = (int) $value;
                        break;
                    case 'float':
                    case 'decimal':
                        $data['decimal_value'] = (float) $value;
                        break;
                    case 'date':
                        $data['date_value'] = $value;
                        break;
                    case 'string':
                        $data['string_value'] = (string) $value;
                        break;
                    case 'url':
                        $data['url_value'] = (string) $value;
                        break;
                    case 'text':
                    case 'select':
                    default:
                        $data['text_value'] = (string) $value;
                        break;
                }

                AttributeValue::firstOrCreate([
                    'attribute_id' => $attribute->id,
                    'attributable_id' => $product->id,
                    'attributable_type' => Product::class,
                    'text_value' => $attribute->type === 'text' || $attribute->type === 'select' ? (string) $value : null,
                ], $data);
            }
        }

        $this->command->info("Attribute values seeded for product: {$product->title}");
    }
}
