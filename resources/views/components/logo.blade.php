@props([
    'class' => '',
    'iconSize' => 'w-10 h-10',
    'svgSize' => 'w-6 h-6',
    'textSize' => 'text-2xl',
])

<a href="{{ route('home') }}" class="site-brand {{ $class }}">
    <div class="site-brand__icon {{ $iconSize }}">
        <svg class="{{ $svgSize }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
        </svg>
    </div>
    <span class="site-brand__text {{ $textSize }}">
        Shop<span class="site-brand__highlight">Mart</span>
    </span>
</a>
