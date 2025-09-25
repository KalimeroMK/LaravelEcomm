<?php

declare(strict_types=1);

namespace Modules\Newsletter\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Http\Controllers\Api\CoreController;
use Modules\Newsletter\Models\EmailTemplate;
use Modules\Newsletter\Models\Newsletter;
use Modules\Newsletter\Services\NewsletterService;
use Modules\Post\Models\Post;
use Modules\Product\Models\Product;

class EmailCampaignController extends CoreController
{
    public function __construct(
        private readonly NewsletterService $newsletterService
    ) {}

    public function index(): JsonResponse
    {
        $templates = EmailTemplate::active()->get();
        $subscribers = Newsletter::where('is_validated', true)->count();

        return $this->respond([
            'templates_count' => $templates->count(),
            'subscribers_count' => $subscribers,
            'campaigns_sent' => 0, // TODO: Implement campaign tracking
            'average_open_rate' => 0, // TODO: Implement analytics
            'templates' => $templates,
        ]);
    }

    public function create(): JsonResponse
    {
        $templates = EmailTemplate::active()->get();
        $posts = Post::where('status', 'active')->orderBy('created_at', 'desc')->limit(10)->get();
        $products = Product::where('status', 'active')->where('is_featured', true)->orderBy('created_at', 'desc')->limit(10)->get();

        return $this->respond([
            'templates' => $templates,
            'posts' => $posts,
            'products' => $products,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'template_id' => 'required|exists:email_templates,id',
            'subject' => 'required|string|max:255',
            'include_posts' => 'boolean',
            'include_products' => 'boolean',
            'post_limit' => 'integer|min:1|max:10',
            'product_limit' => 'integer|min:1|max:10',
            'send_to' => 'required|in:all,segment',
            'segment_criteria' => 'nullable|array',
        ]);

        $template = EmailTemplate::findOrFail($request->template_id);

        $posts = [];
        $products = [];

        if ($request->boolean('include_posts')) {
            $posts = Post::where('status', 'active')
                ->orderBy('created_at', 'desc')
                ->limit($request->get('post_limit', 3))
                ->get()
                ->toArray();
        }

        if ($request->boolean('include_products')) {
            $products = Product::where('status', 'active')
                ->where('is_featured', true)
                ->orderBy('created_at', 'desc')
                ->limit($request->get('product_limit', 5))
                ->get()
                ->toArray();
        }

        if ($request->send_to === 'all') {
            $results = $this->newsletterService->sendNewsletterToAll($posts, $products);
        } else {
            $results = $this->newsletterService->sendNewsletterToSegment($posts, $products, $template);
        }

        return $this
            ->setMessage('Email campaign sent successfully!')
            ->respond([
                'sent_count' => $results['sent_count'],
                'results' => $results,
            ]);
    }

    public function preview(Request $request): JsonResponse
    {
        $request->validate([
            'template_id' => 'required|exists:email_templates,id',
            'include_posts' => 'boolean',
            'include_products' => 'boolean',
            'post_limit' => 'integer|min:1|max:10',
            'product_limit' => 'integer|min:1|max:10',
        ]);

        $template = EmailTemplate::findOrFail($request->template_id);

        $posts = [];
        $products = [];

        if ($request->boolean('include_posts')) {
            $posts = Post::where('status', 'active')
                ->orderBy('created_at', 'desc')
                ->limit($request->get('post_limit', 3))
                ->get();
        }

        if ($request->boolean('include_products')) {
            $products = Product::where('status', 'active')
                ->where('is_featured', true)
                ->orderBy('created_at', 'desc')
                ->limit($request->get('product_limit', 5))
                ->get();
        }

        return $this->respond([
            'template' => $template,
            'posts' => $posts,
            'products' => $products,
            'preview_html' => $this->generatePreviewHtml($template),
        ]);
    }

    public function analytics(): JsonResponse
    {
        $analytics = $this->newsletterService->getCampaignAnalytics();

        return $this->respond($analytics);
    }

    private function generatePreviewHtml($template): string
    {
        // Generate preview HTML based on template and content
        $html = $template->html_content;

        // Replace placeholders with sample data
        $html = str_replace('{{name}}', 'John Doe', $html);
        $html = str_replace('{{email}}', 'john@example.com', $html);

        return str_replace('{{company}}', config('app.name'), $html);
    }
}
