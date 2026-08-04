@props(['status'])

@php
    $map = [
        'pending' => ['Pending', 'bg-amber-100 text-amber-800', 'bg-amber-500'],
        'verified' => ['Verified', 'bg-emerald-100 text-emerald-800', 'bg-emerald-500'],
        'rejected' => ['Rejected', 'bg-red-100 text-red-800', 'bg-red-500'],
    ];
    [$label, $chip, $dot] = $map[$status] ?? $map['pending'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex flex-none items-center gap-1.5 rounded-full px-2.5 py-1 text-[0.68rem] font-semibold $chip"]) }}>
    <span class="h-1.5 w-1.5 rounded-full {{ $dot }}"></span>{{ $label }}
</span>
