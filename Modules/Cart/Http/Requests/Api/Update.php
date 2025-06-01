<?php

declare(strict_types=1);

namespace Modules\Cart\Http\Requests\Api;

use Modules\Cart\Rules\ProductStockRule;
use Modules\Core\Http\Requests\Api\CoreRequest;

class Update extends CoreRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, string|array<int, string|ProductStockRule>>
     */
    public function rules(): array
    {
        return [
            'slug' => 'string|required|exists:products,slug',
            'quantity' => [
                'required',
                new ProductStockRule,
            ],

        ];
    }
}
