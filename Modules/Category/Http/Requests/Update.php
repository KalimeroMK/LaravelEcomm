<?php

namespace Modules\Category\Http\Requests;

use Exception;
use Illuminate\Foundation\Http\FormRequest;
use Modules\Category\Models\Category;

class Update extends FormRequest
{
    /**
     * @return string[]
     * @throws Exception
     */
    public function rules(): array
    {
        $category = $this->route('category');

        // Ensure that $category is indeed a Category model instance
        if (!$category instanceof Category) {
            throw new Exception("Expected a Category model instance.");
        }

        return [
            'title' => 'string|required',
            'summary' => 'string|nullable',
            'photo' => 'string|nullable',
            'status' => 'required|in:active,inactive',
            'is_parent' => 'sometimes|in:1',
            'parent_id' => 'nullable|not_in:'.$category->id,
        ];
    }


    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return void
     */
    public function passedValidation(): void
    {
        $this->merge([
            'id' => $this->route('categories'),
        ]);
    }
}
