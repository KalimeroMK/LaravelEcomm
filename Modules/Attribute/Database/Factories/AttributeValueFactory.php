<?php

namespace Modules\Attribute\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Modules\Attribute\Models\Attribute;
use Modules\Attribute\Models\AttributeValue;

class AttributeValueFactory extends Factory
{
    protected $model = AttributeValue::class;
    
    public function definition(): array
    {
        return [
            'default'       => $this->faker->word(),
            'text_value'    => $this->faker->text(),
            'date_value'    => Carbon::now(),
            'time_value'    => Carbon::now(),
            'url_value'     => $this->faker->url(),
            'hex_value'     => $this->faker->word(),
//            'float_value'   => $this->faker->randomFloat(2),
            'string_value'  => $this->faker->word(),
            'boolean_value' => $this->faker->boolean(),
            'integer_value' => $this->faker->randomNumber(),
//            'decimal_value' => $this->faker->randomFloat(2),
            'created_at'    => Carbon::now(),
            'updated_at'    => Carbon::now(),
            
            'attribute_id' => Attribute::factory(),
        ];
    }
}
