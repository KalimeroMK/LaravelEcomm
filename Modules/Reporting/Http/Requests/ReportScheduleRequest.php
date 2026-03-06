<?php

declare(strict_types=1);

namespace Modules\Reporting\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Reporting\Models\ReportSchedule;

class ReportScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'report_id' => 'required|exists:reports,id',
            'frequency' => ['required', 'string', Rule::in([
                ReportSchedule::FREQUENCY_DAILY,
                ReportSchedule::FREQUENCY_WEEKLY,
                ReportSchedule::FREQUENCY_BIWEEKLY,
                ReportSchedule::FREQUENCY_MONTHLY,
                ReportSchedule::FREQUENCY_QUARTERLY,
            ])],
            'day_of_week' => [
                'nullable',
                'required_if:frequency,weekly',
                Rule::in(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']),
            ],
            'day_of_month' => [
                'nullable',
                'required_if:frequency,monthly,quarterly',
                'integer',
                'min:1',
                'max:31',
            ],
            'time' => 'required|date_format:H:i',
            'timezone' => 'required|string|timezone',
            'date_range_type' => ['required', 'string', Rule::in([
                ReportSchedule::DATE_RANGE_LAST_7_DAYS,
                ReportSchedule::DATE_RANGE_LAST_30_DAYS,
                ReportSchedule::DATE_RANGE_LAST_MONTH,
                ReportSchedule::DATE_RANGE_LAST_QUARTER,
                ReportSchedule::DATE_RANGE_LAST_YEAR,
                ReportSchedule::DATE_RANGE_CUSTOM,
            ])],
            'date_range_days' => 'nullable|integer|min:1',
            'recipients' => 'required|array|min:1',
            'recipients.*' => 'required|email',
            'subject' => 'nullable|string|max:255',
            'message' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'report_id.required' => 'Report is required.',
            'report_id.exists' => 'Selected report does not exist.',
            'frequency.required' => 'Frequency is required.',
            'frequency.in' => 'Invalid frequency.',
            'recipients.required' => 'At least one recipient is required.',
            'recipients.*.email' => 'Invalid email address.',
            'time.required' => 'Time is required.',
            'time.date_format' => 'Time must be in HH:MM format.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active', true),
        ]);
    }
}
