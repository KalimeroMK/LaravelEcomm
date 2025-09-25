<?php

declare(strict_types=1);

namespace Modules\Product\Services;

use Exception;
use Illuminate\Support\Collection;
use Modules\Product\Models\Product;
use Modules\ProductStats\Models\ProductClick;
use Modules\ProductStats\Models\ProductImpression;
use Modules\User\Models\User;
use OpenAI\Laravel\Facades\OpenAI;

class RecommendationService
{
    /**
     * Get AI-powered product recommendations for a user
     */
    public function getAIRecommendations(User $user, int $limit = 10): Collection
    {
        // Get user's browsing and purchase history
        $userBehavior = $this->analyzeUserBehavior($user);

        // Generate AI recommendations
        $recommendations = $this->generateAIRecommendations($userBehavior);

        // Get recommended products
        return $this->getRecommendedProducts($recommendations, $limit);
    }

    /**
     * Get collaborative filtering recommendations
     */
    public function getCollaborativeRecommendations(User $user, int $limit = 10): Collection
    {
        // Find users with similar behavior
        $similarUsers = $this->findSimilarUsers($user);

        // Get products liked by similar users
        $recommendedProductIds = $this->getProductsFromSimilarUsers($similarUsers, $user);

        return Product::with(['brand', 'categories', 'tags', 'attributeValues.attribute'])
            ->whereIn('id', $recommendedProductIds)
            ->where('status', 'active')
            ->where('stock', '>', 0)
            ->limit($limit)
            ->get();
    }

    /**
     * Get content-based recommendations
     */
    public function getContentBasedRecommendations(Product $product, int $limit = 10): Collection
    {
        $query = Product::with(['brand', 'categories', 'tags', 'attributeValues.attribute'])
            ->where('id', '!=', $product->id)
            ->where('status', 'active')
            ->where('stock', '>', 0);

        // Try to find similar products
        if ($product->brand_id || $product->categories->isNotEmpty() || $product->tags->isNotEmpty()) {
            $query->where(function ($q) use ($product): void {
                if ($product->brand_id) {
                    $q->where('brand_id', $product->brand_id);
                }

                if ($product->categories->isNotEmpty()) {
                    $q->orWhereHas('categories', function ($categoryQuery) use ($product): void {
                        $categoryQuery->whereIn('categories.id', $product->categories->pluck('id'));
                    });
                }

                if ($product->tags->isNotEmpty()) {
                    $q->orWhereHas('tags', function ($tagQuery) use ($product): void {
                        $tagQuery->whereIn('tags.id', $product->tags->pluck('id'));
                    });
                }
            });
        }

        $results = $query->limit($limit)->get();

        // If no related products found, return random products
        if ($results->isEmpty()) {
            $results = Product::with(['brand', 'categories', 'tags', 'attributeValues.attribute'])
                ->where('id', '!=', $product->id)
                ->where('status', 'active')
                ->where('stock', '>', 0)
                ->inRandomOrder()
                ->limit($limit)
                ->get();
        }

        return $results;
    }

    /**
     * Get trending products
     */
    public function getTrendingProducts(int $limit = 10): Collection
    {
        $trendingProductIds = ProductClick::selectRaw('product_id, COUNT(*) as click_count')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('product_id')
            ->orderByDesc('click_count')
            ->limit($limit)
            ->pluck('product_id');

        return Product::with(['brand', 'categories', 'tags', 'attributeValues.attribute'])
            ->whereIn('id', $trendingProductIds)
            ->where('status', 'active')
            ->where('stock', '>', 0)
            ->get();
    }

    /**
     * Analyze user behavior for AI recommendations
     */
    protected function analyzeUserBehavior(User $user): array
    {
        $clicks = ProductClick::where('user_id', $user->id)
            ->with('product')
            ->get();

        $impressions = ProductImpression::where('user_id', $user->id)
            ->with('product')
            ->get();

        $cartItems = $user->carts()->with('product')->get();
        $wishlistItems = $user->wishlists()->with('product')->get();

        // Get categories from products (many-to-many relationship)
        $viewedCategories = $clicks->flatMap(function ($click) {
            return $click->product ? $click->product->categories->pluck('id') : [];
        })->countBy()->toArray();

        return [
            'viewed_categories' => $viewedCategories,
            'viewed_brands' => $clicks->pluck('product.brand_id')->filter()->countBy()->toArray(),
            'price_range' => [
                'min' => $clicks->pluck('product.price')->filter()->min() ?: 0,
                'max' => $clicks->pluck('product.price')->filter()->max() ?: 100,
                'avg' => $clicks->pluck('product.price')->filter()->avg() ?: 50,
            ],
            'interaction_patterns' => [
                'clicks' => $clicks->count(),
                'impressions' => $impressions->count(),
                'cart_adds' => $cartItems->count(),
                'wishlist_adds' => $wishlistItems->count(),
            ],
        ];
    }

