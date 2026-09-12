@props([
    'class' => '',
    'iconSize' => 'w-10 h-10',
    'svgSize' => 'w-6 h-6',
    'textSize' => 'text-2xl',
])

<a href="{{ route('home') }}" class="flex items-center gap-2.5 shrink-0 group {{ $class }}">
    <div class="{{ $iconSize }} rounded-xl bg-gradient-to-tr from-[#ea384c] to-[#ff5c6c] flex items-center justify-center text-white shadow-md shadow-rose-500/20 group-hover:scale-105 transition-transform duration-200">
        <svg class="{{ $svgSize }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
        </svg>
    </div>
    <span class="{{ $textSize }} font-black tracking-tight text-gray-900 group-hover:text-[#ea384c] transition-colors">
        Shop<span class="text-[#ea384c]">Mart</span>
    </span>
</a>
