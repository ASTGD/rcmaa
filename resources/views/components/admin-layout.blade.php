@props(['title'])

@php
    $nav = [
        ['label' => 'Dashboard', 'icon' => 'compass', 'href' => route('admin.dashboard'), 'active' => request()->routeIs('admin.dashboard')],
        ['label' => 'Registrations', 'icon' => 'users', 'href' => route('admin.registrations.index'), 'active' => request()->routeIs('admin.registrations.*')],
        ['label' => 'Messages', 'icon' => 'mail', 'href' => route('admin.messages.index'), 'active' => request()->routeIs('admin.messages.*'),
         'badge' => \App\Models\ContactMessage::where('is_read', false)->count()],
        ['label' => 'Accounts', 'icon' => 'lock', 'href' => route('admin.users.index'), 'active' => request()->routeIs('admin.users.*')],
    ];
    $content = \App\Http\Controllers\Admin\ContentController::menu();
@endphp

<!DOCTYPE html>
<html lang="en" class="antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $title }} &mdash; RCMAA Admin</title>
    <link rel="icon" href="{{ asset('favicon-32.png') }}" sizes="32x32" type="image/png">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-parchment-dim">
<div x-data="{ sidebar: false }" class="flex min-h-screen">

    {{-- Sidebar --}}
    <aside class="fixed inset-y-0 left-0 z-40 flex w-64 flex-col bg-ink-950 transition-transform duration-300 lg:static lg:translate-x-0"
           :class="sidebar ? 'translate-x-0' : '-translate-x-full'">
        <div class="flex h-16 flex-none items-center border-b border-white/8 px-5">
            <a href="{{ route('home') }}" class="flex items-center gap-3" target="_blank" rel="noopener">
                <x-logo light :wordmark="false" size="h-8 w-8"/>
                <span class="flex flex-col leading-none">
                    <span class="heading-display text-base text-parchment">RCMAA</span>
                    <span class="mt-0.5 font-mono text-[0.5rem] uppercase tracking-[0.2em] text-brass-500">Administration</span>
                </span>
            </a>
        </div>

        <nav class="flex-1 overflow-y-auto p-3">
            <ul class="space-y-1">
                @foreach ($nav as $item)
                    <li>
                        <a href="{{ $item['href'] }}"
                           @class([
                               'flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm transition-colors',
                               'bg-brass-500 font-semibold text-ink-950' => $item['active'],
                               'text-ink-300 hover:bg-white/5 hover:text-parchment' => ! $item['active'],
                           ])>
                            <x-icon :name="$item['icon']" class="h-4 w-4 flex-none"/>
                            <span class="flex-1">{{ $item['label'] }}</span>
                            @if (! empty($item['badge']))
                                <span class="rounded-full bg-red-500 px-1.5 py-0.5 text-[0.6rem] font-bold text-white">
                                    {{ $item['badge'] }}
                                </span>
                            @endif
                        </a>
                    </li>
                @endforeach
            </ul>

            <p class="mt-6 px-3.5 font-mono text-[0.58rem] uppercase tracking-[0.2em] text-ink-500">Content</p>
            <ul class="mt-2 space-y-1">
                @foreach ($content as $item)
                    @php $active = request()->routeIs('admin.content.*') && request()->route('type') === $item['key']; @endphp
                    <li>
                        <a href="{{ route('admin.content.index', $item['key']) }}"
                           @class([
                               'flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm transition-colors',
                               'bg-brass-500 font-semibold text-ink-950' => $active,
                               'text-ink-300 hover:bg-white/5 hover:text-parchment' => ! $active,
                           ])>
                            <x-icon :name="$item['icon']" class="h-4 w-4 flex-none"/>
                            {{ $item['label'] }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </nav>

        <div class="flex-none border-t border-white/8 p-3">
            <div class="rounded-xl bg-white/5 p-3">
                <p class="truncate text-sm font-medium text-parchment">{{ auth()->user()->name }}</p>
                <p class="truncate text-xs text-ink-400">{{ auth()->user()->email }}</p>
                <a href="{{ route('admin.account') }}"
                   class="mt-3 flex items-center gap-2 rounded-lg px-2 py-1.5 text-xs text-ink-300 transition hover:bg-white/5 hover:text-parchment">
                    <x-icon name="user" class="h-3.5 w-3.5"/>Your account
                </a>
                <form method="POST" action="{{ route('logout') }}" class="mt-1">
                    @csrf
                    <button type="submit" class="flex w-full items-center gap-2 rounded-lg px-2 py-1.5 text-xs text-ink-300 transition hover:bg-white/5 hover:text-parchment">
                        <x-icon name="logout" class="h-3.5 w-3.5"/>Sign out
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <div x-show="sidebar" x-cloak @click="sidebar = false"
         class="fixed inset-0 z-30 bg-ink-950/60 lg:hidden"></div>

    {{-- Main --}}
    <div class="flex min-w-0 flex-1 flex-col">
        <header class="sticky top-0 z-20 flex h-16 flex-none items-center gap-4 border-b border-ink-900/8 bg-parchment/90 px-5 backdrop-blur-xl lg:px-8">
            <button type="button" @click="sidebar = !sidebar"
                    class="grid h-10 w-10 place-items-center rounded-xl border border-ink-900/10 lg:hidden"
                    aria-label="Toggle sidebar">
                <x-icon name="menu" class="h-4 w-4"/>
            </button>

            <h1 class="heading-display flex-1 truncate text-xl text-ink-950">{{ $title }}</h1>

            {{ $actions ?? '' }}
        </header>

        <main class="flex-1 p-5 lg:p-8">
            @if (session('status'))
                <x-alert type="success" class="mb-6">{{ session('status') }}</x-alert>
            @endif
            @if ($errors->any())
                <x-alert type="error" title="Please correct the following" class="mb-6">
                    <ul class="mt-1 list-disc space-y-0.5 pl-4">
                        @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </x-alert>
            @endif

            {{ $slot }}
        </main>
    </div>
</div>
</body>
</html>
