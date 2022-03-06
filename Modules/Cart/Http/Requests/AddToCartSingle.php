<?php

    namespace Modules\Cart\Http\Requests;

    use Illuminate\Foundation\Http\FormRequest;

    class AddToCartSingle extends FormRequest
    {
        /**
         * @var mixed
         */
        public mixed $slug;
        /**
         * @var mixed
         */
        public mixed $quant;

        public function rules(): array
        {
            return [
                'slug'  => 'required|string',
                'quant' => 'required',
            ];
        }

        public function authorize(): bool
        {
            return true;
        }
    }
