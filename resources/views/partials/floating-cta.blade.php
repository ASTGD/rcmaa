@if (config('rcmaa.registration.open') && ! auth('alumni')->check())
    {{-- Appears once the hero is behind you; dismissible for the session. --}}
    <div x-data="{ shown: false, dismissed: false }"
         x-init="window.addEventListener('scroll', () => shown = window.scrollY > 900, { passive: true })"
         x-show="shown && !dismissed" x-cloak
         x-transition:enter="transition ease-out duration-500"
         x-transition:enter-start="opacity-0 translate-y-6"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-end="opacity-0 translate-y-4"
         class="fixed inset-x-4 bottom-4 z-40 mx-auto max-w-md sm:inset-x-auto sm:right-6 sm:bottom-6">
        <div class="flex items-center gap-4 rounded-2xl border border-white/10 bg-ink-900/95 p-4 shadow-[0_24px_60px_-20px_rgba(7,14,27,.7)] backdrop-blur-xl">
            <span class="grid h-11 w-11 flex-none place-items-center rounded-xl bg-brass-500 text-ink-950">
                <x-icon name="calendar" class="h-5 w-5"/>
            </span>
            <div class="min-w-0 flex-1">
                <p class="text-[0.7rem] font-mono uppercase tracking-[0.16em] text-brass-400">Registration open</p>
                <p class="truncate text-sm font-medium text-parchment">Grand Reunion &middot; 19 Dec 2026</p>
            </div>
            <a href="{{ route('register.create') }}" class="btn btn-primary btn-sm flex-none">Register</a>
            <button type="button" @click="dismissed = true"
                    class="flex-none rounded-full p-1.5 text-ink-400 transition hover:text-parchment"
                    aria-label="Dismiss registration reminder">
                <x-icon name="x" class="h-4 w-4"/>
            </button>
        </div>
    </div>
@endif
