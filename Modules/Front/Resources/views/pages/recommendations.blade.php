@extends('front::layouts.app')

@section('title', 'Product Recommendations - ' . config('app.name'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Product Recommendations</h1>
        <p class="text-gray-600">Discover products tailored just for you based on your preferences and behavior.</p>
    </div>

    <!-- Recommendation Type Selector -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <div class="flex flex-wrap gap-4 items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900 mb-2">Recommendation Types</h2>
                <p class="text-gray-600">Choose how you'd like to discover new products</p>
            </div>
            
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('front.recommendations', ['type' => 'ai']) }}" 
                   class="px-4 py-2 rounded-lg {{ $type == 'ai' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} transition-colors">
                    <i class="fas fa-brain mr-2"></i>
                    AI-Powered
                </a>
                
                <a href="{{ route('front.recommendations', ['type' => 'collaborative']) }}" 
                   class="px-4 py-2 rounded-lg {{ $type == 'collaborative' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} transition-colors">
                    <i class="fas fa-users mr-2"></i>
                    Similar Users
                </a>
                
                <a href="{{ route('front.recommendations', ['type' => 'trending']) }}" 
                   class="px-4 py-2 rounded-lg {{ $type == 'trending' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} transition-colors">
                    <i class="fas fa-fire mr-2"></i>
                    Trending
                </a>
            </div>
        </div>
    </div>

    <!-- Current Recommendation Type Info -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-8">
        <div class="flex items-center">
            <i class="fas fa-info-circle text-blue-600 mr-3 text-xl"></i>
            <div>
                <h3 class="font-semibold text-blue-900">{{ $recommendationType }} Recommendations</h3>
                <p class="text-blue-800 text-sm">
                    @if($type == 'ai')
                        These recommendations are powered by artificial intelligence, analyzing your browsing history, purchases, and preferences to suggest products you'll love.
                    @elseif($type == 'collaborative')
                        Based on what users with similar tastes have liked and purchased, helping you discover products that match your style.
                    @else
                        Currently popular products based on recent views, purchases, and user engagement across our platform.
                    @endif
                </p>
            </div>
        </div>
    </div>

    <!-- Recommendations Grid -->
    @if($totalCount > 0)
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-gray-900">
                    {{ $totalCount }} Recommended Products
                </h2>
                
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-600">View:</span>
                    <button onclick="setViewMode('grid')" 
                            id="gridViewBtn"
                            class="p-2 rounded-md bg-blue-100 text-blue-600 hover:bg-blue-200 transition-colors">
                        <i class="fas fa-th"></i>
                    </button>
                    <button onclick="setViewMode('list')" 
                            id="listViewBtn"
                            class="p-2 rounded-md bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors">
                        <i class="fas fa-list"></i>
                    </button>
                </div>
            </div>

            <!-- Grid View -->
            <div id="gridView" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($recommendations as $product)
                    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                        <div class="relative">
                            @if($product->getFirstMediaUrl('images'))
                                <img src="{{ $product->getFirstMediaUrl('images') }}" 
                                     alt="{{ $product->title }}"
                                     class="w-full h-48 object-cover">
                            @else
                                <div class="w-full h-48 bg-gray-300 flex items-center justify-center">
                                    <i class="fas fa-image text-gray-400 text-4xl"></i>
                                </div>
                            @endif
                            
                            <!-- Recommendation Badge -->
                            <div class="absolute top-2 right-2">
                                <span class="bg-blue-600 text-white text-xs px-2 py-1 rounded-full">
                                    <i class="fas fa-star mr-1"></i>
                                    Recommended
                                </span>
                            </div>
                        </div>
                        
                        <div class="p-4">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2">
                                <a href="{{ route('front.product-detail', $product->slug) }}" 
                                   class="hover:text-blue-600 transition-colors">
                                    {{ $product->title }}
                                </a>
                            </h3>
                            
                            <div class="flex items-center mb-2">
                                @if($product->special_price)
                                    <span class="text-lg font-bold text-red-600">${{ number_format($product->special_price, 2) }}</span>
                                    <span class="text-sm text-gray-500 line-through ml-2">${{ number_format($product->price, 2) }}</span>
                                @else
                                    <span class="text-lg font-bold text-gray-900">${{ number_format($product->price, 2) }}</span>
                                @endif
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">
                                    Stock: {{ $product->stock > 0 ? 'In Stock' : 'Out of Stock' }}
                                </span>
                                
                                <div class="flex space-x-2">
                                    <button onclick="addToWishlist({{ $product->id }})" 
                                            class="text-gray-400 hover:text-red-500 transition-colors">
                                        <i class="far fa-heart"></i>
                                    </button>
                                    
                                    <button onclick="addToCart({{ $product->id }})" 
                                            class="text-gray-400 hover:text-green-500 transition-colors">
                                        <i class="fas fa-shopping-cart"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- List View (Hidden by default) -->
            <div id="listView" class="hidden space-y-4">
                @foreach($recommendations as $product)
                    <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0">
                                @if($product->getFirstMediaUrl('images'))
                                    <img src="{{ $product->getFirstMediaUrl('images') }}" 
                                         alt="{{ $product->title }}"
                                         class="w-24 h-24 object-cover rounded-lg">
                                @else
                                    <div class="w-24 h-24 bg-gray-300 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-image text-gray-400 text-2xl"></i>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="flex-1 min-w-0">
                                <h3 class="text-lg font-semibold text-gray-900 mb-1">
                                    <a href="{{ route('front.product-detail', $product->slug) }}" 
                                       class="hover:text-blue-600 transition-colors">
                                        {{ $product->title }}
                                    </a>
                                </h3>
                                
                                <p class="text-gray-600 text-sm mb-2 line-clamp-2">
                                    {{ $product->summary ?? 'No description available.' }}
                                </p>
                                
                                <div class="flex items-center space-x-4">
                                    <div class="flex items-center">
                                        @if($product->special_price)
                                            <span class="text-lg font-bold text-red-600">${{ number_format($product->special_price, 2) }}</span>
                                            <span class="text-sm text-gray-500 line-through ml-2">${{ number_format($product->price, 2) }}</span>
                                        @else
                                            <span class="text-lg font-bold text-gray-900">${{ number_format($product->price, 2) }}</span>
                                        @endif
                                    </div>
                                    
                                    <span class="text-sm text-gray-600">
                                        Stock: {{ $product->stock > 0 ? 'In Stock' : 'Out of Stock' }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="flex flex-col space-y-2">
                                <button onclick="addToWishlist({{ $product->id }})" 
                                        class="text-gray-400 hover:text-red-500 transition-colors p-2">
                                    <i class="far fa-heart"></i>
                                </button>
                                
                                <button onclick="addToCart({{ $product->id }})" 
                                        class="text-gray-400 hover:text-green-500 transition-colors p-2">
                                    <i class="fas fa-shopping-cart"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <!-- No Recommendations -->
        <div class="bg-white rounded-lg shadow-md p-12 text-center">
            <i class="fas fa-lightbulb text-gray-400 text-6xl mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">No recommendations available</h3>
            <p class="text-gray-600 mb-6">
                @if($type == 'ai')
                    We need more information about your preferences to provide AI-powered recommendations. Try browsing some products or making a purchase.
                @elseif($type == 'collaborative')
                    We're still learning about user preferences. Check back later for personalized recommendations.
                @else
                    No trending products available at the moment. Check back later for the latest popular items.
                @endif
            </p>
            
            <div class="flex justify-center space-x-4">
                <a href="{{ route('front.product-grids') }}" 
                   class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                    Browse All Products
                </a>
                
                <a href="{{ route('front.advanced-search') }}" 
                   class="bg-gray-100 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-200 transition-colors">
                    Advanced Search
                </a>
            </div>
        </div>
    @endif

    <!-- Additional Features -->
    <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Personalized Shopping -->
        <div class="bg-white rounded-lg shadow-md p-6 text-center">
            <i class="fas fa-user-cog text-blue-600 text-4xl mb-4"></i>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Personalized Shopping</h3>
            <p class="text-gray-600 mb-4">Get recommendations based on your unique preferences and shopping history.</p>
            <a href="{{ route('front.advanced-search') }}" 
               class="text-blue-600 hover:text-blue-800 font-medium">
                Start Shopping →
            </a>
        </div>

        <!-- Discover New Brands -->
        <div class="bg-white rounded-lg shadow-md p-6 text-center">
            <i class="fas fa-compass text-green-600 text-4xl mb-4"></i>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Discover New Brands</h3>
            <p class="text-gray-600 mb-4">Explore products from brands you might not have discovered yet.</p>
            <a href="{{ route('front.product-brand', 'all') }}" 
               class="text-green-600 hover:text-green-800 font-medium">
                Explore Brands →
            </a>
        </div>

        <!-- Stay Updated -->
        <div class="bg-white rounded-lg shadow-md p-6 text-center">
            <i class="fas fa-bell text-purple-600 text-4xl mb-4"></i>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Stay Updated</h3>
            <p class="text-gray-600 mb-4">Get notified about new products and recommendations tailored for you.</p>
            <button onclick="subscribeToUpdates()" 
                    class="text-purple-600 hover:text-purple-800 font-medium">
                Subscribe →
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
// View mode switching
function setViewMode(mode) {
    const gridView = document.getElementById('gridView');
    const listView = document.getElementById('listView');
    const gridBtn = document.getElementById('gridViewBtn');
    const listBtn = document.getElementById('listViewBtn');
    
    if (mode === 'grid') {
        gridView.classList.remove('hidden');
        listView.classList.add('hidden');
        gridBtn.className = 'p-2 rounded-md bg-blue-100 text-blue-600 hover:bg-blue-200 transition-colors';
        listBtn.className = 'p-2 rounded-md bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors';
    } else {
        gridView.classList.add('hidden');
        listView.classList.remove('hidden');
        gridBtn.className = 'p-2 rounded-md bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors';
        listBtn.className = 'p-2 rounded-md bg-blue-100 text-blue-600 hover:bg-blue-200 transition-colors';
    }
}

// Add to wishlist function
async function addToWishlist(productId) {
    try {
        const response = await fetch('{{ route("api.wishlist.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                product_id: productId,
                quantity: 1
            })
        });
        
        if (response.ok) {
            showNotification('Product added to wishlist!', 'success');
        } else {
            showNotification('Failed to add product to wishlist.', 'error');
        }
    } catch (error) {
        console.error('Error adding to wishlist:', error);
        showNotification('Error adding to wishlist.', 'error');
    }
}

// Add to cart function
async function addToCart(productId) {
    try {
        const response = await fetch('{{ route("api.carts.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                product_id: productId,
                quantity: 1
            })
        });
        
        if (response.ok) {
            showNotification('Product added to cart!', 'success');
        } else {
            showNotification('Failed to add product to cart.', 'error');
        }
    } catch (error) {
        console.error('Error adding to cart:', error);
        showNotification('Error adding to cart.', 'error');
    }
}

// Subscribe to updates function
function subscribeToUpdates() {
    showNotification('Subscription feature coming soon!', 'info');
}

// Notification function
function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
        type === 'success' ? 'bg-green-500 text-white' : 
        type === 'error' ? 'bg-red-500 text-white' : 
        'bg-blue-500 text-white'
    }`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}
</script>
@endpush

@push('styles')
<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Smooth transitions for view switching */
#gridView, #listView {
    transition: opacity 0.3s ease-in-out;
}

/* Hover effects */
.hover\:shadow-lg:hover {
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

.hover\:shadow-md:hover {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}
</style>
@endpush
@endsection

