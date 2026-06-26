<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Facility;
use App\Models\FacilityBooking;
use App\Models\Resident;
use App\Models\ResidentMoveRequest;
use App\Models\ServiceRequest;
use App\Models\ServiceRequestEvent;
use App\Models\TechnicianTeam;
use App\Models\Unit;
use App\Models\Visitor;
use Illuminate\Support\Collection;
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

        $residentSummary = [
            'total' => Resident::query()->count(),
            'active' => Resident::query()->where('status', 'Aktif')->count(),
            'pending' => Resident::query()->where('status', 'Menunggu Approval')->count(),
            'move_in_this_month' => ResidentMoveRequest::query()
                ->where('request_type', 'Pindah Masuk')
                ->whereMonth('scheduled_date', now()->month)
                ->whereYear('scheduled_date', now()->year)
                ->count(),
            'move_out_this_month' => ResidentMoveRequest::query()
                ->where('request_type', 'Pindah Keluar')
                ->whereMonth('scheduled_date', now()->month)
                ->whereYear('scheduled_date', now()->year)
                ->count(),
        ];

        $unitSummary = [
            'total' => Unit::query()->count(),
            'occupied' => Unit::query()->where('occupancy_status', 'Terisi')->count(),
            'vacant' => Unit::query()->where('occupancy_status', 'Kosong')->count(),
            'maintenance' => Unit::query()->where('occupancy_status', 'Perbaikan')->count(),
        ];

        $visitorSummary = [
            'today' => Visitor::query()->whereDate('visit_date', today())->count(),
            'checked_in' => Visitor::query()->where('status', Visitor::STATUS_CHECKED_IN)->count(),
            'pending' => Visitor::query()->where('status', Visitor::STATUS_PENDING)->count(),
            'expected_today' => Visitor::query()
                ->where('status', Visitor::STATUS_APPROVED)
                ->whereDate('visit_date', today())
                ->count(),
        ];

        $facilitySummary = [
            'total' => Facility::query()->count(),
            'available' => Facility::query()->where('status', 'Available')->count(),
            'booked' => Facility::query()->where('status', 'Booked')->count(),
            'maintenance' => Facility::query()->where('status', 'Maintenance')->count(),
            'active_bookings' => FacilityBooking::query()
                ->whereIn('status', ['Pending', 'Confirmed'])
                ->count(),
        ];

        $announcementSummary = [
            'published' => Announcement::query()->published()->count(),
            'pinned' => Announcement::query()->published()->where('is_pinned', true)->count(),
        ];

        $technicianSummary = [
            'teams' => TechnicianTeam::query()->count(),
        ];

        $facilityLoad = Facility::query()
            ->withCount([
                'bookings as total_bookings',
                'bookings as active_bookings' => fn ($query) => $query->whereIn('status', ['Pending', 'Confirmed']),
            ])
            ->orderByDesc('active_bookings')
            ->orderByDesc('total_bookings')
            ->take(4)
            ->get();

        $latestAlerts = $this->latestAlerts();
        $recentActivities = $this->recentActivities();

        return view('dashboard', compact(
            'serviceSummary',
            'residentSummary',
            'unitSummary',
            'visitorSummary',
            'facilitySummary',
            'announcementSummary',
            'technicianSummary',
            'facilityLoad',
            'latestAlerts',
            'recentActivities',
        ));
    }

    /**
     * Build alert items from trustworthy urgent sources only.
     *
     * @return Collection<int, array<string, mixed>>
     */
    private function latestAlerts(): Collection
    {
        $serviceAlerts = ServiceRequest::query()
            ->with(['resident.unit'])
            ->where(function ($query) {
                $query->where('priority', ServiceRequest::PRIORITY_EMERGENCY)
                    ->orWhere(function ($serviceQuery) {
                        $serviceQuery->whereNotIn('status', [
                            ServiceRequest::STATUS_COMPLETED,
                            ServiceRequest::STATUS_CANCELLED,
                        ])->whereNotNull('sla_due_at')
                            ->where('sla_due_at', '<', now());
                    });
            })
            ->latest()
            ->take(4)
            ->get()
            ->map(function (ServiceRequest $ticket) {
                return [
                    'tone' => $ticket->priority === ServiceRequest::PRIORITY_EMERGENCY ? 'red' : 'gold',
                    'title' => $ticket->priority === ServiceRequest::PRIORITY_EMERGENCY ? 'Emergency Ticket' : 'Over SLA',
                    'description' => $ticket->ticket_number.' · '.$ticket->title,
                    'time' => $ticket->updated_at?->diffForHumans(),
                    'url' => auth()->user()?->canAccessModule('service-request', 'read')
                        ? route('service-request.ticket-queue')
                        : null,
                    'timestamp' => $ticket->updated_at,
                ];
            });

        $announcementAlerts = Announcement::query()
            ->published()
            ->orderByDesc('is_pinned')
            ->orderByDesc('published_at')
            ->take(4)
            ->get()
            ->map(function (Announcement $announcement) {
                return [
                    'tone' => $announcement->is_pinned ? 'gold' : 'blue',
                    'title' => $announcement->category ?: 'Announcement',
                    'description' => $announcement->title,
                    'time' => $announcement->published_at?->diffForHumans(),
                    'url' => auth()->user()?->canAccessModule('community-management', 'read')
                        ? route('community-management.announcements')
                        : null,
                    'timestamp' => $announcement->published_at ?? $announcement->updated_at,
                ];
            });

        return $serviceAlerts
            ->concat($announcementAlerts)
            ->sortByDesc(fn (array $item) => $item['timestamp']?->timestamp ?? 0)
            ->take(4)
            ->values();
    }

    /**
     * Build a unified recent activity feed from real timestamped records.
     *
     * @return Collection<int, array<string, mixed>>
     */
    private function recentActivities(): Collection
    {
        $serviceActivities = ServiceRequestEvent::query()
            ->with(['serviceRequest', 'actor'])
            ->latest()
            ->take(6)
            ->get()
            ->map(function (ServiceRequestEvent $event) {
                $actor = $event->actor?->name ?? 'Service Desk';
                $ticket = $event->serviceRequest;

                return [
                    'actor' => $actor,
                    'title' => $ticket?->ticket_number ?? 'Service Request',
                    'description' => trim(($event->event_type ?? 'Updated').' '.($ticket?->title ? '· '.$ticket->title : '')),
                    'time' => $event->created_at,
                    'url' => auth()->user()?->canAccessModule('service-request', 'read')
                        ? route('service-request.ticket-queue')
                        : null,
                ];
            });

        $visitorActivities = Visitor::query()
            ->with(['resident.unit'])
            ->where(function ($query) {
                $query->whereNotNull('approved_at')
                    ->orWhereNotNull('rejected_at')
                    ->orWhereNotNull('cancelled_at')
                    ->orWhereNotNull('checked_in_at')
                    ->orWhereNotNull('checked_out_at')
                    ->orWhere('status', Visitor::STATUS_EXPIRED);
            })
            ->latest('updated_at')
            ->take(6)
            ->get()
            ->map(function (Visitor $visitor) {
                return [
                    'actor' => 'Visitor Management',
                    'title' => $visitor->visitor_name,
                    'description' => $visitor->status.' · '.($visitor->resident?->unit?->code ? 'Unit '.$visitor->resident->unit->code : 'No unit'),
                    'time' => $visitor->checked_out_at
                        ?? $visitor->checked_in_at
                        ?? $visitor->approved_at
                        ?? $visitor->rejected_at
                        ?? $visitor->cancelled_at
                        ?? $visitor->updated_at,
                    'url' => auth()->user()?->canAccessModule('visitor-management', 'read')
                        ? route('visitor-management.registration', ['visitor' => $visitor->id])
                        : null,
                ];
            });

        $facilityActivities = FacilityBooking::query()
            ->with(['facility', 'resident.unit'])
            ->latest('updated_at')
            ->take(6)
            ->get()
            ->map(function (FacilityBooking $booking) {
                return [
                    'actor' => 'Facility Booking',
                    'title' => $booking->booking_title,
                    'description' => $booking->status.' · '.($booking->facility?->name ?? 'Facility'),
                    'time' => $booking->updated_at ?? $booking->created_at,
                    'url' => auth()->user()?->canAccessModule('facility-management', 'read')
                        ? route('facility-management.index')
                        : null,
                ];
            });

        $announcementActivities = Announcement::query()
            ->published()
            ->latest('published_at')
            ->take(6)
            ->get()
            ->map(function (Announcement $announcement) {
                return [
                    'actor' => 'Announcement Center',
                    'title' => $announcement->title,
                    'description' => 'Published · '.($announcement->category ?: 'General'),
                    'time' => $announcement->published_at ?? $announcement->updated_at,
                    'url' => auth()->user()?->canAccessModule('community-management', 'read')
                        ? route('community-management.announcements')
                        : null,
                ];
            });

        $moveActivities = ResidentMoveRequest::query()
            ->with(['resident', 'unit'])
            ->latest('updated_at')
            ->take(6)
            ->get()
            ->map(function (ResidentMoveRequest $move) {
                return [
                    'actor' => 'Resident Movement',
                    'title' => $move->request_number,
                    'description' => $move->request_type.' · '.($move->resident?->name ?? 'Resident'),
                    'time' => $move->updated_at ?? $move->created_at,
                    'url' => auth()->user()?->canAccessModule('resident-management', 'read')
                        ? route('resident-management.move-in-out')
                        : null,
                ];
            });

        return $serviceActivities
            ->concat($visitorActivities)
            ->concat($facilityActivities)
            ->concat($announcementActivities)
            ->concat($moveActivities)
            ->sortByDesc(fn (array $item) => $item['time']?->timestamp ?? 0)
            ->take(10)
            ->values();
    }
}
