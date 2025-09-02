<?php

declare(strict_types=1);

namespace Modules\Newsletter\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Newsletter\Services\NewsletterService;
use Modules\Post\Models\Post;
use Modules\Product\Models\Product;

class NewsletterCampaignController extends Controller
{
    public function __construct(
        private readonly NewsletterService $newsletterService
    ) {}

    /**
     * Send newsletter to all subscribers
     */
    public function sendToAll(Request $request): JsonResponse
    {
        $request->validate([
            'include_posts' => 'boolean',
            'include_products' => 'boolean',
            'post_limit' => 'integer|min:1|max:10',
            'product_limit' => 'integer|min:1|max:10',
        ]);

        $posts = [];
        $products = [];

        if ($request->get('include_posts', true)) {
            $posts = Post::where('status', 'active')
                ->orderBy('created_at', 'desc')
                ->limit($request->get('post_limit', 3))
                ->get()
                ->toArray();
        }

        if ($request->get('include_products', true)) {
            $products = Product::where('status', 'active')
                ->where('is_featured', true)
                ->orderBy('created_at', 'desc')
                ->limit($request->get('product_limit', 5))
                ->get()
                ->toArray();
        }

        $results = $this->newsletterService->sendNewsletterToAll($posts, $products);

        return response()->json([
            'success' => true,
            'message' => 'Newsletter campaign sent successfully',
            'data' => $results,
        ]);
    }

    /**
     * Send newsletter to specific segment
     */
    public function sendToSegment(Request $request): JsonResponse
    {
        $request->validate([
            'segment' => 'required|string|in:active_users,new_subscribers,premium_users,inactive_users,product_interested,blog_readers',
            'include_posts' => 'boolean',
            'include_products' => 'boolean',
            'post_limit' => 'integer|min:1|max:10',
            'product_limit' => 'integer|min:1|max:10',
        ]);

        $segment = $request->get('segment');
        $posts = [];
        $products = [];

        if ($request->get('include_posts', true)) {
            $posts = Post::where('status', 'active')
                ->orderBy('created_at', 'desc')
                ->limit($request->get('post_limit', 3))
                ->get()
                ->toArray();
        }

        if ($request->get('include_products', true)) {
            $products = Product::where('status', 'active')
                ->where('is_featured', true)
                ->orderBy('created_at', 'desc')
                ->limit($request->get('product_limit', 5))
                ->get()
                ->toArray();
        }

        $results = $this->newsletterService->sendNewsletterToSegment($segment, $posts, $products);

        return response()->json([
            'success' => true,
            'message' => "Newsletter campaign sent to {$segment} segment successfully",
            'data' => $results,
        ]);
    }

    /**
     * Get available segments
     */
    public function segments(): JsonResponse
    {
        $segments = [
            'active_users' => [
                'name' => 'Active Users',
                'description' => 'Users who have made purchases in the last 6 months',
                'count' => $this->newsletterService->getSegmentSubscribers('active_users')->count(),
            ],
            'new_subscribers' => [
                'name' => 'New Subscribers',
                'description' => 'Users who subscribed in the last 30 days',
                'count' => $this->newsletterService->getSegmentSubscribers('new_subscribers')->count(),
            ],
            'premium_users' => [
                'name' => 'Premium Users',
                'description' => 'Users with high-value orders (≥$500)',
                'count' => $this->newsletterService->getSegmentSubscribers('premium_users')->count(),
            ],
            'inactive_users' => [
                'name' => 'Inactive Users',
                'description' => 'Users with no activity in the last 3 months',
                'count' => $this->newsletterService->getSegmentSubscribers('inactive_users')->count(),
            ],
            'product_interested' => [
                'name' => 'Product Interested',
                'description' => 'Users who have viewed products',
                'count' => $this->newsletterService->getSegmentSubscribers('product_interested')->count(),
            ],
            'blog_readers' => [
                'name' => 'Blog Readers',
                'description' => 'Users who have commented on blog posts',
                'count' => $this->newsletterService->getSegmentSubscribers('blog_readers')->count(),
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $segments,
        ]);
    }
}
