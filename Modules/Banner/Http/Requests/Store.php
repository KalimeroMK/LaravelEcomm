<?php

    namespace Modules\Banner\Http\Requests;

    use Illuminate\Foundation\Http\FormRequest;
    use JetBrains\PhpStorm\ArrayShape;

    class Store extends FormRequest
    {
        #[ArrayShape([
            'title'       => "string",
            'description' => "string",
            'photo'       => "string",
            'status'      => "string",
        ])]
        public function rules(): array
        {
            return [
                'title'       => 'string|required|max:50|unique:banners',
                'description' => 'string|nullable',
                'photo'       => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
                'status'      => 'required|in:active,inactive',
            ];
        }

        public function authorize(): bool
        {
            return true;
        }
    }
