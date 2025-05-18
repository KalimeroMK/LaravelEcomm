<?php

declare(strict_types=1);

namespace Modules\Attribute\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Attribute\Database\Factories\AttributeGroupFactory;

class AttributeGroup extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public static function Factory(): AttributeGroupFactory
    {
        return AttributeGroupFactory::new();
    }

    public function attributes()
    {
        return $this->hasMany(Attribute::class);
    }
}
