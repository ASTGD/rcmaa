<x-admin-layout :title="$title">
    <nav class="flex gap-2" aria-label="Filter messages">
        @foreach (['' => 'All', 'unread' => 'Unread ('.$unread.')'] as $value => $label)
            @php $active = $filter === $value; @endphp
            <a href="{{ route('admin.messages.index', array_filter(['filter' => $value])) }}"
               @class([
                   'rounded-full px-4 py-2 text-[0.78rem] font-medium transition-all',
                   'bg-ink-900 text-parchment' => $active,
                   'bg-white text-ink-600 ring-1 ring-ink-900/8 hover:bg-brass-100' => ! $active,
               ])>{{ $label }}</a>
        @endforeach
    </nav>

    @if ($messages->isNotEmpty())
        <div class="mt-4 space-y-3" x-data="{ open: null }">
            @foreach ($messages as $message)
                <div @class(['card overflow-hidden', 'ring-1 ring-brass-500/50' => ! $message->is_read])>
                    <button type="button" @click="open = open === {{ $message->id }} ? null : {{ $message->id }}"
                            class="flex w-full items-center gap-4 px-5 py-4 text-left"
                            :aria-expanded="open === {{ $message->id }}">
                        <span @class([
                            'h-2 w-2 flex-none rounded-full',
                            'bg-brass-500' => ! $message->is_read,
                            'bg-ink-900/15' => $message->is_read,
                        ])></span>

                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-ink-900">{{ $message->subject }}</p>
                            <p class="truncate text-xs text-ink-400">
                                {{ $message->name }} &middot; {{ $message->email }}
                                @if ($message->phone) &middot; {{ $message->phone }} @endif
                            </p>
                        </div>

                        <time class="flex-none text-xs text-ink-400">{{ $message->created_at->diffForHumans() }}</time>
                        <x-icon name="chevron-down" class="h-4 w-4 flex-none text-ink-300 transition-transform"
                                ::class="open === {{ $message->id }} && 'rotate-180'"/>
                    </button>

                    <div x-show="open === {{ $message->id }}" x-collapse x-cloak>
                        <div class="border-t border-ink-900/6 px-5 py-4">
                            <p class="prose-rc whitespace-pre-line text-sm">{{ $message->message }}</p>

                            <div class="mt-5 flex flex-wrap gap-2">
                                <a href="mailto:{{ $message->email }}?subject={{ rawurlencode('Re: '.$message->subject) }}"
                                   class="btn btn-ink btn-sm">
                                    <x-icon name="mail" class="h-3.5 w-3.5"/>Reply by email
                                </a>

                                <form method="POST" action="{{ route('admin.messages.update', $message) }}">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="is_read" value="{{ $message->is_read ? 0 : 1 }}">
                                    <button type="submit" class="btn btn-outline btn-sm">
                                        Mark as {{ $message->is_read ? 'unread' : 'read' }}
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('admin.messages.destroy', $message) }}"
                                      onsubmit="return confirm('Delete this message?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="rounded-full border border-red-200 px-4 py-2 text-[0.68rem] font-semibold uppercase tracking-[0.08em] text-red-700 transition hover:bg-red-50">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">{{ $messages->links() }}</div>
    @else
        <x-empty-state class="mt-4" icon="mail" title="No messages"
            message="Enquiries sent through the public contact form arrive here."/>
    @endif
</x-admin-layout>
