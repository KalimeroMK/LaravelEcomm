<?php

declare(strict_types=1);

namespace Modules\Newsletter\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmailTemplateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'subject' => $this->subject,
            'html_content' => $this->html_content,
            'text_content' => $this->text_content,
            'template_type' => $this->template_type,
            'template_type_label' => EmailTemplate::getTemplateTypes()[$this->template_type] ?? $this->template_type,
            'is_active' => $this->is_active,
            'is_default' => $this->is_default,
            'settings' => $this->settings,
            'preview_data' => $this->preview_data,
            'preview_url' => $this->getPreviewUrl(),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'analytics' => [
                'total_sent' => $this->emailAnalytics()->count(),
                'total_opened' => $this->emailAnalytics()->whereNotNull('opened_at')->count(),
                'total_clicked' => $this->emailAnalytics()->whereNotNull('clicked_at')->count(),
                'open_rate' => $this->getOpenRate(),
                'click_rate' => $this->getClickRate(),
            ],
        ];
    }

    /**
     * Get open rate for this template
     */
    private function getOpenRate(): float
    {
        $totalSent = $this->emailAnalytics()->count();
        $totalOpened = $this->emailAnalytics()->whereNotNull('opened_at')->count();

        return $totalSent > 0 ? round(($totalOpened / $totalSent) * 100, 2) : 0;
    }

    /**
     * Get click rate for this template
     */
    private function getClickRate(): float
    {
        $totalSent = $this->emailAnalytics()->count();
        $totalClicked = $this->emailAnalytics()->whereNotNull('clicked_at')->count();

        return $totalSent > 0 ? round(($totalClicked / $totalSent) * 100, 2) : 0;
    }
}
