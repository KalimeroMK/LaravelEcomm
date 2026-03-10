@blaze(memo: true)

@props([
    'size' => 24,
    'class' => '',
    'strokeWidth' => 2,
])

<svg 
    xmlns="http://www.w3.org/2000/svg" 
    width="{{ $size }}" 
    height="{{ $size }}" 
    viewBox="0 0 24 24" 
    fill="none" 
    stroke="currentColor" 
    stroke-width="{{ $strokeWidth }}" 
    stroke-linecap="round" 
    stroke-linejoin="round"
    class="{{ $class }}"
>
    <polyline points="20 6 9 17 4 12"></polyline>
</svg>
