<?php

namespace App\Http\Controllers;

use App\Models\CommitteeMember;
use Illuminate\Contracts\View\View;

class CommitteeController extends Controller
{
    public function __invoke(?string $group = null): View
    {
        $groups = CommitteeMember::COMMITTEES;
        $active = array_key_exists((string) $group, $groups) ? $group : array_key_first($groups);

        return view('pages.committee', [
            'title' => $groups[$active]['en'],
            'description' => 'Meet the '.$groups[$active]['en'].' of the Rajshahi College Mathematics Alumni Association.',
            'groups' => $groups,
            'active' => $active,
            'members' => CommitteeMember::published()->ofCommittee($active)->ordered()->get(),
        ]);
    }
}
