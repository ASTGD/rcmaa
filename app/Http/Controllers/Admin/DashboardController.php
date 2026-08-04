<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\Event;
use App\Models\Notice;
use App\Models\Registration;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'title' => 'Dashboard',
            'stats' => [
                'total' => Registration::count(),
                'pending' => Registration::pending()->count(),
                'verified' => Registration::verified()->count(),
                'guests' => Registration::verified()->get()->sum('guest_total'),
                'collected' => (int) Registration::verified()->sum('amount_paid'),
                'awaiting' => (int) Registration::pending()->sum('amount_paid'),
                'unread_messages' => ContactMessage::where('is_read', false)->count(),
            ],
            'recent' => Registration::latest()->take(8)->get(),
            // Registrations per day for the last fortnight, for the sparkline.
            'trend' => Registration::query()
                ->where('created_at', '>=', now()->subDays(14))
                ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
                ->groupBy('day')
                ->orderBy('day')
                ->pluck('total', 'day'),
            'byYear' => Registration::query()
                ->selectRaw('passing_year, COUNT(*) as total')
                ->groupBy('passing_year')
                ->orderByDesc('total')
                ->take(8)
                ->get(),
            'tshirts' => Registration::query()
                ->selectRaw('tshirt_size, COUNT(*) as total')
                ->groupBy('tshirt_size')
                ->pluck('total', 'tshirt_size'),
            'upcomingEvents' => Event::published()->upcoming()->take(3)->get(),
            'latestNotice' => Notice::published()->latestFirst()->first(),
        ]);
    }
}
