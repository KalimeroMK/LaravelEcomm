@extends('front::layouts.app')

@section('title', 'My Wishlist - ' . config('app.name'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">My Wishlist</h1>
        <p class="text-gray-600">Save products you love and get notified about price drops and special offers.</p>
    </div>

    <!-- Wishlist Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6 text-center">
            <i class="fas fa-heart text-red-500 text-3xl mb-3"></i>
            <h3 class="text-lg font-semibold text-gray-900">{{ $statistics['total_items'] }}</h3>
            <p class="text-gray-600 text-sm">Total Items</p>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 text-center">
            <i class="fas fa-dollar-sign text-green-500 text-3xl mb-3"></i>
            <h3 class="text-lg font-semibold text-gray-900">${{ number_format($statistics['total_value'], 2) }}</h3>
            <p class="text-gray-600 text-sm">Total Value</p>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 text-center">
            <i class="fas fa-tags text-blue-500 text-3xl mb-3"></i>
            <h3 class="text-lg font-semibold text-gray-900">{{ $statistics['categories'] }}</h3>
            <p class="text-gray-600 text-sm">Categories</p>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 text-center">
            <i class="fas fa-crown text-purple-500 text-3xl mb-3"></i>
            <h3 class="text-lg font-semibold text-gray-900">{{ $statistics['brands'] }}</h3>
            <p class="text-gray-600 text-sm">Brands</p>
        </div>
    </div>

    <!-- Price Alerts Toggle -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900 mb-2">Price Alerts</h2>
                <p class="text-gray-600">Track price changes and get notified about the best deals</p>
            </div>
            
            <div class="flex items-center space-x-4">
                <a href="{{ route('front.enhanced-wishlist', ['with_price_alerts' => true]) }}" 
                   class="px-4 py-2 rounded-lg {{ $withPriceAlerts ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} transition-colors">
                    <i class="fas fa-bell mr-2"></i>
                    With Price Alerts
                </a>
                
                <a href="{{ route('front.enhanced-wishlist') }}" 
                   class="px-4 py-2 rounded-lg {{ !$withPriceAlerts ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} transition-colors">
                    <i class="fas fa-list mr-2"></i>
                    Standard View
                </a>
            </div>
        </div>
    </div>

    <!-- Wishlist Items -->
    @if($wishlist->count() > 0)
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-gray-900">Wishlist Items</h2>
                
                <div class="flex space-x-2">
                    <button onclick="bulkAddToCart()" 
                            class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-shopping-cart mr-2"></i>
                        Add All to Cart
                    </button>
                    
                    <button onclick="bulkRemove()" 
                            class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                        <i class="fas fa-trash mr-2"></i>
                        Remove All
                    </button>
                </div>
            </div>

            <!-- Wishlist Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($wishlist as $item)
                    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                        <div class="relative">
                            @if($item->product->getFirstMediaUrl('images'))
                                <img src="{{ $item->product->getFirstMediaUrl('images') }}" 
                                     alt="{{ $item->product->title }}"
                                     class="w-full h-48 object-cover">
                            @else
                                <div class="w-full h-48 bg-gray-300 flex items-center justify-center">
                                    <i class="fas fa-image text-gray-400 text-4xl"></i>
                                </div>
                            @endif
                            
                            <!-- Price Alert Badge -->
                            @if($withPriceAlerts && $item->price_drop)
                                <div class="absolute top-2 left-2">
                                    <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full">
                                        <i class="fas fa-arrow-down mr-1"></i>
                                        {{ $item->price_drop_percentage }}% OFF
                                    </span>
                                </div>
                            @endif
                            
                            <!-- Quantity Badge -->
                            @if($item->quantity > 1)
                                <div class="absolute top-2 right-2">
                                    <span class="bg-blue-500 text-white text-xs px-2 py-1 rounded-full">
                                        Qty: {{ $item->quantity }}
                                    </span>
                                </div>
                            @endif
                        </div>
                        
                        <div class="p-4">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2">
                                <a href="{{ route('front.product-detail', $item->product->slug) }}" 
                                   class="hover:text-blue-600 transition-colors">
                                    {{ $item->product->title }}
                                </a>
                            </h3>
                            
                            <!-- Price Information -->
                            <div class="mb-3">
                                @if($withPriceAlerts && $item->price_drop)
                                    <div class="flex items-center space-x-2 mb-2">
                                        <span class="text-lg font-bold text-red-600">
                                            ${{ number_format($item->product->special_price ?? $item->product->price, 2) }}
                                        </span>
                                        <span class="text-sm text-gray-500 line-through">
                                            ${{ number_format($item->price, 2) }}
                                        </span>
                                        <span class="text-sm text-red-600 font-medium">
                                            Save ${{ number_format($item->price_difference, 2) }}
                                        </span>
                                    </div>
                                @else
                                    <div class="flex items-center mb-2">
                                        @if($item->product->special_price)
                                            <span class="text-lg font-bold text-red-600">${{ number_format($item->product->special_price, 2) }}</span>
                                            <span class="text-sm text-gray-500 line-through ml-2">${{ number_format($item->product->price, 2) }}</span>
                                        @else
                                            <span class="text-lg font-bold text-gray-900">${{ number_format($item->product->price, 2) }}</span>
                                        @endif
                                    </div>
                                @endif
                                
                                <div class="text-sm text-gray-600">
                                    Stock: {{ $item->product->stock > 0 ? 'In Stock' : 'Out of Stock' }}
                                </div>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="flex items-center justify-between">
                                <div class="flex space-x-2">
                                    <button onclick="moveToCart({{ $item->product->id }})" 
                                            class="bg-blue-600 text-white px-3 py-2 rounded-md hover:bg-blue-700 transition-colors text-sm">
                                        <i class="fas fa-shopping-cart mr-1"></i>
                                        Move to Cart
                                    </button>
                                    
                                    <button onclick="updateQuantity({{ $item->product->id }})" 
                                            class="bg-gray-100 text-gray-700 px-3 py-2 rounded-md hover:bg-gray-200 transition-colors text-sm">
                                        <i class="fas fa-edit mr-1"></i>
                                        Edit
                                    </button>
                                </div>
                                
                                <button onclick="removeFromWishlist({{ $item->product->id }})" 
                                        class="text-red-500 hover:text-red-700 transition-colors">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <!-- Empty Wishlist -->
        <div class="bg-white rounded-lg shadow-md p-12 text-center">
            <i class="far fa-heart text-gray-400 text-6xl mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Your wishlist is empty</h3>
            <p class="text-gray-600 mb-6">Start adding products you love to your wishlist to track them and get notified about price drops.</p>
            
            <div class="flex justify-center space-x-4">
                <a href="{{ route('front.product-grids') }}" 
                   class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                    Browse Products
                </a>
                
                <a href="{{ route('front.recommendations') }}" 
                   class="bg-gray-100 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-200 transition-colors">
                    Get Recommendations
                </a>
            </div>
        </div>
    @endif

    <!-- Wishlist Recommendations -->
    @if($recommendations->count() > 0)
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">You Might Also Like</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($recommendations as $product)
                    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                        <div class="aspect-w-1 aspect-h-1 bg-gray-200">
                            @if($product->getFirstMediaUrl('images'))
                                <img src="{{ $product->getFirstMediaUrl('images') }}" 
                                     alt="{{ $product->title }}"
                                     class="w-full h-48 object-cover">
                            @else
                                <div class="w-full h-48 bg-gray-300 flex items-center justify-center">
                                    <i class="fas fa-image text-gray-400 text-4xl"></i>
                                </div>
                            @endif
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
                                
                                <button onclick="addToWishlist({{ $product->id }})" 
                                        class="text-gray-400 hover:text-red-500 transition-colors">
                                    <i class="far fa-heart"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Wishlist Actions -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Wishlist Actions</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Share Wishlist -->
            <div class="text-center">
                <i class="fas fa-share-alt text-blue-600 text-3xl mb-3"></i>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Share Wishlist</h3>
                <p class="text-gray-600 mb-4">Share your wishlist with friends and family</p>
                <button onclick="shareWishlist()" 
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    Share Now
                </button>
            </div>
            
            <!-- Export Wishlist -->
            <div class="text-center">
                <i class="fas fa-download text-green-600 text-3xl mb-3"></i>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Export Wishlist</h3>
                <p class="text-gray-600 mb-4">Download your wishlist as a PDF or CSV</p>
                <button onclick="exportWishlist()" 
                        class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                    Export
                </button>
            </div>
            
            <!-- Price Alerts -->
            <div class="text-center">
                <i class="fas fa-bell text-purple-600 text-3xl mb-3"></i>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Price Alerts</h3>
                <p class="text-gray-600 mb-4">Get notified when prices drop on your wishlist items</p>
                <button onclick="managePriceAlerts()" 
                        class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                    Manage Alerts
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Bulk operations
function bulkAddToCart() {
    if (confirm('Add all wishlist items to cart?')) {
        const productIds = Array.from(document.querySelectorAll('[onclick*="moveToCart"]'))
            .map(btn => btn.getAttribute('onclick').match(/\d+/)[0]);
        
        // Implementation for bulk add to cart
        showNotification('Bulk add to cart feature coming soon!', 'info');
    }
}

function bulkRemove() {
    if (confirm('Remove all items from wishlist? This action cannot be undone.')) {
        const productIds = Array.from(document.querySelectorAll('[onclick*="removeFromWishlist"]'))
            .map(btn => btn.getAttribute('onclick').match(/\d+/)[0]);
        
        // Implementation for bulk remove
        showNotification('Bulk remove feature coming soon!', 'info');
    }
}

// Individual item operations
async function moveToCart(productId) {
    try {
        const response = await fetch(`{{ route('api.wishlist.move-to-cart', ['id' => ':id']) }}`.replace(':id', productId), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        });
        
        if (response.ok) {
            showNotification('Product moved to cart successfully!', 'success');
            // Reload page to update wishlist
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification('Failed to move product to cart.', 'error');
        }
    } catch (error) {
        console.error('Error moving to cart:', error);
        showNotification('Error moving to cart.', 'error');
    }
}

async function removeFromWishlist(productId) {
    if (confirm('Remove this item from your wishlist?')) {
        try {
            const response = await fetch(`{{ route('api.wishlist.destroy', ['id' => ':id']) }}`.replace(':id', productId), {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });
            
            if (response.ok) {
                showNotification('Product removed from wishlist!', 'success');
                // Reload page to update wishlist
                setTimeout(() => location.reload(), 1000);
            } else {
                showNotification('Failed to remove product from wishlist.', 'error');
            }
        } catch (error) {
            console.error('Error removing from wishlist:', error);
            showNotification('Error removing from wishlist.', 'error');
        }
    }
}

function updateQuantity(productId) {
    const newQuantity = prompt('Enter new quantity:');
    if (newQuantity && !isNaN(newQuantity) && newQuantity > 0) {
        // Implementation for updating quantity
        showNotification('Quantity update feature coming soon!', 'info');
    }
}

// Wishlist actions
function shareWishlist() {
    // Implementation for sharing wishlist
    showNotification('Share feature coming soon!', 'info');
}

function exportWishlist() {
    // Implementation for exporting wishlist
    showNotification('Export feature coming soon!', 'info');
}

function managePriceAlerts() {
    // Implementation for managing price alerts
    showNotification('Price alerts management coming soon!', 'info');
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

/* Hover effects */
.hover\:shadow-lg:hover {
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

/* Price drop animation */
@keyframes priceDrop {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

.bg-red-500 {
    animation: priceDrop 2s ease-in-out infinite;
}
</style>
@endpush
@endsection



