<?php

namespace Modules\Core\Traits;

use Faker\Generator;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

trait HasNewFactory
{
    use HasFactory;
    
    protected static function newFactory(): Factory
    {
        $factory = new class extends Factory {
            public static $definition;
            
            public function definition()
            {
                $definition = static::$definition;
                
                return $definition($this->faker);
            }
        };
        
        $factory::guessModelNamesUsing(function () {
            return get_called_class();
        });
        
        $factory::$definition = function (Generator $faker) {
            return (new static)->definition($faker);
        };
        
        return $factory;
    }
}