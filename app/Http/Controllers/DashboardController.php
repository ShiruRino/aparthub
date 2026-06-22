<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show the admin dashboard.
     */
    public function __invoke(): View
    {
        $serviceSummary = [
            'open' => ServiceRequest::query()->whereIn('status', ['New', 'Pending', 'Assigned', 'In Progress', 'Over SLA'])->count(),
            'assigned' => ServiceRequest::query()->where('status', 'Assigned')->count(),
            'in_progress' => ServiceRequest::query()->where('status', 'In Progress')->count(),
            'resolved' => ServiceRequest::query()->where('status', 'Completed')->count(),
            'over_sla' => ServiceRequest::query()->where('status', 'Over SLA')->count(),
            'emergency' => ServiceRequest::query()->where('priority', 'Emergency')->count(),
        ];

        $recentServiceRequests = ServiceRequest::query()
            ->with('resident.unit')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact('serviceSummary', 'recentServiceRequests'));
    }
}
