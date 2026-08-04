@props(['icon' => 'sparkle', 'title', 'message' => null])

<div {{ $attributes->merge(['class' => 'rounded-2xl border border-dashed border-ink-900/15 bg-white/60 px-8 py-16 text-center']) }}>
    <span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-brass-100 text-brass-700">
        <x-icon :name="$icon" class="h-6 w-6"/>
    </span>
    <h3 class="heading-display mt-5 text-xl text-ink-900">{{ $title }}</h3>
    @if ($message)
        <p class="prose-rc mx-auto mt-2 max-w-md text-sm">{{ $message }}</p>
    @endif
    {{ $slot }}
</div>
