<table class="head" width="100%">
    <tr>
        @if ($logo)
            <td width="64"><img src="{{ $logo }}" class="logo" alt=""></td>
        @endif
        <td>
            <div class="org">
                {{ config('rcmaa.name') }}
                <small>Rajshahi College &middot; Mathematics</small>
            </div>
        </td>
        <td class="doc">
            <div class="kind">{{ $kind }}</div>
            <div class="ref">{{ $r->reference }}</div>
        </td>
    </tr>
</table>
