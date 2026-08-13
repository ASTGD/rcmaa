@props(['name', 'class' => 'h-5 w-5'])

@php
    // Single stroke-based set so every icon shares one visual weight.
    $paths = [
        'mail' => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3.5 6.5 8.5 6 8.5-6"/>',
        'phone' => '<path d="M4.5 4.5h4l1.5 4-2.2 1.6a12 12 0 0 0 6.1 6.1l1.6-2.2 4 1.5v4a1.5 1.5 0 0 1-1.7 1.5A17.5 17.5 0 0 1 3 6.2 1.5 1.5 0 0 1 4.5 4.5Z"/>',
        'map-pin' => '<path d="M12 21s7-5.5 7-11a7 7 0 1 0-14 0c0 5.5 7 11 7 11Z"/><circle cx="12" cy="10" r="2.6"/>',
        'clock' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5.2l3.2 2"/>',
        'calendar' => '<rect x="3.5" y="5" width="17" height="16" rx="2.5"/><path d="M8 3v4M16 3v4M3.5 10h17"/>',
        'chevron-down' => '<path d="m6 9 6 6 6-6"/>',
        'chevron-left' => '<path d="m15 6-6 6 6 6"/>',
        'chevron-right' => '<path d="m9 6 6 6-6 6"/>',
        'arrow-right' => '<path d="M4 12h16m0 0-6-6m6 6-6 6"/>',
        'arrow-up-right' => '<path d="M7 17 17 7m0 0H8m9 0v9"/>',
        'arrow-down' => '<path d="M12 4v16m0 0 6-6m-6 6-6-6"/>',
        'check' => '<path d="m5 13 4.5 4.5L19 7"/>',
        'check-circle' => '<circle cx="12" cy="12" r="9"/><path d="m8.5 12.2 2.4 2.4 4.6-4.9"/>',
        'x' => '<path d="m6 6 12 12M18 6 6 18"/>',
        'alert' => '<circle cx="12" cy="12" r="9"/><path d="M12 7.5v5.5M12 16.3v.2"/>',
        'user' => '<circle cx="12" cy="8" r="3.6"/><path d="M4.8 20a7.2 7.2 0 0 1 14.4 0"/>',
        'users' => '<circle cx="9" cy="8" r="3.2"/><path d="M3 19.5a6 6 0 0 1 12 0"/><path d="M16.5 5.2a3.2 3.2 0 0 1 0 5.9M17.5 14.4a6 6 0 0 1 3.5 5.1"/>',
        'download' => '<path d="M12 4v11m0 0 4-4m-4 4-4-4M4.5 19.5h15"/>',
        'upload' => '<path d="M12 16V5m0 0 4 4m-4-4-8 4M4.5 19.5h15"/>',
        'search' => '<circle cx="11" cy="11" r="6.5"/><path d="m16 16 4 4"/>',
        'quote' => '<path d="M9.5 6C6.5 7.5 5 10 5 13v5h6v-6H8c0-2 .5-3.5 2.5-4.5ZM19.5 6C16.5 7.5 15 10 15 13v5h6v-6h-3c0-2 .5-3.5 2.5-4.5Z"/>',
        'sparkle' => '<path d="M12 3.5 13.9 9l5.6 2-5.6 2L12 18.5 10.1 13l-5.6-2 5.6-2Z"/>',
        'sigma' => '<path d="M17.5 5.5h-11L12 12l-5.5 6.5h11"/>',
        'pi' => '<path d="M5 8h14M9 8v9M16 8v6.5c0 1.4.7 2.5 2 2.5"/>',
        'compass' => '<circle cx="12" cy="12" r="9"/><path d="m15 9-1.8 4.2L9 15l1.8-4.2Z"/>',
        'book' => '<path d="M4.5 5.5A2 2 0 0 1 6.5 4H19v14H6.5a2 2 0 0 0-2 2Z"/><path d="M4.5 5.5V20"/>',
        'graduation' => '<path d="m12 4 9 4.5-9 4.5-9-4.5Z"/><path d="M6.5 10.8V16c0 1.4 2.5 2.6 5.5 2.6s5.5-1.2 5.5-2.6v-5.2"/>',
        'shield' => '<path d="M12 3.5 19.5 6v6c0 4.2-3 7.5-7.5 9-4.5-1.5-7.5-4.8-7.5-9V6Z"/>',
        'globe' => '<circle cx="12" cy="12" r="9"/><path d="M3.2 12h17.6M12 3a15 15 0 0 1 0 18 15 15 0 0 1 0-18Z"/>',
        'heart' => '<path d="M12 20s-7.5-4.6-7.5-9.5A4.2 4.2 0 0 1 12 7.6a4.2 4.2 0 0 1 7.5 2.9C19.5 15.4 12 20 12 20Z"/>',
        'briefcase' => '<rect x="3" y="7.5" width="18" height="12.5" rx="2"/><path d="M8.5 7.5V6a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v1.5M3 13h18"/>',
        'camera' => '<path d="M3.5 8.5h3l1.5-2.5h8l1.5 2.5h3v10.5h-17Z"/><circle cx="12" cy="13.2" r="3.4"/>',
        'bell' => '<path d="M6.5 10a5.5 5.5 0 0 1 11 0c0 4 1.5 5.5 1.5 5.5H5S6.5 14 6.5 10Z"/><path d="M10 19a2.2 2.2 0 0 0 4 0"/>',
        'facebook' => '<path d="M14.5 8.5h2.2V5.4h-2.6c-2.4 0-3.8 1.5-3.8 3.9v1.9H8v3.1h2.3V21h3.3v-6.7h2.3l.4-3.1h-2.7V9.6c0-.8.3-1.1.9-1.1Z" fill="currentColor" stroke="none"/>',
        'linkedin' => '<path d="M6.9 8.9H4.2V21h2.7ZM5.5 3.2a1.7 1.7 0 1 0 0 3.4 1.7 1.7 0 0 0 0-3.4ZM19.8 13.5c0-3-1.6-4.8-4-4.8a3.4 3.4 0 0 0-3 1.6V8.9H9.9V21h2.7v-6.3c0-1.5.7-2.5 2-2.5s2 .9 2 2.4V21h2.7Z" fill="currentColor" stroke="none"/>',
        'twitter' => '<path d="M17.6 3.5h3.1l-6.8 7.8L21.6 21h-5.9l-4.4-5.7L6.1 21H3l7.3-8.3L2.8 3.5h6l4 5.3Zm-1.1 15.6h1.7L7.6 5.3H5.8Z" fill="currentColor" stroke="none"/>',
        'whatsapp' => '<path d="M12 3.6a8.3 8.3 0 0 0-7.1 12.6L3.6 20.9l4.8-1.3A8.3 8.3 0 1 0 12 3.6Zm4.5 11.6c-.2.6-1.1 1.1-1.6 1.2-.9.1-1.6.1-3.4-.7-2.6-1.1-4.3-3.8-4.4-4-.1-.2-1-1.4-1-2.6s.6-1.8.9-2.1c.2-.2.5-.3.7-.3h.5c.2 0 .4 0 .6.5l.8 2c.1.2 0 .4-.1.5l-.4.5c-.1.2-.3.3-.1.6.2.3.7 1.2 1.5 2 1 .8 1.8 1.1 2.1 1.2.2.1.4.1.5-.1l.8-1c.2-.2.3-.2.6-.1l1.9.9c.3.1.4.2.5.3 0 .1 0 .6-.2 1.2Z" fill="currentColor" stroke="none"/>',
        'menu' => '<path d="M4 7h16M4 12h16M4 17h16"/>',
        'external' => '<path d="M14 4h6v6M20 4l-9 9M18 14v5a1.5 1.5 0 0 1-1.5 1.5h-11A1.5 1.5 0 0 1 4 19V8a1.5 1.5 0 0 1 1.5-1.5H10"/>',
        'filter' => '<path d="M4 6h16M7 12h10M10 18h4"/>',
        'logout' => '<path d="M15 4.5h3.5A1.5 1.5 0 0 1 20 6v12a1.5 1.5 0 0 1-1.5 1.5H15M11 8l-4 4 4 4M7 12h11"/>',
        'lock' => '<rect x="4.5" y="10" width="15" height="10.5" rx="2"/><path d="M8 10V7.5a4 4 0 0 1 8 0V10"/>',
        'trash' => '<path d="M4.5 7h15M9.5 7V5.5A1.5 1.5 0 0 1 11 4h2a1.5 1.5 0 0 1 1.5 1.5V7M6.5 7l.8 12.1A1.5 1.5 0 0 0 8.8 20.5h6.4a1.5 1.5 0 0 0 1.5-1.4L17.5 7"/>',
        'plus' => '<path d="M12 5v14M5 12h14"/>',
        'play' => '<path d="M8 5.5 18.5 12 8 18.5Z"/>',
        'link' => '<path d="M10.5 13.5a4 4 0 0 0 5.7 0l2.3-2.3a4 4 0 0 0-5.7-5.7l-1.3 1.3"/><path d="M13.5 10.5a4 4 0 0 0-5.7 0l-2.3 2.3a4 4 0 0 0 5.7 5.7l1.3-1.3"/>',
        'database' => '<ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/><path d="M3 12c0 1.66 4 3 9 3s9-1.34 9-3"/>',
    ];
@endphp

<svg {{ $attributes->merge(['class' => $class]) }}
     viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"
     stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    {!! $paths[$name] ?? $paths['sparkle'] !!}
</svg>
