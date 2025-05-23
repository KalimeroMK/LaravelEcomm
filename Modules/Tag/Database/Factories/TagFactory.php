<?php

declare(strict_types=1);

namespace Modules\Tag\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Modules\Tag\Models\Tag;

class TagFactory extends Factory
{
    protected $model = Tag::class;

    /**
     * Define the model's default state.
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
