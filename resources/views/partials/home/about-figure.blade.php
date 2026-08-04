@php
    // Geometry of the figure, in the SVG's own 800x1000 space.
    $cx = 196; $cy = 700; $r = 82;          // unit circle
    $x0 = 320; $x1 = 724;                    // where the wave is plotted
    $steps = 120;

    // The curve is drawn here rather than in JavaScript so the shape is right
    // even if the script never runs — the animation only reveals it.
    $points = [];
    for ($i = 0; $i <= $steps; $i++) {
        $t = $i / $steps;
        $points[] = sprintf(
            '%s%.2f %.2f',
            $i ? 'L' : 'M',
            $x0 + ($x1 - $x0) * $t,
            $cy - $r * sin($t * 2 * M_PI)
        );
    }
    $sine = implode(' ', $points);
@endphp

{{--
    The department's own subject, doing what it does.

    A point travels the unit circle while its height is plotted to the right:
    the definition of sine, drawn rather than asserted. It replaces a
    placeholder of grey circles at desks that had no business on a mathematics
    faculty's page — and which shipped to production reading "PLACEHOLDER IMAGE".
--}}
<svg viewBox="0 0 800 1000" class="h-full w-full" role="img" data-math-figure
     aria-label="A point moving around a circle traces a sine wave — the Department of Mathematics, Rajshahi College">
    <defs>
        <linearGradient id="fig-wall" x1="0" y1="0" x2="0.4" y2="1">
            <stop offset="0" stop-color="#132340"/>
            <stop offset="1" stop-color="#070e1b"/>
        </linearGradient>
        <pattern id="fig-grid" width="50" height="50" patternUnits="userSpaceOnUse">
            <path d="M50 0H0V50" fill="none" stroke="#ffffff" stroke-opacity="0.045" stroke-width="1"/>
        </pattern>
    </defs>

    <rect width="800" height="1000" fill="url(#fig-wall)"/>
    <rect width="800" height="1000" fill="url(#fig-grid)"/>

    {{-- Blackboard --}}
    <rect x="70" y="120" width="660" height="380" rx="10"
          fill="#04080f" stroke="#c8a863" stroke-opacity="0.35" stroke-width="3"/>
    <g stroke="#a8d5d8" stroke-opacity="0.32" fill="none" stroke-width="2.5" stroke-linecap="round">
        <path d="M120 230h300M120 288h240M120 346h340M120 404h180"/>
    </g>
    <g fill="#c8a863" fill-opacity="0.42" font-family="Georgia, serif" font-style="italic">
        <text x="500" y="250" font-size="72">&#8721;</text>
        <text x="500" y="342" font-size="48">lim</text>
        <text x="500" y="428" font-size="48">&#8730;n</text>
    </g>

    {{-- ---- The figure ---- --}}
    <g data-figure>
        {{-- Axes --}}
        <g stroke="#ffffff" stroke-opacity="0.14" stroke-width="1.5">
            <path d="M{{ $x0 }} {{ $cy }}H{{ $x1 + 14 }}"/>
            <path d="M{{ $cx }} {{ $cy - $r - 26 }}V{{ $cy + $r + 26 }}"/>
            <path d="M{{ $cx - $r - 26 }} {{ $cy }}H{{ $x0 - 8 }}"/>
        </g>

        {{-- Unit circle --}}
        <circle cx="{{ $cx }}" cy="{{ $cy }}" r="{{ $r }}"
                fill="none" stroke="#a8d5d8" stroke-opacity="0.30" stroke-width="2"/>

        {{-- The curve, revealed as the point travels --}}
        <path data-figure-wave d="{{ $sine }}" fill="none"
              stroke="#c8a863" stroke-opacity="0.85" stroke-width="3"
              stroke-linecap="round" stroke-linejoin="round"/>

        {{-- Radius, height and the tie between them --}}
        <line data-figure-radius x1="{{ $cx }}" y1="{{ $cy }}" x2="{{ $cx + $r }}" y2="{{ $cy }}"
              stroke="#a8d5d8" stroke-opacity="0.65" stroke-width="2"/>
        <line data-figure-height x1="{{ $cx + $r }}" y1="{{ $cy }}" x2="{{ $cx + $r }}" y2="{{ $cy }}"
              stroke="#c8a863" stroke-opacity="0.55" stroke-width="2"/>
        <line data-figure-link x1="{{ $cx + $r }}" y1="{{ $cy }}" x2="{{ $x0 }}" y2="{{ $cy }}"
              stroke="#c8a863" stroke-opacity="0.28" stroke-width="1.5" stroke-dasharray="4 6"/>

        {{-- The travelling point, and where it lands on the curve --}}
        <circle data-figure-dot cx="{{ $cx + $r }}" cy="{{ $cy }}" r="7"
                fill="#c8a863" fill-opacity="0.95"/>
        <circle data-figure-tip cx="{{ $x0 }}" cy="{{ $cy }}" r="5.5"
                fill="#a8d5d8" fill-opacity="0.9"/>

        <text x="{{ $cx }}" y="{{ $cy + $r + 54 }}" text-anchor="middle"
              fill="#ffffff" fill-opacity="0.30"
              font-family="Georgia, serif" font-style="italic" font-size="26">sin&#952;</text>
    </g>

    <text x="400" y="932" text-anchor="middle" fill="#ffffff" fill-opacity="0.20"
          font-family="ui-monospace, monospace" font-size="14" letter-spacing="5">
        DEPARTMENT OF MATHEMATICS
    </text>
</svg>
