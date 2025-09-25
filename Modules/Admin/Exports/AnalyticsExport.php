<?php

declare(strict_types=1);

namespace Modules\Admin\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class AnalyticsExport implements FromArray, WithHeadings, WithTitle
{
    protected array $data;

    protected string $title;

    public function __construct(array $data, string $title = 'Analytics')
    {
        $this->data = $data;
        $this->title = $title;
    }

    public function array(): array
    {
        return $this->convertToExcelData($this->data);
    }

    public function headings(): array
    {
        return ['Metric', 'Value'];
    }

    public function title(): string
    {
        return $this->title;
    }

    private function convertToExcelData(array $data): array
    {
        $rows = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $subKey => $subValue) {
                    $rows[] = [
                        ucfirst(str_replace('_', ' ', $key)).' - '.ucfirst(str_replace('_', ' ', $subKey)),
                        is_array($subValue) ? json_encode($subValue) : $subValue,
                    ];
                }
            } else {
                $rows[] = [
                    ucfirst(str_replace('_', ' ', $key)),
                    $value,
                ];
            }
        }

        return $rows;
    }
}
