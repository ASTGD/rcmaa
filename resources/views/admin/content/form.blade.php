@php
    // Named option sets referenced by ContentController::types().
    $optionSets = [
        'committees' => collect(\App\Models\CommitteeMember::COMMITTEES)->map(fn ($c) => $c['en'])->all(),
        'gallery_categories' => \App\Models\GalleryItem::CATEGORIES,
        'faq_categories' => \App\Models\Faq::CATEGORIES,
        'sponsor_tiers' => \App\Models\Sponsor::TIERS,
    ];

    $action = $record->exists
        ? route('admin.content.update', [$type, $record->id])
        : route('admin.content.store', $type);

    $toggles = collect($config['fields'])->filter(fn ($f) => $f['type'] === 'toggle');
    $inputs = collect($config['fields'])->reject(fn ($f) => $f['type'] === 'toggle');
@endphp

<x-admin-layout :title="$title">
    <x-slot:actions>
        <a href="{{ route('admin.content.index', $type) }}" class="btn btn-outline btn-sm">
            <x-icon name="chevron-left" class="h-3.5 w-3.5"/>Back
        </a>
    </x-slot:actions>

    <form method="POST" action="{{ $action }}" enctype="multipart/form-data"
          class="grid max-w-5xl gap-6 lg:grid-cols-[1fr_18rem] lg:items-start">
        @csrf
        @if ($record->exists) @method('PUT') @endif

        {{-- Fields --}}
        <div class="card space-y-6 p-6">
            @foreach ($inputs as $name => $field)
                @php
                    $value = old($name, $name === 'upload' ? null : $record->{$name});
                    $id = 'f-'.$name;
                    $invalid = $errors->has($name);
                @endphp

                <div>
                    <label for="{{ $id }}" class="field-label">{{ $field['label'] }}</label>

                    @if ($field['type'] === 'textarea' || $field['type'] === 'richtext')
                        <textarea id="{{ $id }}" name="{{ $name }}"
                                  rows="{{ $field['type'] === 'richtext' ? 12 : 4 }}"
                                  class="input @if (! empty($field['bangla'])) font-bangla @endif"
                                  @if ($invalid) aria-invalid="true" @endif>{{ $value }}</textarea>
                        @if ($field['type'] === 'richtext')
                            <p class="field-hint">
                                Separate paragraphs with a blank line. Wrap text in **double asterisks** to bold it.
                            </p>
                        @endif

                    @elseif ($field['type'] === 'select')
                        <select id="{{ $id }}" name="{{ $name }}" class="input" @if ($invalid) aria-invalid="true" @endif>
                            <option value="">Select…</option>
                            @foreach ($optionSets[$field['options']] as $key => $label)
                                <option value="{{ $key }}" @selected($value === $key)>{{ $label }}</option>
                            @endforeach
                        </select>

                    @elseif ($field['type'] === 'file')
                        @php $existing = $record->{$field['target']} ?? null; @endphp
                        <div class="flex flex-wrap items-center gap-4">
                            @if ($existing)
                                <span class="grid h-20 w-20 flex-none place-items-center overflow-hidden rounded-xl bg-ink-900">
                                    <img src="{{ Storage::disk('public')->url($existing) }}" alt=""
                                         class="h-full w-full object-cover">
                                </span>
                            @endif
                            <input id="{{ $id }}" name="upload" type="file"
                                   class="input flex-1 !py-2 file:mr-3 file:rounded-lg file:border-0 file:bg-ink-900 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-parchment"
                                   @if ($invalid) aria-invalid="true" @endif>
                        </div>
                        @if ($existing)
                            <p class="field-hint">Leave empty to keep the current file.</p>
                        @endif

                    @else
                        <input id="{{ $id }}" name="{{ $name }}" type="{{ $field['type'] }}"
                               class="input @if (! empty($field['bangla'])) font-bangla @endif"
                               value="{{ $field['type'] === 'date' && $value ? \Carbon\Carbon::parse($value)->toDateString() : $value }}"
                               @if ($invalid) aria-invalid="true" @endif>
                    @endif

                    @if (! empty($field['hint']))
                        <p class="field-hint">{{ $field['hint'] }}</p>
                    @endif
                    @error($name)<p class="field-error">{{ $message }}</p>@enderror
                    @error('upload')
                        @if ($name === 'upload')<p class="field-error">{{ $message }}</p>@endif
                    @enderror
                </div>
            @endforeach
        </div>

        {{-- Publish controls --}}
        <aside class="space-y-4 lg:sticky lg:top-24">
            <div class="card p-5">
                <h2 class="font-mono text-[0.62rem] uppercase tracking-[0.16em] text-ink-500">Visibility</h2>

                <div class="mt-4 space-y-2.5">
                    @foreach ($toggles as $name => $field)
                        <label class="choice !py-2.5 !text-[0.8rem]">
                            <input type="checkbox" name="{{ $name }}" value="1"
                                   @checked(old($name, $record->exists ? $record->{$name} : ($name === 'is_published')))>
                            <span class="choice-box" aria-hidden="true"></span>
                            <span>{{ $field['label'] }}</span>
                        </label>
                    @endforeach
                </div>

                <button type="submit" class="btn btn-primary mt-5 w-full">
                    {{ $record->exists ? 'Save Changes' : 'Create' }}
                </button>
            </div>

            @if ($record->exists)
                <div class="card p-5 text-xs text-ink-400">
                    <p>Created {{ $record->created_at?->format('j M Y') }}</p>
                    <p class="mt-1">Updated {{ $record->updated_at?->diffForHumans() }}</p>
                </div>
            @endif
        </aside>
    </form>
</x-admin-layout>
