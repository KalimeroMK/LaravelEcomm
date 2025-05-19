<?php

declare(strict_types=1);

namespace Modules\Attribute\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Attribute\Models\AttributeGroup;

class AttributeGroupSeeder extends Seeder
{
    public function run(): void
    {
        $groups = [
            ['id' => 1, 'name' => 'General'],
            ['id' => 2, 'name' => 'Dimensions'],
            ['id' => 3, 'name' => 'Specifications'],
            ['id' => 4, 'name' => 'Marketing Info'],
        ];
        foreach ($groups as $group) {
            AttributeGroup::updateOrCreate(['id' => $group['id']], $group);
        }
    }
}
