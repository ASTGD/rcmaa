<x-admin-layout :title="$title">
    <x-slot:actions>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
            <x-icon name="plus" class="h-3.5 w-3.5"/>New account
        </a>
    </x-slot:actions>

    <x-alert type="info" class="mb-6">
        Anyone with an administrator account can verify payments, see every registrant's
        contact details and edit site content. There {{ $adminCount === 1 ? 'is' : 'are' }}
        currently <strong>{{ $adminCount }}</strong> administrator{{ $adminCount === 1 ? '' : 's' }}.
        The last one cannot be removed.
    </x-alert>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[40rem] text-sm">
                <thead>
                    <tr class="border-b border-ink-900/8 bg-parchment-dim text-left">
                        @foreach (['Name', 'Email', 'Access', 'Added', ''] as $h)
                            <th class="px-4 py-3 font-mono text-[0.6rem] uppercase tracking-[0.14em] text-ink-500">{{ $h }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-ink-900/6">
                    @foreach ($users as $u)
                        <tr class="transition hover:bg-brass-100/40">
                            <td class="px-4 py-3 font-medium text-ink-900">
                                {{ $u->name }}
                                @if ($u->id === auth()->id())
                                    <span class="ml-1.5 text-[0.68rem] font-normal text-brass-700">(you)</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-ink-600">{{ $u->email }}</td>
                            <td class="px-4 py-3">
                                @if ($u->is_admin)
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-brass-200 px-2.5 py-1 text-[0.68rem] font-semibold text-brass-900">
                                        <span class="h-1.5 w-1.5 rounded-full bg-brass-600"></span>Administrator
                                    </span>
                                @else
                                    <span class="text-[0.72rem] text-ink-400">No admin access</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs text-ink-400">{{ $u->created_at?->format('j M Y') }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('admin.users.edit', $u) }}"
                                       class="text-xs font-semibold text-brass-700 transition hover:text-ink-950">Edit</a>
                                    @if ($u->id !== auth()->id() && ! ($u->is_admin && $adminCount <= 1))
                                        <form method="POST" action="{{ route('admin.users.destroy', $u) }}"
                                              onsubmit="return confirm('Delete {{ $u->name }}? They will lose access immediately.')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-ink-300 transition hover:text-red-600" aria-label="Delete">
                                                <x-icon name="trash" class="h-4 w-4"/>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="border-t border-ink-900/8 px-4 py-3">{{ $users->links() }}</div>
    </div>
</x-admin-layout>
