@blaze

@props([
    'type' => 'button',
    'variant' => 'primary',
    'size' => 'md',
    'disabled' => false,
    'loading' => false,
])

@php
$baseClasses = 'inline-flex items-center justify-center font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2';

$variantClasses = match($variant) {
    'primary' => 'bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500',
    'secondary' => 'bg-gray-200 text-gray-900 hover:bg-gray-300 focus:ring-gray-500',
    'danger' => 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500',
    'outline' => 'border-2 border-gray-300 text-gray-700 hover:border-gray-400 focus:ring-gray-500',
    'ghost' => 'text-gray-700 hover:bg-gray-100 focus:ring-gray-500',
    default => 'bg-blue-600 text-white hover:bg-blue-700',
};

$sizeClasses = match($size) {
    'sm' => 'px-3 py-1.5 text-sm',
    'md' => 'px-4 py-2 text-base',
    'lg' => 'px-6 py-3 text-lg',
    default => 'px-4 py-2 text-base',
};

$classes = implode(' ', [$baseClasses, $variantClasses, $sizeClasses]);
@endphp

<button 
    type="{{ $type }}"
    {{ $attributes->merge(['class' => $classes, 'disabled' => $disabled]) }}
>
    @if($loading)
        <x-front::spinner class="w-4 h-4 mr-2" />
    @endif
    
    {{ $slot }}
</button>
