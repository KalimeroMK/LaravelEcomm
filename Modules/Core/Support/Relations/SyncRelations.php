<?php

declare(strict_types=1);

namespace Modules\Core\Support\Relations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SyncRelations
{
    public static function execute(Model $model, array $relations): void
    {
        $hasChanges = false;

        foreach ($relations as $relation => $value) {
            if (! method_exists($model, $relation)) {
                continue;
            }

            $relationInstance = $model->{$relation}();
            // BelongsToMany: sync()
            if ($relationInstance instanceof BelongsToMany && is_array($value)) {
                $relationInstance->sync($value);

                continue;
            }

            // BelongsTo: set foreign key directly
            if ($relationInstance instanceof BelongsTo && (is_scalar($value) || is_null($value))) {
                $foreignKey = $relationInstance->getForeignKeyName();

                if ($model->{$foreignKey} !== $value) {
                    $model->{$foreignKey} = $value;
                    $hasChanges = true;
                }
            }
        }

        if ($hasChanges) {
            $model->save();
        }
    }
}
