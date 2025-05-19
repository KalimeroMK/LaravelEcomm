<?php

declare(strict_types=1);

namespace Modules\Category\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Modules\Category\Models\Category;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    /**
     * @return array<string, string>
     */
    public function definition(): array
    {
        return [
            'title' => 'Title-'.mb_strtoupper(Str::random(10)),
            'slug' => 'Slug-'.mb_strtoupper(Str::random(10)),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
