@props(['type' => 'success', 'title' => null])

@php
    $styles = [
        'success' => ['bg-emerald-50 border-emerald-200 text-emerald-900', 'text-emerald-600', 'check-circle'],
        'error' => ['bg-red-50 border-red-200 text-red-900', 'text-red-600', 'alert'],
        'info' => ['bg-brass-100 border-brass-300 text-brass-900', 'text-brass-700', 'bell'],
    ];
    [$box, $iconColour, $icon] = $styles[$type] ?? $styles['info'];
@endphp

<div {{ $attributes->merge(['class' => "flex gap-3.5 rounded-2xl border px-5 py-4 $box"]) }} role="alert">
    <x-icon :name="$icon" class="mt-0.5 h-5 w-5 flex-none {{ $iconColour }}"/>
    <div class="text-sm leading-relaxed">
        @if ($title)
            <p class="font-semibold">{{ $title }}</p>
        @endif
        <div class="{{ $title ? 'mt-1 opacity-90' : '' }}">{{ $slot }}</div>
    </div>
</div>
