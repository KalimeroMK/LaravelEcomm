<?php

declare(strict_types=1);

namespace Modules\Attribute\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Attribute\Models\Attribute;
use Modules\Attribute\Models\AttributeFamily;
use Modules\Attribute\Models\AttributeGroup;

class AttributeFamilySeeder extends Seeder
{
    public function run(): void
    {
        // Create Attribute Families
        $families = [
            [
                'name' => 'Clothing',
                'code' => 'clothing',
                'description' => 'Attributes for clothing items',
                'attributes' => [
                    'General' => ['color', 'size', 'material', 'brand'],
                    'Dimensions' => ['weight'],
                ],
            ],
            [
                'name' => 'Electronics',
                'code' => 'electronics',
                'description' => 'Attributes for electronic products',
                'attributes' => [
                    'General' => ['brand', 'is_new'],
                    'Specifications' => ['weight', 'length', 'width', 'height'],
                ],
            ],
            [
                'name' => 'Furniture',
                'code' => 'furniture',
                'description' => 'Attributes for furniture',
                'attributes' => [
                    'General' => ['color', 'material', 'brand'],
                    'Dimensions' => ['weight', 'length', 'width', 'height'],
                ],
            ],
            [
                'name' => 'Default',
                'code' => 'default',
                'description' => 'Default attribute family for all products',
                'attributes' => [
                    'General' => ['color', 'size', 'material', 'brand'],
                    'Specifications' => ['is_new'],
                ],
            ],
        ];

        foreach ($families as $familyData) {
            $family = AttributeFamily::firstOrCreate(
                ['code' => $familyData['code']],
                [
                    'name' => $familyData['name'],
                    'description' => $familyData['description'],
                    'is_active' => true,
                ]
            );

            // Attach attributes to family
            foreach ($familyData['attributes'] as $groupName => $attributeCodes) {
                $group = AttributeGroup::where('name', $groupName)->first();

                if (! $group) {
                    continue;
                }

                $attributes = Attribute::whereIn('code', $attributeCodes)->get();

                foreach ($attributes as $index => $attribute) {
                    // Check if already attached to avoid duplicate
                    $exists = $family->attributes()
                        ->where('attribute_id', $attribute->id)
                        ->exists();

                    if (! $exists) {
                        $family->attributes()->attach($attribute->id, [
                            'attribute_group_id' => $group->id,
                            'position' => $index * 10,
                            'is_required' => in_array($attribute->code, ['brand']),
                        ]);
                    }
                }
            }
        }

        $this->command->info('Attribute families seeded successfully!');
    }
}
