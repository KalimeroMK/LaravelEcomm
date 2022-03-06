<?php

    namespace Modules\Product\Http\Requests;

    use Illuminate\Foundation\Http\FormRequest;

    class ProductReviewStore extends FormRequest
    {
        /**
         * @var mixed
         */
        public $slug;

        public function rules(): array
        {
            return [
                'rate' => 'required|numeric|min:1',
            ];
        }

        public function authorize(): bool
        {
            return true;
        }
    }
