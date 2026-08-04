<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.messages', [
            'title' => 'Messages',
            'messages' => ContactMessage::query()
                ->when($request->string('filter')->toString() === 'unread', fn ($q) => $q->where('is_read', false))
                ->latest()
                ->paginate(20)
                ->withQueryString(),
            'unread' => ContactMessage::where('is_read', false)->count(),
            'filter' => $request->string('filter')->toString(),
        ]);
    }

    public function update(Request $request, ContactMessage $message): RedirectResponse
    {
        $message->update(['is_read' => $request->boolean('is_read', true)]);

        return back();
    }

    public function destroy(ContactMessage $message): RedirectResponse
    {
        $message->delete();

        return back()->with('status', 'Message deleted.');
    }
}
