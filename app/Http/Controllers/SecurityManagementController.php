<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class SecurityManagementController extends Controller
{
    public function index(): View
    {
        return $this->page('task-assignment');
    }

    public function liveMonitoring(): View
    {
        return $this->page('live-monitoring');
    }

    public function patrolMonitoring(): View
    {
        return $this->page('patrol-monitoring');
    }

    public function taskAssignment(): View
    {
        return $this->page('task-assignment');
    }

    public function officers(): View
    {
        return $this->page('officers');
    }

    public function schedule(): View
    {
        return $this->page('schedule');
    }

    public function incidents(): View
    {
        return $this->page('incidents');
    }

    public function devices(): View
    {
        return $this->page('devices');
    }

    public function reports(): View
    {
        return $this->page('reports');
    }

    public function settings(): View
    {
        return $this->page('settings');
    }

    private function page(string $page): View
    {
        return view('security-management.index', [
            'pageKey' => $page,
        ]);
    }
}
