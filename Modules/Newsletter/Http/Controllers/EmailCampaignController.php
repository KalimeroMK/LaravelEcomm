<?php

declare(strict_types=1);

namespace Modules\Newsletter\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Newsletter\Models\EmailTemplate;
use Modules\Newsletter\Models\Newsletter;
use Modules\Newsletter\Services\NewsletterService;
use Modules\Post\Models\Post;
use Modules\Product\Models\Product;

class EmailCampaignController extends CoreController
{
    public function __construct(
        private readonly NewsletterService $newsletterService
    ) {
        // Constructor with dependency injection
    }

    public function index(): View
    {
        $templates = EmailTemplate::active()->get();
        $subscribers = Newsletter::where('is_validated', true)->count();
        
        return view('newsletter::email-campaigns.index', compact('templates', 'subscribers'));
    }

    public function create(): View
    {
        $templates = EmailTemplate::active()->get();
        $posts = Post::where('status', 'active')->orderBy('created_at', 'desc')->limit(10)->get();
        $products = Product::where('status', 'active')->where('is_featured', true)->orderBy('created_at', 'desc')->limit(10)->get();
        
        return view('newsletter::email-campaigns.create', compact('templates', 'posts', 'products'));
    }

    public function store(Request $request): RedirectResponse
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
            $results = $this->newsletterService->sendNewsletterToAll($posts, $products, $template);
        } else {
            $results = $this->newsletterService->sendNewsletterToSegment($posts, $products, $template, $request->segment_criteria);
        }

        return redirect()
            ->route('admin.email-campaigns.index')
            ->with('success', 'Email campaign sent successfully! ' . $results['sent_count'] . ' emails sent.');
    }

    public function preview(Request $request): View
    {
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

        return view('newsletter::email-campaigns.preview', compact('template', 'posts', 'products'));
    }

    public function analytics(): View
    {
        $newsletterService = new \Modules\Newsletter\Services\NewsletterService();
        $analytics = $newsletterService->getAllCampaignsAnalytics();
        
        return view('newsletter::email-campaigns.analytics-test', compact('analytics'));
    }

    public function analyticsApi(): \Illuminate\Http\JsonResponse
    {
        $analytics = $this->newsletterService->getAllCampaignsAnalytics();
        
        return response()->json([
            'success' => true,
            'data' => $analytics,
        ]);
    }
}

