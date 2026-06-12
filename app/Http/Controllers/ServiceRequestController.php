<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class ServiceRequestController extends Controller
{
    public function index(): View
    {
        return $this->page('ticket-queue');
    }

    public function ticketQueue(): View
    {
        return $this->page('ticket-queue');
    }

    public function newRequest(): View
    {
        return $this->page('new-request');
    }

    public function assignmentBoard(): View
    {
        return $this->page('assignment-board');
    }

    public function workOrders(): View
    {
        return $this->page('work-orders');
    }

    public function technicianSchedule(): View
    {
        return $this->page('technician-schedule');
    }

    public function workInProgress(): View
    {
        return $this->page('work-in-progress');
    }

    public function completedRequests(): View
    {
        return $this->page('completed-requests');
    }

    public function slaMonitoring(): View
    {
        return $this->page('sla-monitoring');
    }

    public function serviceHistory(): View
    {
        return $this->page('service-history');
    }

    public function settings(): View
    {
        return $this->page('settings');
    }

    private function page(string $page): View
    {
        return view('service-request.index', [
            'pageKey' => $page,
        ]);
    }
}
