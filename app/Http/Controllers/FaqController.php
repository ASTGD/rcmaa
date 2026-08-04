<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use Illuminate\Contracts\View\View;

class FaqController extends Controller
{
    public function __invoke(): View
    {
        return view('pages.faqs', [
            'title' => 'Frequently Asked Questions',
            'description' => 'Answers about membership, reunion registration, payment and events.',
            'grouped' => Faq::published()->orderBy('sort_order')->get()->groupBy('category'),
        ]);
    }
}
