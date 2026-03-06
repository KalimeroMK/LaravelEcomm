<?php

declare(strict_types=1);

namespace Modules\Reporting\Actions;

use Illuminate\Support\Collection;
use Modules\Reporting\Models\Report;
use Modules\Reporting\Services\ReportDataService;
use Symfony\Component\HttpFoundation\StreamedResponse;

readonly class ExportReportAction
{
    public function __construct(
        private ReportDataService $dataService,
    ) {}

    /**
     * Export report to specified format
     */
    public function execute(Report $report, string $format, array $parameters = []): StreamedResponse|string
    {
        $result = $this->dataService->generate($report, $parameters);
        $data = $result['data'];

        return match ($format) {
            'csv' => $this->exportToCsv($report, $data),
            'excel' => $this->exportToExcel($report, $data),
            'pdf' => $this->exportToPdf($report, $data, $result['summary']),
            default => throw new \InvalidArgumentException("Unsupported format: {$format}"),
        };
    }

    /**
     * Export to CSV format
     */
    private function exportToCsv(Report $report, Collection $data): StreamedResponse
    {
        $filename = $this->generateFilename($report, 'csv');
        $headers = $this->getCsvHeaders($data);

        $response = new StreamedResponse(function () use ($data, $headers): void {
            $handle = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            
            // Headers
            fputcsv($handle, $headers);
            
            // Data
            foreach ($data as $row) {
                fputcsv($handle, $this->flattenRow($row, $headers));
            }
            
            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');

        return $response;
    }

    /**
     * Export to Excel format using SimpleExcel or similar
     */
    private function exportToExcel(Report $report, Collection $data): StreamedResponse
    {
        $filename = $this->generateFilename($report, 'xlsx');
        
        // For now, we'll use CSV with Excel MIME type
        // In production, you'd use a library like PhpSpreadsheet or Laravel Excel
        $headers = $this->getCsvHeaders($data);

        $response = new StreamedResponse(function () use ($data, $headers): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers);
            foreach ($data as $row) {
                fputcsv($handle, $this->flattenRow($row, $headers));
            }
            fclose($handle);
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');

        return $response;
    }

    /**
     * Export to PDF format
     */
    private function exportToPdf(Report $report, Collection $data, array $summary): string
    {
        // In production, you'd use a library like DomPDF or Laravel PDF
        // For now, return HTML content
        $html = view('reporting::pdf.report', [
            'report' => $report,
            'data' => $data,
            'summary' => $summary,
        ])->render();

        return $html;
    }

    /**
     * Get CSV headers from data
     *
     * @return array<string>
     */
    private function getCsvHeaders(Collection $data): array
    {
        if ($data->isEmpty()) {
            return [];
        }

        return array_keys($data->first());
    }

    /**
     * Flatten row data for CSV
     *
     * @param array<string, mixed> $row
     * @param array<string> $headers
     * @return array<mixed>
     */
    private function flattenRow(array $row, array $headers): array
    {
        $result = [];
        foreach ($headers as $header) {
            $value = $row[$header] ?? '';
            // Format numbers
            if (is_numeric($value) && str_contains($header, 'price') || str_contains($header, 'total') || str_contains($header, 'revenue') || str_contains($header, 'spent')) {
                $value = number_format((float) $value, 2);
            }
            $result[] = $value;
        }
        return $result;
    }

    /**
     * Generate filename for export
     */
    private function generateFilename(Report $report, string $extension): string
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $slug = \Illuminate\Support\Str::slug($report->name);
        
        return "{$slug}_{$timestamp}.{$extension}";
    }
}