    /**
     * Generate AI recommendations using OpenAI
     */
    protected function generateAIRecommendations(array $userBehavior): array
    {
        try {
            $prompt = $this->buildRecommendationPrompt($userBehavior);

            $response = OpenAI::chat()->create([
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are an e-commerce product recommendation expert. Analyze user behavior and suggest product categories and attributes.',
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt,
                    ],
                ],
                'max_tokens' => 500,
                'temperature' => 0.7,
            ]);

            $content = $response->choices[0]->message->content;

            // Parse AI response and extract recommendations
            return $this->parseAIRecommendations($content);
        } catch (Exception $e) {
            // Fallback to rule-based recommendations
            return $this->getFallbackRecommendations($userBehavior);
        }
    }

    /**
     * Build prompt for AI recommendations
     */
    protected function buildRecommendationPrompt(array $userBehavior): string
    {
        $categories = implode(', ', array_keys($userBehavior['viewed_categories']));
        $brands = implode(', ', array_keys($userBehavior['viewed_brands']));
        $priceRange = $userBehavior['price_range'];

        return "Based on this user behavior:
        - Viewed categories: {$categories}
        - Viewed brands: {$brands}
        - Price range: \${$priceRange['min']} - \${$priceRange['max']} (avg: \${$priceRange['avg']})
        - Interaction patterns: {$userBehavior['interaction_patterns']['clicks']} clicks, {$userBehavior['interaction_patterns']['cart_adds']} cart additions
        
        Suggest 5 product categories and 3 price ranges that would interest this user. Format as JSON with 'categories' and 'price_ranges' arrays.";
    }

    /**
     * Parse AI recommendations from response
     */
    protected function parseAIRecommendations(string $content): array
    {
        try {
            // Try to extract JSON from the response
            if (preg_match('/\{.*\}/s', $content, $matches)) {
                $json = json_decode($matches[0], true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    return $json;
                }
            }
        } catch (Exception $e) {
            // Continue to fallback
        }

        return $this->getFallbackRecommendations([]);
    }

    /**
     * Get fallback recommendations when AI fails
     */
    protected function getFallbackRecommendations(array $userBehavior): array
    {
        return [
            'categories' => array_keys($userBehavior['viewed_categories'] ?? []),
            'price_ranges' => [
                ['min' => 0, 'max' => 50],
                ['min' => 50, 'max' => 200],
                ['min' => 200, 'max' => 1000],
            ],
        ];
    }

    /**
     * Get recommended products based on AI suggestions
     */
    protected function getRecommendedProducts(array $recommendations, int $limit): Collection
    {
        $query = Product::with(['brand', 'categories', 'tags', 'attributeValues.attribute'])
            ->where('status', 'active')
            ->where('stock', '>', 0);

        if (! empty($recommendations['categories'])) {
            $query->whereHas('categories', function ($q) use ($recommendations): void {
                $q->whereIn('id', $recommendations['categories']);
            });
        }

        if (! empty($recommendations['price_ranges'])) {
            $priceConditions = [];
            foreach ($recommendations['price_ranges'] as $range) {
                $priceConditions[] = function ($q) use ($range): void {
                    $q->whereBetween('price', [$range['min'], $range['max']]);
                };
            }
            $query->where(function ($q) use ($priceConditions): void {
                foreach ($priceConditions as $condition) {
                    $q->orWhere($condition);
                }
            });
        }

        return $query->limit($limit)->get();
    }

    /**
     * Find users with similar behavior
     */
    protected function findSimilarUsers(User $user): Collection
    {
        // Get categories from user's cart products (many-to-many relationship)
        $userCategories = $user->carts()->with('product.categories')->get()
            ->flatMap(function ($cart) {
                return $cart->product ? $cart->product->categories->pluck('id') : [];
            })->unique();

        if ($userCategories->isEmpty()) {
            return collect();
        }

        return User::whereHas('carts.product.categories', function ($query) use ($userCategories): void {
            $query->whereIn('categories.id', $userCategories);
        })->where('id', '!=', $user->id)
            ->limit(10)
            ->get();
    }

    /**
     * Get products from similar users
     */
    protected function getProductsFromSimilarUsers(Collection $similarUsers, User $currentUser): array
    {
        $excludeProductIds = $currentUser->carts()->pluck('product_id')->toArray();

        return $similarUsers->flatMap(function ($user) use ($excludeProductIds) {
            return $user->carts()
                ->whereNotIn('product_id', $excludeProductIds)
                ->pluck('product_id');
        })->unique()->take(20)->toArray();
    }
}
