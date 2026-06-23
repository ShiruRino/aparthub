<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\Resident;
use App\Models\Visitor;
use App\Services\Visitors\ExpireVisitors;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VisitorManagementController extends Controller
{
    private const GUEST_LIMIT_KEY = 'visitor_guest_max';

    public function index(Request $request, ExpireVisitors $expireVisitors): View
    {
        return $this->page($request, $expireVisitors, 'registration');
    }

    public function registration(Request $request, ExpireVisitors $expireVisitors): View
    {
        return $this->page($request, $expireVisitors, 'registration');
    }

    public function pendingApproval(Request $request, ExpireVisitors $expireVisitors): View
    {
        return $this->page($request, $expireVisitors, 'pending-approval');
    }

    public function expectedVisitors(Request $request, ExpireVisitors $expireVisitors): View
    {
        return $this->page($request, $expireVisitors, 'expected-visitors');
    }

    public function checkInOut(Request $request, ExpireVisitors $expireVisitors): View
    {
        return $this->page($request, $expireVisitors, 'check-in-out');
    }

    public function history(Request $request, ExpireVisitors $expireVisitors): View
    {
        return $this->page($request, $expireVisitors, 'history');
    }

    public function blacklist(Request $request, ExpireVisitors $expireVisitors): View
    {
        return $this->page($request, $expireVisitors, 'blacklist');
    }

    public function storeWalkIn(Request $request): RedirectResponse
    {
        $validated = $this->validateAdminPayload($request, true);

        $visitor = DB::transaction(function () use ($request, $validated) {
            return Visitor::query()->create([
                'resident_id' => $validated['resident_id'],
                'visitor_name' => $validated['visitor_name'],
                'visitor_phone' => $validated['visitor_phone'],
                'visit_date' => $validated['visit_date'],
                'estimated_arrival_time' => $validated['estimated_arrival_time'],
                'guest_count' => $validated['guest_count'],
                'visit_purpose' => $validated['visit_purpose'],
                'identity_photo_path' => $request->file('identity_photo')?->store('visitors/identity', 'local'),
                'status' => Visitor::STATUS_APPROVED,
                'registration_source' => Visitor::SOURCE_ADMIN_WALK_IN,
                'access_code' => $this->generateAccessCode(),
                'approved_at' => now(),
                'expires_at' => Carbon::parse($validated['visit_date'].' 23:59:59', config('app.timezone')),
            ]);
        });

        return redirect()
            ->route('visitor-management.registration', ['visitor' => $visitor->id])
            ->with('status', 'Walk-in visitor berhasil diregistrasi.');
    }

    public function approve(Request $request, Visitor $visitor, ExpireVisitors $expireVisitors): RedirectResponse
    {
        $expireVisitors->applyToVisitor($visitor);

        if (! $visitor->canAdminApprove()) {
            throw ValidationException::withMessages([
                'visitor' => 'Visitor tidak dapat di-approve pada status saat ini.',
            ]);
        }

        $visitor->update([
            'status' => Visitor::STATUS_APPROVED,
            'approved_at' => now(),
            'rejected_at' => null,
            'rejection_reason' => null,
        ]);

        return redirect()
            ->back()
            ->with('status', 'Visitor berhasil di-approve.');
    }

    public function reject(Request $request, Visitor $visitor, ExpireVisitors $expireVisitors): RedirectResponse
    {
        $expireVisitors->applyToVisitor($visitor);

        if (! $visitor->canAdminReject()) {
            throw ValidationException::withMessages([
                'visitor' => 'Visitor tidak dapat di-reject pada status saat ini.',
            ]);
        }

        $validated = $request->validate([
            'rejection_reason' => ['nullable', 'string', 'max:255'],
        ]);

        $visitor->update([
            'status' => Visitor::STATUS_REJECTED,
            'rejected_at' => now(),
            'rejection_reason' => $validated['rejection_reason'] ?? null,
        ]);

        return redirect()
            ->back()
            ->with('status', 'Visitor berhasil ditolak.');
    }

    public function lookupByCode(Request $request, ExpireVisitors $expireVisitors): RedirectResponse
    {
        $validated = $request->validate([
            'access_code' => ['required', 'string'],
        ]);

        $visitor = Visitor::query()->where('access_code', $validated['access_code'])->first();

        if (! $visitor) {
            throw ValidationException::withMessages([
                'access_code' => 'Kode akses visitor tidak ditemukan.',
            ]);
        }

        $expireVisitors->applyToVisitor($visitor);

        return redirect()
            ->route('visitor-management.check-in-out', ['visitor' => $visitor->id])
            ->with('status', 'Visitor ditemukan melalui kode akses.');
    }

    public function checkIn(Request $request, Visitor $visitor, ExpireVisitors $expireVisitors): RedirectResponse
    {
        $validated = $request->validate([
            'access_code' => ['required', 'string'],
            'access_card_number' => ['nullable', 'string', 'max:255'],
        ]);

        $expireVisitors->applyToVisitor($visitor);

        if ($validated['access_code'] !== $visitor->access_code || ! $visitor->canAdminCheckIn()) {
            throw ValidationException::withMessages([
                'access_code' => 'Visitor tidak valid untuk check-in.',
            ]);
        }

        $visitor->update([
            'status' => Visitor::STATUS_CHECKED_IN,
            'checked_in_at' => now(),
            'access_card_number' => $validated['access_card_number'] ?? null,
        ]);

        return redirect()
            ->route('visitor-management.check-in-out', ['visitor' => $visitor->id])
            ->with('status', 'Visitor berhasil check-in.');
    }

    public function checkOut(Visitor $visitor, ExpireVisitors $expireVisitors): RedirectResponse
    {
        $expireVisitors->applyToVisitor($visitor);

        if (! $visitor->canAdminCheckOut()) {
            throw ValidationException::withMessages([
                'visitor' => 'Visitor tidak dapat check-out pada status saat ini.',
            ]);
        }

        $visitor->update([
            'status' => Visitor::STATUS_CHECKED_OUT,
            'checked_out_at' => now(),
        ]);

        return redirect()
            ->route('visitor-management.history', ['visitor' => $visitor->id])
            ->with('status', 'Visitor berhasil check-out.');
    }

    public function identityPhoto(Visitor $visitor): StreamedResponse
    {
        abort_if(! $visitor->identity_photo_path || ! Storage::disk('local')->exists($visitor->identity_photo_path), 404);

        return Storage::disk('local')->response($visitor->identity_photo_path);
    }

    private function page(Request $request, ExpireVisitors $expireVisitors, string $page): View
    {
        $expireVisitors->run();

        $blacklistRows = $this->blacklistRows();
        $summary = $this->summary();
        $pageConfig = $this->pageConfig($page, $summary);
        $baseQuery = Visitor::query()
            ->with(['resident.unit'])
            ->search($request->string('search')->toString())
            ->when($request->filled('visit_date'), fn (Builder $query) => $query->whereDate('visit_date', $request->date('visit_date')))
            ->when($request->filled('registration_source'), fn (Builder $query) => $query->where('registration_source', $request->string('registration_source')->toString()))
            ->when($request->filled('resident_id'), fn (Builder $query) => $query->where('resident_id', $request->integer('resident_id')))
            ->when($request->filled('status') && $page === 'registration', fn (Builder $query) => $query->where('status', $request->string('status')->toString()));

        $records = match ($page) {
            'pending-approval' => (clone $baseQuery)->where('status', Visitor::STATUS_PENDING)->latest('visit_date')->latest()->paginate(10)->withQueryString(),
            'expected-visitors' => (clone $baseQuery)->where('status', Visitor::STATUS_APPROVED)->orderBy('visit_date')->orderBy('estimated_arrival_time')->paginate(10)->withQueryString(),
            'history' => (clone $baseQuery)->whereIn('status', [Visitor::STATUS_REJECTED, Visitor::STATUS_CANCELLED, Visitor::STATUS_EXPIRED, Visitor::STATUS_CHECKED_OUT])->latest('visit_date')->latest()->paginate(10)->withQueryString(),
            'blacklist' => null,
            default => (clone $baseQuery)->latest('visit_date')->latest()->paginate(10)->withQueryString(),
        };

        $checkInQueue = $page === 'check-in-out'
            ? (clone $baseQuery)->where('status', Visitor::STATUS_APPROVED)->orderBy('visit_date')->orderBy('estimated_arrival_time')->paginate(10, ['*'], 'check_in_page')->withQueryString()
            : null;
        $checkOutQueue = $page === 'check-in-out'
            ? (clone $baseQuery)->where('status', Visitor::STATUS_CHECKED_IN)->latest('checked_in_at')->paginate(10, ['*'], 'check_out_page')->withQueryString()
            : null;

        $selectedVisitor = null;
        $checkMode = $request->string('mode')->toString() === 'check-out' ? 'check-out' : 'check-in';

        if ($request->filled('visitor')) {
            $selectedVisitor = Visitor::query()->with('resident.unit')->find($request->integer('visitor'));
        }

        return view('visitor-management.index', [
            'pageKey' => $page,
            'page' => $pageConfig,
            'filters' => $request->only(['search', 'visit_date', 'registration_source', 'resident_id', 'status']),
            'records' => $records,
            'rows' => $records ? $this->rowsForPage($records->getCollection()->all(), $page) : [],
            'checkInQueue' => $checkInQueue,
            'checkInRows' => $checkInQueue ? $this->rowsForPage($checkInQueue->getCollection()->all(), 'check-in') : [],
            'checkOutQueue' => $checkOutQueue,
            'checkOutRows' => $checkOutQueue ? $this->rowsForPage($checkOutQueue->getCollection()->all(), 'check-out') : [],
            'selectedVisitor' => $selectedVisitor,
            'residentOptions' => Resident::query()->with('unit')->orderBy('name')->get(),
            'registrationSources' => [Visitor::SOURCE_RESIDENT_APP, Visitor::SOURCE_ADMIN_WALK_IN],
            'statusOptions' => Visitor::statuses(),
            'guestLimit' => AppSetting::getInteger(self::GUEST_LIMIT_KEY) ?? 10,
            'summary' => $summary,
            'blacklistRows' => $blacklistRows,
            'checkMode' => $checkMode,
        ]);
    }

    /**
     * @param  array<int, Visitor>  $visitors
     * @return array<int, array<string, mixed>>
     */
    private function rowsForPage(array $visitors, string $page): array
    {
        return collect($visitors)->map(function (Visitor $visitor) use ($page) {
            $actions = [['View', 'info', route($page === 'check-out' ? 'visitor-management.check-in-out' : $this->routeForPage($page), ['visitor' => $visitor->id])]];

            if ($page === 'pending-approval' && $visitor->canAdminApprove()) {
                $actions[] = ['Approve', 'success', route('visitor-management.pending-approval', ['visitor' => $visitor->id])];
                $actions[] = ['Reject', 'danger', route('visitor-management.pending-approval', ['visitor' => $visitor->id])];
            }

            if (in_array($page, ['registration', 'expected-visitors', 'check-in'], true) && $visitor->canAdminCheckIn()) {
                $actions[] = ['Check-In', 'success', route('visitor-management.check-in-out', ['visitor' => $visitor->id])];
            }

            if ($page === 'check-out' && $visitor->canAdminCheckOut()) {
                $actions[] = ['Check-Out', 'danger', route('visitor-management.check-in-out', ['visitor' => $visitor->id])];
            }

            return [
                'id' => $visitor->id,
                'no' => $visitor->id,
                'name' => $visitor->visitor_name,
                'unit' => $visitor->resident?->unit?->code ? 'Unit '.$visitor->resident->unit->code : '-',
                'resident' => $visitor->resident?->name,
                'date' => $visitor->visit_date?->format('d M Y').' - '.$visitor->estimated_arrival_time?->format('H:i'),
                'purpose' => $visitor->visit_purpose,
                'vehicle' => '-',
                'plate' => '-',
                'type' => '-',
                'lot' => '-',
                'contact' => $visitor->visitor_phone,
                'reason' => $visitor->rejection_reason ?: $visitor->cancellation_reason ?: '-',
                'blocked' => optional($visitor->created_at)->format('d M Y'),
                'blockedBy' => $visitor->status === Visitor::STATUS_REJECTED ? 'Front Office' : 'System',
                'expiry' => $visitor->expires_at?->format('d M Y H:i') ?? '-',
                'checkout' => $visitor->checked_out_at?->format('d M Y - H:i') ?? '-',
                'status' => $visitor->status,
                'statusClass' => $this->statusClass($visitor->status),
                'actions' => $actions,
            ];
        })->all();
    }

    /**
     * @return array<string, mixed>
     */
    private function pageConfig(string $page, array $summary): array
    {
        return [
            'registration' => [
                'label' => 'Visitor Registration',
                'title' => 'Visitor Registration',
                'subtitle' => 'Input data visitor atau walk-in visitor oleh front office / security.',
                'stats' => [
                    'Today Registrations: '.$summary['today_registrations'],
                    'Pending Approvals: '.$summary['pending'],
                    'Visitors Inside: '.$summary['inside'],
                ],
                'tableTitle' => 'All Visitor Registration',
            ],
            'pending-approval' => [
                'label' => 'Pending Approval',
                'title' => 'Pending Approval',
                'subtitle' => 'Kelola approval visitor yang diajukan resident.',
                'stats' => ['Total Pending Requests: '.$summary['pending']],
                'tableTitle' => 'Pending Visitor Approval Queue',
            ],
            'expected-visitors' => [
                'label' => 'Expected Visitors',
                'title' => 'Expected Visitors',
                'subtitle' => 'Daftar visitor approved yang menunggu kedatangan.',
                'stats' => ['Total Expected Visitors: '.$summary['approved']],
                'tableTitle' => 'Expected Visitors Queue',
            ],
            'check-in-out' => [
                'label' => 'Check-In / Check-Out',
                'title' => 'Visitor Check-In / Check-Out',
                'subtitle' => 'Validasi kode akses dan proses kedatangan / kepulangan visitor.',
                'stats' => [
                    'Total Expected Today: '.$summary['approved_today'],
                    'Total Visitors Currently Inside: '.$summary['inside'],
                ],
            ],
            'history' => [
                'label' => 'Visitor History',
                'title' => 'Visitor History Log',
                'subtitle' => 'Riwayat visitor yang selesai, dibatalkan, ditolak, atau expired.',
                'stats' => ['Total History Records: '.$summary['history']],
                'tableTitle' => 'Visitor History Log',
            ],
            'blacklist' => [
                'label' => 'Blacklist Management',
                'title' => 'Visitor Blacklist Management',
                'subtitle' => 'Static preview blacklist management.',
                'stats' => ['Total Visitors Currently Blacklisted: 12 / 50'],
                'tableTitle' => 'Visitor Blacklist Management',
            ],
        ][$page];
    }

    /**
     * @return array<string, int>
     */
    private function summary(): array
    {
        return [
            'today_registrations' => Visitor::query()->whereDate('created_at', today())->count(),
            'pending' => Visitor::query()->where('status', Visitor::STATUS_PENDING)->count(),
            'approved' => Visitor::query()->where('status', Visitor::STATUS_APPROVED)->count(),
            'approved_today' => Visitor::query()->where('status', Visitor::STATUS_APPROVED)->whereDate('visit_date', today())->count(),
            'inside' => Visitor::query()->where('status', Visitor::STATUS_CHECKED_IN)->count(),
            'history' => Visitor::query()->whereIn('status', [Visitor::STATUS_REJECTED, Visitor::STATUS_CANCELLED, Visitor::STATUS_EXPIRED, Visitor::STATUS_CHECKED_OUT])->count(),
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function blacklistRows(): array
    {
        return [
            ['no' => 1, 'name' => 'Mike Thompson', 'contact' => '08111222333', 'reason' => 'Unauthorized access', 'blocked' => '01 Mar 2026', 'blockedBy' => 'Security Chief', 'expiry' => 'Indefinite', 'status' => 'Active', 'statusClass' => 'status-approved', 'actions' => [['Review Record', 'info', route('visitor-management.blacklist')]]],
            ['no' => 2, 'name' => 'Jane Fisher', 'contact' => 'jane.f@email.com', 'reason' => 'Property Damage', 'blocked' => '15 Apr 2026', 'blockedBy' => 'Ops Manager', 'expiry' => '15 Apr 2027', 'status' => 'Active', 'statusClass' => 'status-approved', 'actions' => [['Review Record', 'info', route('visitor-management.blacklist')]]],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function validateAdminPayload(Request $request, bool $walkIn = false): array
    {
        $maxGuests = AppSetting::getInteger(self::GUEST_LIMIT_KEY) ?? 10;

        return $request->validate([
            'resident_id' => ['required', 'exists:residents,id'],
            'visitor_name' => ['required', 'string', 'max:255'],
            'visitor_phone' => ['required', 'string', 'max:50'],
            'visit_date' => ['required', 'date', 'after_or_equal:today'],
            'estimated_arrival_time' => ['required', 'date_format:H:i'],
            'guest_count' => ['required', 'integer', 'min:1', 'max:'.$maxGuests],
            'visit_purpose' => ['required', 'string', 'max:255'],
            'identity_photo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], [
            'guest_count.max' => "Jumlah tamu maksimal {$maxGuests} orang.",
        ]);
    }

    private function routeForPage(string $page): string
    {
        return match ($page) {
            'registration' => 'visitor-management.registration',
            'pending-approval' => 'visitor-management.pending-approval',
            'expected-visitors' => 'visitor-management.expected-visitors',
            'check-in', 'check-out', 'check-in-out' => 'visitor-management.check-in-out',
            'history' => 'visitor-management.history',
            default => 'visitor-management.index',
        };
    }

    private function statusClass(string $status): string
    {
        return match ($status) {
            Visitor::STATUS_PENDING => 'status-pending',
            Visitor::STATUS_APPROVED, Visitor::STATUS_CHECKED_IN, Visitor::STATUS_CHECKED_OUT => 'status-approved',
            Visitor::STATUS_REJECTED, Visitor::STATUS_CANCELLED => 'status-rejected',
            Visitor::STATUS_EXPIRED => 'status-expired',
            default => 'status-blue',
        };
    }

    private function generateAccessCode(): string
    {
        do {
            $code = Str::upper(Str::random(48));
        } while (Visitor::query()->where('access_code', $code)->exists());

        return $code;
    }
}
