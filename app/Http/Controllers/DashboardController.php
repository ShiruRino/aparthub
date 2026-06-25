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
            'submitted' => ServiceRequest::query()->canonicalStatus(ServiceRequest::STATUS_SUBMITTED)->count(),
            'open' => ServiceRequest::query()->whereNotIn('status', [ServiceRequest::STATUS_COMPLETED, ServiceRequest::STATUS_CANCELLED])->count(),
            'assigned' => ServiceRequest::query()->canonicalStatus(ServiceRequest::STATUS_ASSIGNED)->count(),
            'on_the_way' => ServiceRequest::query()->canonicalStatus(ServiceRequest::STATUS_ON_THE_WAY)->count(),
            'in_progress' => ServiceRequest::query()->canonicalStatus(ServiceRequest::STATUS_IN_PROGRESS)->count(),
            'resolved' => ServiceRequest::query()->canonicalStatus(ServiceRequest::STATUS_COMPLETED)->count(),
            'completed_today' => ServiceRequest::query()->whereDate('completed_at', today())->count(),
            'over_sla' => ServiceRequest::query()->overSla()->count(),
            'emergency' => ServiceRequest::query()->where('priority', ServiceRequest::PRIORITY_EMERGENCY)->count(),
        ];

        $recentServiceRequests = ServiceRequest::query()
            ->with(['resident.unit', 'subcategory', 'attachments'])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact('serviceSummary', 'recentServiceRequests'));
    }
}
