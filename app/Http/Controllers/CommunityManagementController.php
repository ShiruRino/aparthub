<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class CommunityManagementController extends Controller
{
    public function announcements(): View
    {
        return $this->page('announcements');
    }

    public function events(): View
    {
        return $this->page('events');
    }

    public function pollingSurvey(): View
    {
        return $this->page('polling-survey');
    }

    public function forum(): View
    {
        return $this->page('forum');
    }

    public function broadcasts(): View
    {
        return $this->page('broadcasts');
    }

    public function programs(): View
    {
        return $this->page('programs');
    }

    public function calendar(): View
    {
        return $this->page('calendar');
    }

    public function engagement(): View
    {
        return $this->page('engagement');
    }

    public function archive(): View
    {
        return $this->page('archive');
    }

    public function settings(): View
    {
        return $this->page('settings');
    }

    private function page(string $page): View
    {
        return view('community-management.index', [
            'pageKey' => $page,
        ]);
    }
}
