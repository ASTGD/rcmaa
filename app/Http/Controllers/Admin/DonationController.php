<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DonationController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.donations', [
            'title' => 'Donations',
            'donations' => Donation::query()
                ->when($request->string('filter')->toString() === 'unverified', fn ($q) => $q->where('is_verified', false))
                ->latest()
                ->paginate(20)
                ->withQueryString(),
            'unverified' => Donation::where('is_verified', false)->count(),
            'filter' => $request->string('filter')->toString(),
        ]);
    }

    public function update(Request $request, Donation $donation): RedirectResponse
    {
        $donation->update(['is_verified' => $request->boolean('is_verified', true)]);

        return back()->with('status', $donation->is_verified ? 'Donation verified.' : 'Donation marked as unverified.');
    }

    public function destroy(Donation $donation): RedirectResponse
    {
        if ($donation->receipt_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($donation->receipt_path);
        }
        $donation->delete();

        return back()->with('status', 'Donation record deleted.');
    }
}
