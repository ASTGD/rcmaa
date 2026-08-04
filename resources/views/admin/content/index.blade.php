<x-admin-layout :title="$title">
    <x-slot:actions>
        <a href="{{ route('admin.content.create', $type) }}" class="btn btn-primary btn-sm">
            <x-icon name="plus" class="h-3.5 w-3.5"/>New {{ Str::lower($config['singular']) }}
        </a>
    </x-slot:actions>

    <div class="card overflow-hidden">
        @if ($records->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full min-w-[42rem] text-sm">
                    <thead>
                        <tr class="border-b border-ink-900/8 bg-parchment-dim text-left">
                            @if (isset($config['upload']))
                                <th class="w-16 px-4 py-3"></th>
                            @endif
                            @foreach ($config['columns'] as $heading)
                                <th class="px-4 py-3 font-mono text-[0.6rem] uppercase tracking-[0.14em] text-ink-500">
                                    {{ $heading }}
                                </th>
                            @endforeach
                            <th class="px-4 py-3 font-mono text-[0.6rem] uppercase tracking-[0.14em] text-ink-500">Status</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-ink-900/6">
                        @foreach ($records as $record)
                            @php
                                $uploadField = $config['upload'] ?? null;
                                $preview = $uploadField
                                    ? ($record->photo_url ?? $record->image_url ?? $record->cover_url ?? $record->logo_url ?? null)
                                    : null;
                            @endphp
                            <tr class="transition hover:bg-brass-100/40">
                                @if ($uploadField)
                                    <td class="px-4 py-3">
                                        <span class="grid h-10 w-10 place-items-center overflow-hidden rounded-lg bg-ink-900 text-brass-500">
                                            @if ($preview)
                                                <img src="{{ $preview }}" alt="" class="h-full w-full object-cover">
                                            @else
                                                <x-icon :name="$config['icon']" class="h-4 w-4"/>
                                            @endif
                                        </span>
                                    </td>
                                @endif

                                @foreach (array_keys($config['columns']) as $field)
                                    <td class="px-4 py-3 text-ink-700">
                                        @php $value = $record->{$field}; @endphp
                                        {{ $value instanceof \Carbon\CarbonInterface ? $value->format('j M Y') : Str::limit((string) $value, 60) }}
                                    </td>
                                @endforeach

                                <td class="px-4 py-3">
                                    @if ($record->is_published)
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-2.5 py-1 text-[0.68rem] font-semibold text-emerald-800">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>Published
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-ink-900/8 px-2.5 py-1 text-[0.68rem] font-semibold text-ink-500">
                                            <span class="h-1.5 w-1.5 rounded-full bg-ink-400"></span>Draft
                                        </span>
                                    @endif
                                </td>

                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-3">
                                        <a href="{{ route('admin.content.edit', [$type, $record->id]) }}"
                                           class="text-xs font-semibold text-brass-700 transition hover:text-ink-950">Edit</a>
                                        <form method="POST" action="{{ route('admin.content.destroy', [$type, $record->id]) }}"
                                              onsubmit="return confirm('Delete this {{ Str::lower($config['singular']) }}? This cannot be undone.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-ink-300 transition hover:text-red-600"
                                                    aria-label="Delete">
                                                <x-icon name="trash" class="h-4 w-4"/>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t border-ink-900/8 px-4 py-3">{{ $records->links() }}</div>
        @else
            <x-empty-state class="!border-0" :icon="$config['icon']"
                :title="'No '.Str::lower($config['plural']).' yet'"
                message="Everything you add here appears on the public site straight away.">
                <a href="{{ route('admin.content.create', $type) }}" class="btn btn-primary btn-sm mt-6">
                    Add the first one
                </a>
            </x-empty-state>
        @endif
    </div>
</x-admin-layout>
