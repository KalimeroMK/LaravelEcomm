<!-- Advanced Features Navigation Component -->
<div class="bg-white border-b border-gray-200">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between py-4">
            <!-- Logo/Brand -->
            <div class="flex items-center">
                <a href="{{ route('front.index') }}" class="text-xl font-bold text-gray-900">
                    {{ config('app.name') }}
                </a>
            </div>

            <!-- Advanced Features Navigation -->
            <nav class="hidden md:flex items-center space-x-8">
                <a href="{{ route('front.advanced-search') }}" 
                   class="text-gray-700 hover:text-blue-600 transition-colors flex items-center">
                    <i class="fas fa-search mr-2"></i>
                    Advanced Search
                </a>
                
                <a href="{{ route('front.recommendations') }}" 
                   class="text-gray-700 hover:text-blue-600 transition-colors flex items-center">
                    <i class="fas fa-star mr-2"></i>
                    Recommendations
                </a>
                
                @auth
                    <a href="{{ route('front.enhanced-wishlist') }}" 
                       class="text-gray-700 hover:text-blue-600 transition-colors flex items-center">
                        <i class="fas fa-heart mr-2"></i>
                        Wishlist
                        <span class="ml-1 bg-red-500 text-white text-xs rounded-full px-2 py-1" id="wishlistCount">
                            {{ auth()->user()->wishlists()->count() }}
                        </span>
                    </a>
                @endauth
                
                <a href="{{ route('front.product-grids') }}" 
                   class="text-gray-700 hover:text-blue-600 transition-colors flex items-center">
                    <i class="fas fa-th mr-2"></i>
                    All Products
                </a>
            </nav>

            <!-- Mobile Menu Button -->
            <div class="md:hidden">
                <button onclick="toggleMobileMenu()" 
                        class="text-gray-700 hover:text-blue-600 transition-colors">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobileMenu" class="md:hidden hidden pb-4">
            <nav class="flex flex-col space-y-4">
                <a href="{{ route('front.advanced-search') }}" 
                   class="text-gray-700 hover:text-blue-600 transition-colors flex items-center py-2">
                    <i class="fas fa-search mr-3 w-5"></i>
                    Advanced Search
                </a>
                
                <a href="{{ route('front.recommendations') }}" 
                   class="text-gray-700 hover:text-blue-600 transition-colors flex items-center py-2">
                    <i class="fas fa-star mr-3 w-5"></i>
                    Recommendations
                </a>
                
                @auth
                    <a href="{{ route('front.enhanced-wishlist') }}" 
                       class="text-gray-700 hover:text-blue-600 transition-colors flex items-center py-2">
                        <i class="fas fa-heart mr-3 w-5"></i>
                        Wishlist
                        <span class="ml-auto bg-red-500 text-white text-xs rounded-full px-2 py-1">
                            {{ auth()->user()->wishlists()->count() }}
                        </span>
                    </a>
                @endauth
                
                <a href="{{ route('front.product-grids') }}" 
                   class="text-gray-700 hover:text-blue-600 transition-colors flex items-center py-2">
                    <i class="fas fa-th mr-3 w-5"></i>
                    All Products
                </a>
            </nav>
        </div>
    </div>
</div>

<script>
function toggleMobileMenu() {
    const mobileMenu = document.getElementById('mobileMenu');
    mobileMenu.classList.toggle('hidden');
}

// Update wishlist count in real-time
document.addEventListener('DOMContentLoaded', function() {
    // Listen for wishlist updates
    window.addEventListener('wishlistUpdated', function() {
        updateWishlistCount();
    });
});

async function updateWishlistCount() {
    try {
        const response = await fetch('{{ route("api.wishlist.count") }}');
        if (response.ok) {
            const data = await response.json();
            const countElement = document.getElementById('wishlistCount');
            if (countElement) {
                countElement.textContent = data.data.count;
            }
        }
    } catch (error) {
        console.error('Error updating wishlist count:', error);
    }
}

// Update count when adding/removing from wishlist
function updateWishlistCountFromAction(action) {
    const countElement = document.getElementById('wishlistCount');
    if (countElement) {
        let currentCount = parseInt(countElement.textContent) || 0;
        
        if (action === 'add') {
            currentCount++;
        } else if (action === 'remove') {
            currentCount = Math.max(0, currentCount - 1);
        }
        
        countElement.textContent = currentCount;
    }
}
</script>

