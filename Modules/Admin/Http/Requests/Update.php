<?php

namespace Modules\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Update extends FormRequest
{
    public function rules(): array
    {
        return [
            'short_des'   => 'required|string',
            'description' => 'required|string',
            'logo'        => 'required',
            'address'     => 'required|string',
            'email'       => 'required|email',
            'phone'       => 'required|string',
        ];
    }
    
    public function authorize(): bool
    {
        return true;
    }
}
