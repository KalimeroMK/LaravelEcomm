<?php

namespace Modules\Bundle\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Search extends FormRequest
{
    public function rules(): array
    {
        return [

        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
