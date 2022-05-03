<?php

    namespace Modules\Brand\Http\Requests\Api;

    use App\Helpers\ApiRequest;
    use JetBrains\PhpStorm\ArrayShape;

    class StoreRequest extends ApiRequest
    {
        #[ArrayShape([
            'title' => "string",
        ])] public function rules(): array
        {
            return [
                'title' => 'string|required|unique:brands',
            ];
        }

        public function authorize(): bool
        {
            return true;
        }
    }
