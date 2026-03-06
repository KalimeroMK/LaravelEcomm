<?php

declare(strict_types=1);

namespace Modules\Reporting\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Reporting\Models\Report;

class ReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $reportId = $this->route('report')?->id;

        return [
            'name' => 'required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('reports', 'slug')->ignore($reportId),
            ],
            'description' => 'nullable|string',
            'type' => ['required', 'string', Rule::in(Report::TYPES)],
            'format' => ['required', 'string', Rule::in(Report::FORMATS)],
            'filters' => 'nullable|array',
            'columns' => 'nullable|array',
            'grouping' => 'nullable|array',
            'sorting' => 'nullable|array',
            'is_template' => 'nullable|boolean',
            'is_public' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Report name is required.',
            'type.required' => 'Report type is required.',
            'type.in' => 'Invalid report type.',
            'format.required' => 'Export format is required.',
            'format.in' => 'Invalid export format.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_template' => $this->boolean('is_template'),
            'is_public' => $this->boolean('is_public'),
            'sort_order' => $this->input('sort_order', 0),
        ]);
    }
}
