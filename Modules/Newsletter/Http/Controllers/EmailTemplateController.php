<?php

declare(strict_types=1);

namespace Modules\Newsletter\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Newsletter\Models\EmailTemplate;

class EmailTemplateController extends CoreController
{
    public function index(): View
    {
        $templates = EmailTemplate::orderBy('template_type')
            ->orderBy('name')
            ->paginate(15);

        return view('newsletter::email-templates.index', ['templates' => $templates]);
    }

    public function create(): View
    {
        $templateTypes = EmailTemplate::getTemplateTypes();
        $template = new EmailTemplate;

        return view('newsletter::email-templates.create', ['templateTypes' => $templateTypes, 'template' => $template]);
    }

    public function store(Request $request): RedirectResponse
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

        return redirect()
            ->route('admin.email-templates.index')
            ->with('success', 'Email template created successfully!');
    }

    public function show(EmailTemplate $emailTemplate): View
    {
        return view('newsletter::email-templates.show', ['emailTemplate' => $emailTemplate]);
    }

    public function edit(EmailTemplate $emailTemplate): View
    {
        $templateTypes = EmailTemplate::getTemplateTypes();

        return view('newsletter::email-templates.edit', ['emailTemplate' => $emailTemplate, 'templateTypes' => $templateTypes]);
    }

    public function update(Request $request, EmailTemplate $emailTemplate): RedirectResponse
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

        return redirect()
            ->route('admin.email-templates.index')
            ->with('success', 'Email template updated successfully!');
    }

    public function destroy(EmailTemplate $emailTemplate): RedirectResponse
    {
        // Don't allow deletion of default templates
        if ($emailTemplate->is_default) {
            return redirect()
                ->route('admin.email-templates.index')
                ->with('error', 'Cannot delete default template. Please set another template as default first.');
        }

        $emailTemplate->delete();

        return redirect()
            ->route('admin.email-templates.index')
            ->with('success', 'Email template deleted successfully!');
    }

    public function preview(EmailTemplate $emailTemplate): View
    {
        return view('newsletter::email-templates.preview', ['emailTemplate' => $emailTemplate]);
    }

    public function duplicate(EmailTemplate $emailTemplate): RedirectResponse
    {
        $newTemplate = $emailTemplate->replicate();
        $newTemplate->name = $emailTemplate->name.' (Copy)';
        $newTemplate->is_default = false;
        $newTemplate->save();

        return redirect()
            ->route('admin.email-templates.edit', $newTemplate)
            ->with('success', 'Email template duplicated successfully!');
    }

    public function setDefault(EmailTemplate $emailTemplate): RedirectResponse
    {
        $emailTemplate->setAsDefault();

        return redirect()
            ->route('admin.email-templates.index')
            ->with('success', 'Template set as default successfully!');
    }

    public function toggleActive(EmailTemplate $emailTemplate): RedirectResponse
    {
        $emailTemplate->update(['is_active' => ! $emailTemplate->is_active]);

        $status = $emailTemplate->is_active ? 'activated' : 'deactivated';

        return redirect()
            ->route('admin.email-templates.index')
            ->with('success', "Template {$status} successfully!");
    }

    public function usage(EmailTemplate $emailTemplate): View
    {
        // Get usage statistics
        $usageStats = [
            'total_campaigns' => 0, // This would come from a campaigns table
            'total_sent' => $emailTemplate->emailAnalytics()->count(),
            'total_opened' => $emailTemplate->emailAnalytics()->whereNotNull('opened_at')->count(),
            'total_clicked' => $emailTemplate->emailAnalytics()->whereNotNull('clicked_at')->count(),
        ];

        // Get recent campaigns (placeholder - would need campaigns table)
        $recentCampaigns = collect([]);

        return view('newsletter::email-templates.usage', ['emailTemplate' => $emailTemplate, 'usageStats' => $usageStats, 'recentCampaigns' => $recentCampaigns]);
    }
}
