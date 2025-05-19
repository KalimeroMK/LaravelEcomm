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
            'color' => ['Red', 'Blue', 'Green', 'Yellow'],
            'size' => ['S', 'M', 'L', 'XL'],
            'material' => ['Cotton', 'Silk', 'Polyester'],
        ];

        foreach ($options as $code => $values) {
            $attribute = Attribute::where('code', $code)->first();
            if ($attribute) {
                foreach ($values as $value) {
                    AttributeOption::firstOrCreate([
                        'attribute_id' => $attribute->id,
                        'value' => $value,
                    ]);
                }
            }
        }
    }
}
