<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\Teacher;
use Illuminate\Contracts\View\View;

class PageController extends Controller
{
    public function about(): View
    {
        return view('pages.about', [
            'title' => 'About RCMAA',
            'description' => 'Founded with a legacy of academic excellence, RCMAA brings together decades of graduates under one global community.',
        ]);
    }

    public function heritage(): View
    {
        return view('pages.heritage', [
            'title' => 'Our Heritage',
            'description' => 'Historical milestones of Rajshahi College, from its founding in 1873 to the formation of RCMAA in 2026.',
        ]);
    }

    public function ourGoal(): View
    {
        return view('pages.our-goal', [
            'title' => 'Our Goal',
            'description' => 'What the Rajshahi College Mathematics Alumni Association sets out to achieve.',
        ]);
    }

    public function teachers(): View
    {
        return view('pages.teachers', [
            'title' => 'Faculty',
            'description' => 'The teaching body of the Department of Mathematics, Rajshahi College.',
            'head' => Teacher::published()->where('is_head', true)->first(),
            'teachers' => Teacher::published()->where('is_head', false)->ordered()->get(),
        ]);
    }

    public function howToApply(): View
    {
        return view('pages.how-to-apply', [
            'title' => 'How to Apply',
            'description' => 'Step-by-step guidance for joining RCMAA and registering for the Grand Reunion 2026.',
        ]);
    }

    public function features(): View
    {
        return view('pages.features', [
            'title' => 'Features',
            'description' => 'What membership of the Rajshahi College Mathematics Alumni Association offers.',
        ]);
    }

    public function helpCenter(): View
    {
        return view('pages.help-center', [
            'title' => 'Help Center',
            'description' => 'Helpdesk contacts and answers to the questions we are asked most.',
            'faqs' => Faq::published()->whereIn('category', ['registration', 'payment'])->orderBy('sort_order')->get(),
        ]);
    }

    public function privacy(): View
    {
        return view('pages.privacy', ['title' => 'Privacy Policy']);
    }

    public function terms(): View
    {
        return view('pages.terms', ['title' => 'Terms of Service']);
    }
}
