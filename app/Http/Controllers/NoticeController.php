<?php

namespace App\Http\Controllers;

use App\Models\Notice;
use Illuminate\Contracts\View\View;

class NoticeController extends Controller
{
    public function index(): View
    {
        return view('pages.notices.index', [
            'title' => 'Notice Board',
            'description' => 'Official announcements from the Rajshahi College Mathematics Alumni Association.',
            'notices' => Notice::published()->latestFirst()->paginate(10),
        ]);
    }

    public function show(Notice $notice): View
    {
        abort_unless($notice->is_published, 404);

        return view('pages.notices.show', [
            'title' => $notice->title,
            'description' => $notice->excerpt,
            'notice' => $notice,
            'recent' => Notice::published()->latestFirst()->whereKeyNot($notice->id)->take(4)->get(),
        ]);
    }
}
