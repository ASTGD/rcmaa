@props([
    'name',
    'label',
    'bn' => null,          // Bangla sub-label, exactly as on the paper form
    'type' => 'text',
    'required' => false,
    // An Alpine expression, for fields that are only required sometimes. When
    // given it drives the asterisk instead of `required`.
    'requiredIf' => null,
    // Alpine expressions for labels that depend on another answer.
    'labelIf' => null,
    'bnIf' => null,
    'hint' => null,
    'placeholder' => null,
    'options' => null,     // for type="select"
    'rows' => 4,
    // Current value for plain server-rendered forms. Alpine-backed forms hold
    // their own state and leave this null.
    'value' => null,
    // Lets a phone offer the name, number or address it already knows. Most
    // registrants are on a phone and many are not fast typists.
    'autocomplete' => null,
    // Alpine binding. Defaults to form.<name>; pass :model="false" on plain
    // server-rendered forms that have no Alpine state behind them.
    'model' => null,
])

@php
    $alpine = $model !== false;
    $model = $alpine ? ($model ?? 'form.'.$name) : null;

    $id = 'field-'.Str::slug(str_replace(['.', '[', ']'], '-', $name));
    $serverError = $errors->first($name);

    // What the field paints on load: a rejected submission first, then the
    // stored value, then nothing.
    $current = old($name, $value);

    // Server errors win on first paint; Alpine's take over once it boots.
    $binding = $alpine
        ? ' x-model="'.e($model).'" :aria-invalid="errors[\''.e($name).'\'] ? \'true\' : \'false\'"'
        : '';
@endphp

<div {{ $attributes->merge(['class' => 'min-w-0']) }}>
    <label for="{{ $id }}" class="field-label">
        @if ($labelIf)
            <span x-text="{{ $labelIf }}">{{ $label }}</span>
        @else
            {{ $label }}
        @endif
        @if ($bnIf)
            <span lang="bn" class="field-label-bn"> &middot; <span x-text="{{ $bnIf }}">{{ $bn }}</span></span>
        @elseif ($bn)
            <span lang="bn" class="field-label-bn"> &middot; {{ $bn }}</span>
        @endif
        @if ($requiredIf)
            <span class="text-red-600" aria-hidden="true" x-show="{{ $requiredIf }}">*</span>
        @elseif ($required)
            <span class="text-red-600" aria-hidden="true">*</span>
        @endif
    </label>

    @if ($type === 'textarea')
        <textarea id="{{ $id }}" name="{{ $name }}" rows="{{ $rows }}" class="input"
                  placeholder="{{ $placeholder }}"
                  @if ($autocomplete) autocomplete="{{ $autocomplete }}" @endif @if ($serverError) aria-invalid="true" @endif
                  {!! $binding !!}>{{ $current }}</textarea>

    @elseif ($type === 'select')
        <select id="{{ $id }}" name="{{ $name }}" class="input"
                @if ($serverError) aria-invalid="true" @endif {!! $binding !!}>
            <option value="">{{ $placeholder ?? 'Select…' }}</option>
            @foreach ($options as $value => $text)
                @php $optionValue = is_int($value) ? $text : $value; @endphp
                <option value="{{ $optionValue }}" @selected((string) $current === (string) $optionValue)>{{ $text }}</option>
            @endforeach
        </select>

    @else
        <input id="{{ $id }}" name="{{ $name }}" type="{{ $type }}" class="input"
               placeholder="{{ $placeholder }}" value="{{ $current }}"
               @if ($autocomplete) autocomplete="{{ $autocomplete }}" @endif
               @if ($type === 'tel') inputmode="tel" @endif
               @if ($type === 'number') inputmode="numeric" @endif
               @if ($serverError) aria-invalid="true" @endif {!! $binding !!}>
    @endif

    @if ($hint)
        <p class="field-hint">{{ $hint }}</p>
    @endif

    @if ($alpine)
        <p class="field-error" x-show="errors['{{ $name }}']" x-text="errors['{{ $name }}']" x-cloak></p>
        @if ($serverError)
            <p class="field-error" x-show="!errors['{{ $name }}']">{{ $serverError }}</p>
        @endif
    @elseif ($serverError)
        <p class="field-error">{{ $serverError }}</p>
    @endif
</div>
