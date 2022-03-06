<?php

    namespace Modules\Post\Http\Requests;

    use Illuminate\Foundation\Http\FormRequest;

    class PostCategoryStore extends FormRequest
    {
        public function rules(): array
        {
            return [
                'title'  => 'string|required',
                'status' => 'required|in:active,inactive',
            ];
        }

        public function authorize(): bool
        {
            return true;
        }
    }