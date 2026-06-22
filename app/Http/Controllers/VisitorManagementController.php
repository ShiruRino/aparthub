<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class VisitorManagementController extends Controller
{
    public function index(): View
    {
        return $this->page('registration');
    }

    /**
     * Show visitor registration.
     */
    public function registration(): View
    {
        return $this->page('registration');
    }

    /**
     * Show pending approval queue.
     */
    public function pendingApproval(): View
    {
        return $this->page('pending-approval');
    }

    /**
     * Show expected visitors queue.
     */
    public function expectedVisitors(): View
    {
        return $this->page('expected-visitors');
    }

    /**
     * Show combined check-in and check-out queue.
     */
    public function checkInOut(): View
    {
        return $this->page('check-in-out');
    }

    /**
     * Show visitor history.
     */
    public function history(): View
    {
        return $this->page('history');
    }

    /**
     * Show visitor blacklist management.
     */
    public function blacklist(): View
    {
        return $this->page('blacklist');
    }

    /**
     * Render a static visitor management page.
     */
    private function page(string $page): View
    {
        return view('visitor-management.index', [
            'pageKey' => $page,
        ]);
    }
}
