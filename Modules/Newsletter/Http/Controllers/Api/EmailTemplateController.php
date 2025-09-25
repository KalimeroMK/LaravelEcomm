<?php

declare(strict_types=1);

namespace Modules\Newsletter\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Core\Http\Controllers\Api\CoreController;
use Modules\Newsletter\Http\Resources\EmailTemplateResource;
use Modules\Newsletter\Models\EmailTemplate;

class EmailTemplateController extends CoreController
{
    public function index(): ResourceCollection
    {
        $templates = EmailTemplate::orderBy('template_type')
            ->orderBy('name')
            ->paginate(15);

        return EmailTemplateResource::collection($templates);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'html_content' => 'required|string',
            'text_content' => 'nullable|string',
            'template_type' => 'required|string|in:'.implode(',', array_keys(EmailTemplate::getTemplateTypes())),
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'settings' => 'nullable|array',
            'preview_data' => 'nullable|array',
        ]);

        $template = EmailTemplate::create($request->all());

        // If this is set as default, update others
        if ($request->boolean('is_default')) {
            $template->setAsDefault();
        }

        return $this
            ->setMessage('Email template created successfully!')
            ->respond(new EmailTemplateResource($template));
    }

    public function show(EmailTemplate $emailTemplate): JsonResponse
    {
        return $this->respond(new EmailTemplateResource($emailTemplate));
    }

    public function update(Request $request, EmailTemplate $emailTemplate): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'html_content' => 'required|string',
            'text_content' => 'nullable|string',
            'template_type' => 'required|string|in:'.implode(',', array_keys(EmailTemplate::getTemplateTypes())),
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'settings' => 'nullable|array',
            'preview_data' => 'nullable|array',
        ]);

        $emailTemplate->update($request->all());

        // If this is set as default, update others
        if ($request->boolean('is_default')) {
            $emailTemplate->setAsDefault();
        }

        return $this
            ->setMessage('Email template updated successfully!')
            ->respond(new EmailTemplateResource($emailTemplate));
    }

    public function destroy(EmailTemplate $emailTemplate): JsonResponse
    {
        // Don't allow deletion of default templates
        if ($emailTemplate->is_default) {
            return $this
                ->setMessage('Cannot delete default template. Please set another template as default first.')
                ->setStatusCode(422)
                ->respond();
        }

        $emailTemplate->delete();

        return $this
            ->setMessage('Email template deleted successfully!')
            ->respond();
    }

    public function preview(EmailTemplate $emailTemplate): JsonResponse
    {
        return $this->respond([
            'template' => new EmailTemplateResource($emailTemplate),
            'preview_url' => $emailTemplate->getPreviewUrl(),
        ]);
    }

    public function duplicate(EmailTemplate $emailTemplate): JsonResponse
    {
        $newTemplate = $emailTemplate->replicate();
        $newTemplate->name = $emailTemplate->name.' (Copy)';
        $newTemplate->is_default = false;
        $newTemplate->save();

        return $this
            ->setMessage('Email template duplicated successfully!')
            ->respond(new EmailTemplateResource($newTemplate));
    }

    public function setDefault(EmailTemplate $emailTemplate): JsonResponse
    {
        $emailTemplate->setAsDefault();

        return $this
            ->setMessage('Template set as default successfully!')
            ->respond(new EmailTemplateResource($emailTemplate));
    }

    public function toggleActive(EmailTemplate $emailTemplate): JsonResponse
    {
        $emailTemplate->update(['is_active' => ! $emailTemplate->is_active]);

        $status = $emailTemplate->is_active ? 'activated' : 'deactivated';

        return $this
            ->setMessage("Template {$status} successfully!")
            ->respond(new EmailTemplateResource($emailTemplate));
    }

    public function getTemplateTypes(): JsonResponse
    {
        return $this->respond([
            'template_types' => EmailTemplate::getTemplateTypes(),
        ]);
    }

    public function getByType(string $type): ResourceCollection
    {
        $templates = EmailTemplate::ofType($type)
            ->active()
            ->orderBy('name')
            ->get();

        return EmailTemplateResource::collection($templates);
    }
}
