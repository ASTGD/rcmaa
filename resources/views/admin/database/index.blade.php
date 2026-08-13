<x-admin-layout :title="$title">
    <div class="max-w-2xl">
        <div class="card p-6">
            <div class="flex items-center gap-4">
                <span class="grid h-12 w-12 place-items-center rounded-2xl bg-brass-100 text-brass-700">
                    <x-icon name="database" class="h-6 w-6"/>
                </span>
                <div>
                    <h1 class="heading-display text-xl text-ink-950">Database Backup</h1>
                    <p class="text-xs text-ink-400">Download a full SQL export of your database tables and data</p>
                </div>
            </div>

            <div class="mt-6 border-t border-ink-100 pt-6">
                <p class="text-sm leading-relaxed text-ink-600">
                    This utility generates a complete backup of the database containing all tables, schema definitions, and rows. Keep this file secure as it contains personal information about registered members.
                </p>

                <div class="mt-4 rounded-xl bg-red-50 border border-red-200 p-4 text-xs text-red-800">
                    <div class="flex gap-2">
                        <x-icon name="alert" class="h-4 w-4 flex-none"/>
                        <span>
                            <strong>Important:</strong> SQL backups contain sensitive registration data, including credentials, contact info, and payment records. Store downloads in a safe, encrypted location.
                        </span>
                    </div>
                </div>
            </div>

            <div class="mt-6 border-t border-ink-100 pt-6">
                <form action="{{ route('admin.database.backup') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary flex items-center gap-2">
                        <x-icon name="download" class="h-4 w-4"/>
                        Download SQL Backup
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
