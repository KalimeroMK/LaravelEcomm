<?php

declare(strict_types=1);

namespace Modules\Core\Support\Media;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\FileAdder;

class MediaUploader
{
    public static function uploadMultiple(Model&HasMedia $model, array $fields, string $collection = 'default'): void
    {
        foreach ($fields as $field) {
            if (request()->hasFile($field)) {
                $model->addMultipleMediaFromRequest([$field])
                    ->each(fn (
                        FileAdder $fileAdder
                    ): \Spatie\MediaLibrary\MediaCollections\Models\Media => $fileAdder->preservingOriginal()->toMediaCollection($collection));
            }
        }
    }

    public static function uploadSingle(Model&HasMedia $model, string $field, string $collection = 'default'): void
    {
        if (request()->hasFile($field)) {
            $model->addMediaFromRequest($field)
                ->preservingOriginal()
                ->toMediaCollection($collection);
        }
    }

    public static function clearAndUpload(Model&HasMedia $model, array $fields, string $collection): void
    {
        $model->clearMediaCollection($collection);
        self::uploadMultiple($model, $fields, $collection);
    }
}
