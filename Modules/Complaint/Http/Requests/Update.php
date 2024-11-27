<?php

namespace Modules\Complaint\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Update extends FormRequest
{
    public function rules(): array
    {
        return [
            'reply_content' => 'required|string',
            'status' => 'nullable|in:open,in_progress,closed',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
