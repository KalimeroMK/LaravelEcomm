<?php

    namespace Modules\Shipping\Http\Requests;

    use Illuminate\Foundation\Http\FormRequest;

    class Store extends FormRequest
    {
        public function rules(): array
        {
            return [
                'type'   => 'string|required',
                'price'  => 'nullable|numeric',
                'status' => 'required|in:active,inactive',
            ];
        }

        public function authorize(): bool
        {
            return true;
        }
    }
