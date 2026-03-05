# План за Подобрување на Attributes Модулот

## Цел: Достигнување на Bagisto ниво на функционалност

---

## Фаза 1: Configurable Products (2-3 недели)

### 1.1 Датабаза Измени
```php
// Миграција: додавање на product типови
Schema::table('products', function (Blueprint $table) {
    $table->enum('type', ['simple', 'configurable', 'variant'])->default('simple');
    $table->foreignId('parent_id')->nullable()->constrained('products')->onDelete('cascade');
    $table->json('configurable_attributes')->nullable(); // ["color", "size"]
});
```

### 1.2 Модел Измени
```php
class Product extends Core {
    
    // Relationships
    public function parent(): BelongsTo {
        return $this->belongsTo(Product::class, 'parent_id');
    }
    
    public function variants(): HasMany {
        return $this->hasMany(Product::class, 'parent_id')->where('type', 'variant');
    }
    
    public function isConfigurable(): bool {
        return $this->type === 'configurable';
    }
    
    public function isVariant(): bool {
        return $this->type === 'variant';
    }
}
```

### 1.3 Service за Configurable Products
```php
class ConfigurableProductService {
    
    /**
     * Генерира варијации од комбинации на атрибути
     */
    public function generateVariants(Product $product, array $attributeCodes): Collection {
        // Пример: Color [Red, Blue] × Size [S, M] = 4 варијации
        $attributes = Attribute::whereIn('code', $attributeCodes)->with('options')->get();
        $combinations = $this->generateCombinations($attributes);
        
        $variants = collect();
        foreach ($combinations as $combination) {
            $variant = $this->createVariant($product, $combination);
            $variants->push($variant);
        }
        
        return $variants;
    }
    
    /**
     * Креира варијација со специфични атрибути
     */
    private function createVariant(Product $parent, array $attributeValues): Product {
        $variant = Product::create([
            'parent_id' => $parent->id,
            'type' => 'variant',
            'title' => $parent->title . ' ' . $this->formatVariantName($attributeValues),
            'sku' => $parent->sku . '-' . $this->generateVariantSkuSuffix($attributeValues),
            'price' => $parent->price, // или различна цена
            'stock' => 0,
        ]);
        
        // Додади ги атрибут вредностите
        foreach ($attributeValues as $attributeCode => $value) {
            $attribute = Attribute::where('code', $attributeCode)->first();
            $variant->attributeValues()->create([
                'attribute_id' => $attribute->id,
                'text_value' => $value,
            ]);
        }
        
        return $variant;
    }
}
```

### 1.4 Админ UI за Configurable Products
```php
// На product create/edit страна:
- Checkbox: "This is a configurable product"
- Ако е чекирано:
  - Multi-select: "Select configurable attributes" (само is_configurable=true)
  - Button: "Generate Variants"
  - Табела со варијации (editable price, stock, sku)
```

---

## Фаза 2: Layered Navigation (1-2 недели)

### 2.1 Query Scope за Филтрирање
```php
class Product extends Core {
    
    public function scopeFilterByAttributes($query, array $filters) {
        foreach ($filters as $attributeCode => $values) {
            $attribute = Attribute::where('code', $attributeCode)->first();
            
            $query->whereHas('attributeValues', function ($q) use ($attribute, $values) {
                $q->where('attribute_id', $attribute->id);
                
                $column = $attribute->getValueColumnName();
                if (is_array($values)) {
                    $q->whereIn($column, $values);
                } else {
                    $q->where($column, $values);
                }
            });
        }
        
        return $query;
    }
}
```

### 2.2 Frontend Filter Component
```php
// Во controller:
public function productGrids(Request $request) {
    $products = Product::query()
        ->when($request->has('color'), fn($q) => $q->filterByAttributes(['color' => $request->color]))
        ->when($request->has('size'), fn($q) => $q->filterByAttributes(['size' => $request->size]))
        ->paginate();
    
    // Земи ги filterable атрибутите со нивните опции
    $filterableAttributes = Attribute::where('is_filterable', true)
        ->with(['options' => fn($q) => $q->withCount('products')])
        ->get();
    
    return view('product-grids', compact('products', 'filterableAttributes'));
}
```

### 2.3 Blade Component
```blade
{{-- Sidebar Filter --}}
<div class="filter-sidebar">
    @foreach($filterableAttributes as $attribute)
        <div class="filter-group">
            <h5>{{ $attribute->name }}</h5>
            
            @if($attribute->display === 'color')
                {{-- Color Swatches --}}
                <div class="color-swatches">
                    @foreach($attribute->options as $option)
                        <a href="{{ request()->fullUrlWithQuery([$attribute->code => $option->value]) }}"
                           class="color-swatch {{ request($attribute->code) == $option->value ? 'active' : '' }}"
                           style="background-color: {{ $option->value }}">
                        </a>
                    @endforeach
                </div>
            @else
                {{-- Regular Checkboxes --}}
                @foreach($attribute->options as $option)
                    <label class="filter-option">
                        <input type="checkbox" name="{{ $attribute->code }}[]" 
                               value="{{ $option->value }}"
                               {{ in_array($option->value, request($attribute->code, [])) ? 'checked' : '' }}>
                        {{ $option->label }}
                        <span class="count">({{ $option->products_count }})</span>
                    </label>
                @endforeach
            @endif
        </div>
    @endforeach
</div>
```

