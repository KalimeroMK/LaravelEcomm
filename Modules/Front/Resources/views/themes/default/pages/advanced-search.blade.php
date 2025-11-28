@extends($themePath . '.layouts.app')

@section('title', 'Advanced Search - ' . config('app.name'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Search Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Advanced Search</h1>
        <p class="text-gray-600">Find exactly what you're looking for with our powerful search and filtering tools.</p>
    </div>

    <!-- Search Form -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <form action="{{ route('front.advanced-search') }}" method="GET" id="advancedSearchForm">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <!-- Search Query -->
                <div class="md:col-span-2">
                    <label for="query" class="block text-sm font-medium text-gray-700 mb-2">Search Products</label>
                    <div class="relative">
                        <input type="text" 
                               id="query" 
                               name="query" 
                               value="{{ $query }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Search for products, brands, or categories..."
                               autocomplete="off">
                        <div id="searchSuggestions" class="absolute z-50 w-full bg-white border border-gray-300 rounded-lg shadow-lg hidden"></div>
                    </div>
                </div>

                <!-- Sort By -->
                <div>
                    <label for="sort_by" class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                    <select id="sort_by" name="sort_by" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="relevance" {{ $filters['sort_by'] ?? '' == 'relevance' ? 'selected' : '' }}>Relevance</option>
                        <option value="price_asc" {{ $filters['sort_by'] ?? '' == 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_desc" {{ $filters['sort_by'] ?? '' == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                        <option value="newest" {{ $filters['sort_by'] ?? '' == 'newest' ? 'selected' : '' }}>Newest First</option>
                        <option value="popular" {{ $filters['sort_by'] ?? '' == 'popular' ? 'selected' : '' }}>Most Popular</option>
                    </select>
                </div>
            </div>

            <!-- Advanced Filters -->
            <div class="border-t pt-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Advanced Filters</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Price Range -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Price Range</label>
                        <div class="flex space-x-2">
                            <input type="number" 
                                   name="price_min" 
                                   value="{{ $filters['price_min'] ?? '' }}"
                                   placeholder="Min"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <span class="text-gray-500 self-center">-</span>
                            <input type="number" 
                                   name="price_max" 
                                   value="{{ $filters['price_max'] ?? '' }}"
                                   placeholder="Max"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>

                    <!-- Categories -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Categories</label>
                        <select name="categories[]" multiple class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @foreach($availableFilters['categories'] as $category)
                                <option value="{{ $category->id }}" 
                                        {{ in_array($category->id, $filters['categories'] ?? []) ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Brands -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Brands</label>
                        <select name="brand" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">All Brands</option>
                            @foreach($availableFilters['brands'] as $brand)
                                <option value="{{ $brand->name }}" {{ ($filters['brand'] ?? '') == $brand->name ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Stock Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Stock Status</label>
                        <select name="in_stock" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">All Items</option>
                            <option value="1" {{ ($filters['in_stock'] ?? '') == '1' ? 'selected' : '' }}>In Stock</option>
                            <option value="0" {{ ($filters['in_stock'] ?? '') == '0' ? 'selected' : '' }}>Out of Stock</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Search Buttons -->
            <div class="flex justify-between items-center mt-6">
                <button type="submit" 
                        class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                    <i class="fas fa-search mr-2"></i>
                    Search Products
                </button>
                
                <button type="button" 
                        onclick="clearFilters()"
                        class="text-gray-600 hover:text-gray-800 px-4 py-2 rounded-md hover:bg-gray-100 transition-colors">
                    Clear Filters
                </button>
            </div>
        </form>
    </div>

    <!-- Search Results -->
    @if($searchPerformed)
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-gray-900">
                    Search Results for "{{ $query }}"
                </h2>
                <span class="text-gray-600">{{ $totalResults }} products found</span>
            </div>

            @if($totalResults > 0)
                <!-- Results Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($products as $product)
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
            @else
                <!-- No Results -->
                <div class="text-center py-12">
                    <i class="fas fa-search text-gray-400 text-6xl mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No products found</h3>
                    <p class="text-gray-600 mb-6">Try adjusting your search terms or filters to find what you're looking for.</p>
                    
                    <!-- Suggested Searches -->
                    <div class="max-w-md mx-auto">
                        <h4 class="text-sm font-medium text-gray-700 mb-3">Try these suggestions:</h4>
                        <div class="flex flex-wrap gap-2 justify-center">
                            <button onclick="suggestedSearch('laptop')" 
                                    class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-gray-200 transition-colors">
                                Laptop
                            </button>
                            <button onclick="suggestedSearch('smartphone')" 
                                    class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-gray-200 transition-colors">
                                Smartphone
                            </button>
                            <button onclick="suggestedSearch('headphones')" 
                                    class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-gray-200 transition-colors">
                                Headphones
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @else
        <!-- Search Tips -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-blue-900 mb-3">Search Tips</h3>
            <ul class="text-blue-800 space-y-2">
                <li class="flex items-start">
                    <i class="fas fa-lightbulb text-blue-600 mt-1 mr-2"></i>
                    Use specific keywords for better results (e.g., "gaming laptop" instead of just "laptop")
                </li>
                <li class="flex items-start">
                    <i class="fas fa-filter text-blue-600 mt-1 mr-2"></i>
                    Use filters to narrow down results by price, brand, or category
                </li>
                <li class="flex items-start">
                    <i class="fas fa-star text-blue-600 mt-1 mr-2"></i>
                    Check out our <a href="{{ route('front.recommendations') }}" class="underline hover:text-blue-900">personalized recommendations</a>
                </li>
            </ul>
        </div>
    @endif
</div>

@push('scripts')
<script>
// Search suggestions autocomplete
let searchTimeout;
const queryInput = document.getElementById('query');
const suggestionsDiv = document.getElementById('searchSuggestions');

queryInput.addEventListener('input', function() {
    clearTimeout(searchTimeout);
    const query = this.value.trim();
    
    if (query.length < 2) {
        suggestionsDiv.classList.add('hidden');
        return;
    }
    
    searchTimeout = setTimeout(() => {
        fetchSuggestions(query);
    }, 300);
});

async function fetchSuggestions(query) {
    try {
        const response = await fetch(`{{ route('front.search-suggestions') }}?query=${encodeURIComponent(query)}`);
        const data = await response.json();
        
        if (data.suggestions && Object.keys(data.suggestions).some(key => data.suggestions[key].length > 0)) {
            displaySuggestions(data.suggestions);
        } else {
            suggestionsDiv.classList.add('hidden');
        }
    } catch (error) {
        console.error('Error fetching suggestions:', error);
    }
}

function displaySuggestions(suggestions) {
    let html = '<div class="p-3">';
    
    if (suggestions.popular_terms && suggestions.popular_terms.length > 0) {
        html += '<div class="mb-3"><h4 class="text-sm font-medium text-gray-700 mb-2">Popular Products</h4>';
        suggestions.popular_terms.forEach(term => {
            html += `<div class="suggestion-item px-2 py-1 hover:bg-gray-100 cursor-pointer rounded" onclick="selectSuggestion('${term}')">${term}</div>`;
        });
        html += '</div>';
    }
    
    if (suggestions.categories && suggestions.categories.length > 0) {
        html += '<div class="mb-3"><h4 class="text-sm font-medium text-gray-700 mb-2">Categories</h4>';
        suggestions.categories.forEach(category => {
            html += `<div class="suggestion-item px-2 py-1 hover:bg-gray-100 cursor-pointer rounded" onclick="selectSuggestion('${category}')">${category}</div>`;
        });
        html += '</div>';
    }
    
    if (suggestions.brands && suggestions.brands.length > 0) {
        html += '<div><h4 class="text-sm font-medium text-gray-700 mb-2">Brands</h4>';
        suggestions.brands.forEach(brand => {
            html += `<div class="suggestion-item px-2 py-1 hover:bg-gray-100 cursor-pointer rounded" onclick="selectSuggestion('${brand}')">${brand}</div>`;
        });
        html += '</div>';
    }
    
    html += '</div>';
    suggestionsDiv.innerHTML = html;
    suggestionsDiv.classList.remove('hidden');
}

function selectSuggestion(term) {
    queryInput.value = term;
    suggestionsDiv.classList.add('hidden');
    document.getElementById('advancedSearchForm').submit();
}

// Hide suggestions when clicking outside
document.addEventListener('click', function(e) {
    if (!queryInput.contains(e.target) && !suggestionsDiv.contains(e.target)) {
        suggestionsDiv.classList.add('hidden');
    }
});

// Clear filters function
function clearFilters() {
    document.getElementById('query').value = '';
    document.getElementById('sort_by').value = 'relevance';
    document.querySelector('input[name="price_min"]').value = '';
    document.querySelector('input[name="price_max"]').value = '';
    document.querySelector('select[name="categories[]"]').selectedIndex = -1;
    document.querySelector('select[name="brand"]').value = '';
    document.querySelector('select[name="in_stock"]').value = '';
}

// Suggested search function
function suggestedSearch(term) {
    document.getElementById('query').value = term;
    document.getElementById('advancedSearchForm').submit();
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
            // Show success message
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
        type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
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

.suggestion-item:hover {
    background-color: #f3f4f6;
}

#searchSuggestions {
    max-height: 300px;
    overflow-y: auto;
}
</style>
@endpush
@endsection






