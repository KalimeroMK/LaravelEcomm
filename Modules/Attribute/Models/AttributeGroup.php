<?php

namespace Modules\Attribute\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Attribute\Database\Factories\AttributeGroupFactory;

class AttributeGroup extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function attributes()
    {
        return $this->hasMany(Attribute::class);
    }

    public static function Factory(): AttributeGroupFactory
    {
        return AttributeGroupFactory::new();
    }
}
