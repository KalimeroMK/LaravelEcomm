# 🔥 Blaze Optimization Guide for Themes

## Overview

This project uses **Blaze** - a high-performance Blade compiler that significantly speeds up view rendering. This is a fork by KalimeroMK that adds support for `View::share()` and `View::composer()`.

### Performance Improvements

| Strategy | Improvement | Use Case |
|----------|-------------|----------|
| **Function Compiler** (default) | 91-97% faster | All components - safe to use everywhere |
| **Runtime Memoization** | Caches repeated renders | Icons, avatars, badges - repeated components |
| **Compile-Time Folding** | Static HTML at compile | Spinner, dividers - truly static components |

## Quick Start

### 1. Enable Blaze for Your Theme

Add your theme to `config/blaze.php`:

```php
'themes' => [
    'your-theme' => [
        'enabled' => true,
        'strategy' => [
            'compile' => true,  // Enable function compiler
            'memo' => true,     // Enable memoization for icons
            'fold' => false,    // Keep fold disabled by default
        ],
        'components' => [
            'compile' => ['*'],
            'memo' => ['icon*', 'avatar*', 'badge*', 'button*'],
            'fold' => [],
            'exclude' => [],
        ],
    ],
],
```

### 2. Add @blaze Directive to Components

Add `@blaze` at the top of your anonymous components:

```blade
@blaze

@props(['type' => 'button'])

<button {{ $attributes }}>
    {{ $slot }}
</button>
```

### 3. Clear and Warm Cache

```bash
# Check status
php artisan blaze:optimize --status

# Warm cache for active theme
php artisan blaze:optimize

# Warm cache for all themes
php artisan blaze:optimize --all

# Clear cache
php artisan blaze:optimize --clear

# Show recommendations
php artisan blaze:optimize --recommendations
```

## CLI Commands

| Command | Description |
|---------|-------------|
| `php artisan blaze:optimize` | Warm cache for active theme |
| `php artisan blaze:optimize --all` | Warm cache for all themes |
| `php artisan blaze:optimize --theme=modern` | Warm specific theme |
| `php artisan blaze:optimize --clear` | Clear Blaze cache |
| `php artisan blaze:optimize --status` | Show Blaze status |
| `php artisan blaze:optimize --recommendations` | Show optimization tips |

## Optimization Strategies

### Strategy 1: Function Compiler (Default)

**Safe for all components.** Use `@blaze` without parameters:

```blade
@blaze

@props(['variant' => 'primary'])

<button class="btn btn-{{ $variant }}">
    {{ $slot }}
</button>
```

**Benefits:**
- 91-97% faster rendering
- Zero risk of bugs
- Works with dynamic content
- Supports `View::share()` and `View::composer()`

### Strategy 2: Runtime Memoization

**Best for repeated components** like icons and avatars:

```blade
@blaze(memo: true)

@props(['name', 'size' => 24])

<svg width="{{ $size }}" height="{{ $size }}">
    <!-- Icon content -->
</svg>
```

**Requirements:**
- Component must NOT have slots (only attributes)
- Same props = cached output
- Perfect for icon libraries

**Benefits:**
- Icons render only once per unique combination
- Massive savings when using many icons

### Strategy 3: Compile-Time Folding

**⚠️ DANGEROUS - Use with caution!**

Only for truly static components:

```blade
@blaze(fold: true)

@props(['size' => 'md'])

@php
$sizeClasses = match($size) {
    'sm' => 'w-4 h-4',
    'md' => 'w-6 h-6',
    default => 'w-6 h-6',
};
@endphp

<svg class="animate-spin {{ $sizeClasses }}">
    <!-- Spinner SVG -->
</svg>
```

**⚠️ NEVER use fold with:**
- Database queries (`User::get()`)
- Authentication checks (`auth()->check()`)
- Session data (`session('key')`)
- Request info (`request()->path()`)
- Time functions (`now()`, `Carbon::now()`)
- CSRF tokens (`@csrf`)

## Theme Checklist

When creating a new theme:

- [ ] Add theme to `config/blaze.php`
- [ ] Add `@blaze` to all anonymous components
- [ ] Use `@blaze(memo: true)` for icons
- [ ] Use `@blaze(fold: true)` only for truly static components
- [ ] Test with `php artisan blaze:optimize --status`
- [ ] Run `php artisan blaze:optimize --all` after deployment

## Best Practices

1. **Start with `@blaze` only** - no parameters, safest option
2. **Profile first** - use debug mode to find bottlenecks
3. **Use memoization for icons** - biggest impact for repeated components
4. **Be careful with folding** - only for completely static content
5. **Test thoroughly** - especially when using fold strategy
6. **Clear cache on deploy** - always warm cache after deployment
