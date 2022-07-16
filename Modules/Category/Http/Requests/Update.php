<?php

namespace Modules\Category\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Update extends FormRequest
{
    /**
     * @return string[]
     */
    public function rules(): array
    {
        return [
            'title'     => 'string|required',
            'summary'   => 'string|nullable',
            'photo'     => 'string|nullable',
            'status'    => 'required|in:active,inactive',
            'is_parent' => 'sometimes|in:1',
            'parent_id' => 'not_in:'.$this->route('category')->id,
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
