@php
    $links = array_filter([
        'facebook' => config('rcmaa.social.facebook'),
        'linkedin' => config('rcmaa.social.linkedin'),
        'twitter' => config('rcmaa.social.twitter'),
        'whatsapp' => config('rcmaa.social.whatsapp'),
    ]);
    $labels = ['facebook' => 'Facebook', 'linkedin' => 'LinkedIn', 'twitter' => 'X (Twitter)', 'whatsapp' => 'WhatsApp'];
@endphp

{{-- $size lets a context choose how large the icons draw. The footer passes a
     bigger one — the association found the defaults too small to tap. --}}
<ul class="flex items-center gap-3 {{ $class ?? '' }}">
    @foreach ($links as $key => $url)
        <li>
            <a href="{{ $url }}" target="_blank" rel="noopener noreferrer"
               class="-m-1.5 block p-1.5 transition-colors duration-300 hover:text-brass-500"
               aria-label="{{ config('rcmaa.short_name') }} on {{ $labels[$key] }}">
                <x-icon :name="$key" class="{{ $size ?? 'h-4 w-4' }}"/>
            </a>
        </li>
    @endforeach
</ul>