### 2.4 URL Handling
```php
// Тековна URL: /product-grids
// Со филтри: /product-grids?color=red,blue&size=large

// Route:
Route::get('/product-grids', [ProductController::class, 'productGrids'])->name('front.product-grids');
```

---

## Фаза 3: Attribute Families (1 недела)

### 3.1 Нова Табела
```php
Schema::create('attribute_families', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('code')->unique();
    $table->timestamps();
});

Schema::create('attribute_family_attributes', function (Blueprint $table) {
    $table->id();
    $table->foreignId('attribute_family_id')->constrained()->onDelete('cascade');
    $table->foreignId('attribute_group_id')->constrained()->onDelete('cascade');
    $table->foreignId('attribute_id')->constrained()->onDelete('cascade');
    $table->integer('position')->default(0);
});

Schema::create('category_attribute_families', function (Blueprint $table) {
    $table->id();
    $table->foreignId('category_id')->constrained()->onDelete('cascade');
    $table->foreignId('attribute_family_id')->constrained()->onDelete('cascade');
});
```

### 3.2 Модел
```php
class AttributeFamily extends Model {
    protected $fillable = ['name', 'code'];
    
    public function attributes() {
        return $this->belongsToMany(Attribute::class, 'attribute_family_attributes')
            ->withPivot('attribute_group_id', 'position')
            ->orderBy('position');
    }
    
    public function groups() {
        return $this->belongsToMany(AttributeGroup::class, 'attribute_family_attributes')
            ->distinct();
    }
}
```

### 3.3 UI за Assign на Family
```php
// На Category форма:
- Select: "Attribute Family" (Clothing, Electronics, Furniture...)

// На Product форма:
- Се прикажуваат само атрибутите од таа фамилија
- Групирани по Attribute Groups
```

---

## Фаза 4: Подобрувања на UI/UX (1 недела)

### 4.1 Visual Swatches
```php
// За color атрибути:
- Покажувај color picker во админ
- Покажувај swatches на frontend

// За image swatches:
- Додади image колона во attribute_options
- Покажувај слики наместо текст
```

### 4.2 AJAX Filter
```javascript
// Филтрирање без refresh на страница
$('.filter-option input').on('change', function() {
    const filters = collectFilters();
    
    $.ajax({
        url: '/api/products/filter',
        data: filters,
        success: function(response) {
            $('#product-grid').html(response.html);
            updateUrl(filters);
        }
    });
});
```

### 4.3 Price Slider
```php
// Во filter sidebar:
<div id="price-slider" data-min="{{ $priceRange['min'] }}" data-max="{{ $priceRange['max'] }}">
<input type="hidden" name="price_min" id="price-min">
<input type="hidden" name="price_max" id="price-max">
```

---

## Фаза 5: Elasticsearch Integration (1 недела)

### 5.1 Index на Attributes
```php
// Во Elasticsearch mapping:
'properties' => [
    'attributes' => [
        'type' => 'nested',
        'properties' => [
            'code' => ['type' => 'keyword'],
            'value' => ['type' => 'text'],
            'value_text' => ['type' => 'text'],
            'value_boolean' => ['type' => 'boolean'],
            'value_integer' => ['type' => 'integer'],
        ]
    ]
]

// Indexing:
$product->attributes = $product->attributeValues->map(fn($av) => [
    'code' => $av->attribute->code,
    'value' => $av->value,
]);
```

### 5.2 Search по Атрибути
```php
// Пребарување: "red shirt size L"
$query = [
    'bool' => [
        'must' => [
            ['match' => ['attributes.value' => 'red']],
            ['match' => ['title' => 'shirt']],
            ['match' => ['attributes.value' => 'L']],
        ]
    ]
];
```

---

## Времеплан

| Фаза | Време | Приоритет |
|------|-------|-----------|
| 1. Configurable Products | 2-3 недели | 🔴 Критично |
| 2. Layered Navigation | 1-2 недели | 🔴 Критично |
| 3. Attribute Families | 1 недела | 🟡 Среден |
| 4. UI/UX Подобрувања | 1 недела | 🟢 Низок |
| 5. Elasticsearch | 1 недела | 🟢 Низок |

**Вкупно време**: 6-8 недели за целосна имплементација

---

## Очекуван Резултат

По имплементацијата на овие фази, системот ќе има:

1. ✅ Configurable продукти со варијации (Color × Size)
2. ✅ Layered navigation со филтрирање по атрибути
3. ✅ Attribute families за различни категории
4. ✅ Visual swatches за бои и слики
5. ✅ AJAX филтрирање без refresh
6. ✅ Search по спецификации

**Оценка после подобрувањата**: 9/10 за e-commerce
