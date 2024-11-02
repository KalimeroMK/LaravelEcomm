<?php

namespace Modules\Complaint\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Store extends FormRequest
{
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'exists:users,id', 'in:'.auth()->id()],
            'order_id' => ['required', 'exists:orders'],
            'status' => ['required'],
            'description' => ['required|string'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
