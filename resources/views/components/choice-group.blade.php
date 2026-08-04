@props([
    'name',
    'label',
    'bn' => null,
    'options',           // value => label
    'type' => 'radio',
    'required' => false,
    'hint' => null,
    'model' => null,
    'cols' => 'sm:grid-cols-3',
])

@php
    $model ??= 'form.'.$name;
@endphp

<fieldset {{ $attributes->merge(['class' => 'min-w-0']) }}>
    <legend class="field-label">
        {{ $label }}
        @if ($bn)
            <span lang="bn" class="field-label-bn"> &middot; {{ $bn }}</span>
        @endif
        @if ($required)
            <span class="text-red-600" aria-hidden="true">*</span>
        @endif
    </legend>

    <div class="grid grid-cols-2 gap-2.5 {{ $cols }}">
        @foreach ($options as $value => $text)
            <label class="choice">
                <input type="{{ $type }}" name="{{ $name }}" value="{{ $value }}"
                       x-model="{{ $model }}" @checked(old($name) == $value)>
                <span class="choice-box" aria-hidden="true"></span>
                <span class="min-w-0 truncate">{{ $text }}</span>
            </label>
        @endforeach
    </div>

    @if ($hint)
        <p class="field-hint">{{ $hint }}</p>
    @endif

    <p class="field-error" x-show="errors['{{ $name }}']" x-text="errors['{{ $name }}']" x-cloak></p>

    @error($name)
        <p class="field-error" x-show="!errors['{{ $name }}']">{{ $message }}</p>
    @enderror
</fieldset>
