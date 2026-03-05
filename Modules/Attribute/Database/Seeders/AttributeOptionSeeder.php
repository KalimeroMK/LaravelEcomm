<?php

declare(strict_types=1);

namespace Modules\Attribute\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Attribute\Models\Attribute;
use Modules\Attribute\Models\AttributeOption;

class AttributeOptionSeeder extends Seeder
{
    public function run(): void
    {
        $options = [
            'color' => [
                ['value' => 'red', 'label' => 'Red', 'color_hex' => '#FF0000'],
                ['value' => 'blue', 'label' => 'Blue', 'color_hex' => '#0000FF'],
                ['value' => 'green', 'label' => 'Green', 'color_hex' => '#00FF00'],
                ['value' => 'yellow', 'label' => 'Yellow', 'color_hex' => '#FFFF00'],
                ['value' => 'black', 'label' => 'Black', 'color_hex' => '#000000'],
                ['value' => 'white', 'label' => 'White', 'color_hex' => '#FFFFFF'],
            ],
            'size' => [
                ['value' => 'xs', 'label' => 'XS'],
                ['value' => 's', 'label' => 'S'],
                ['value' => 'm', 'label' => 'M'],
                ['value' => 'l', 'label' => 'L'],
                ['value' => 'xl', 'label' => 'XL'],
                ['value' => 'xxl', 'label' => 'XXL'],
            ],
            'material' => [
                ['value' => 'cotton', 'label' => 'Cotton'],
                ['value' => 'silk', 'label' => 'Silk'],
                ['value' => 'polyester', 'label' => 'Polyester'],
                ['value' => 'wool', 'label' => 'Wool'],
                ['value' => 'linen', 'label' => 'Linen'],
            ],
        ];

        foreach ($options as $code => $values) {
            $attribute = Attribute::where('code', $code)->first();
            if ($attribute) {
                foreach ($values as $index => $optionData) {
                    AttributeOption::firstOrCreate([
                        'attribute_id' => $attribute->id,
                        'value' => $optionData['value'],
                    ], [
                        'label' => $optionData['label'] ?? $optionData['value'],
                        'color_hex' => $optionData['color_hex'] ?? null,
                        'sort_order' => $index * 10,
                    ]);
                }
            }
        }

        $this->command->info('Attribute options seeded successfully!');
    }
}
