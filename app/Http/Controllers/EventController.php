<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Contracts\View\View;

class EventController extends Controller
{
    public function index(): View
    {
        return view('pages.events.index', [
            'title' => 'Events',
            'description' => 'Alumni reunions, seminars and networking sessions from the Department of Mathematics, Rajshahi College.',
            'upcoming' => Event::published()->upcoming()->get(),
            'past' => Event::published()->past()->take(9)->get(),
        ]);
    }

    public function show(Event $event): View
    {
        abort_unless($event->is_published, 404);

        return view('pages.events.show', [
            'title' => $event->title,
            'description' => $event->excerpt,
            'event' => $event,
            'related' => Event::published()->upcoming()->whereKeyNot($event->id)->take(2)->get(),
        ]);
    }
}
