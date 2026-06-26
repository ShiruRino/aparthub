<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\Resident;
use App\Models\Visitor;
use App\Services\Visitors\ExpireVisitors;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ResidentVisitorController extends Controller
{
    private const GUEST_LIMIT_KEY = 'visitor_guest_max';

    public function index(Request $request, ExpireVisitors $expireVisitors): JsonResponse
    {
        /** @var Resident $resident */
        $resident = $request->user();

        $validated = $request->validate([
            'status' => ['nullable', Rule::in(Visitor::statuses())],
        ]);

        $expireVisitors->run();

        $visitors = Visitor::query()
            ->with('resident.unit')
            ->where('resident_id', $resident->id)
            ->when($validated['status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status))
            ->latest('visit_date')
            ->latest()
            ->paginate(10);

        return response()->json([
            'data' => $visitors->getCollection()->map(fn (Visitor $visitor) => $this->payload($visitor))->values(),
            'meta' => [
                'current_page' => $visitors->currentPage(),
                'last_page' => $visitors->lastPage(),
                'per_page' => $visitors->perPage(),
                'total' => $visitors->total(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        /** @var Resident $resident */
        $resident = $request->user();

        $validated = $this->validatePayload($request);

        $visitor = DB::transaction(function () use ($request, $resident, $validated) {
            return Visitor::query()->create([
                'resident_id' => $resident->id,
                'visitor_name' => $validated['visitor_name'],
                'visitor_phone' => $validated['visitor_phone'],
                'visit_date' => $validated['visit_date'],
                'estimated_arrival_time' => $validated['estimated_arrival_time'],
                'guest_count' => $validated['guest_count'],
                'visit_purpose' => $validated['visit_purpose'],
                'identity_photo_path' => $request->file('identity_photo')?->store('visitors/identity', 'local'),
                'status' => Visitor::STATUS_PENDING,
                'registration_source' => Visitor::SOURCE_RESIDENT_APP,
                'access_code' => $this->generateAccessCode(),
                'expires_at' => Carbon::parse($validated['visit_date'].' 23:59:59', config('app.timezone')),
            ]);
        });

        $visitor->load('resident.unit');

        return response()->json([
            'message' => 'Registrasi visitor berhasil dibuat.',
            'data' => $this->payload($visitor, true),
        ], 201);
    }

    public function show(Request $request, Visitor $visitor, ExpireVisitors $expireVisitors): JsonResponse
    {
        /** @var Resident $resident */
        $resident = $request->user();

        abort_if($visitor->resident_id !== $resident->id, 404);

        $expireVisitors->applyToVisitor($visitor);
        $visitor->loadMissing('resident.unit');

        return response()->json([
            'data' => $this->payload($visitor, true),
        ]);
    }

    public function update(Request $request, Visitor $visitor, ExpireVisitors $expireVisitors): JsonResponse
    {
        /** @var Resident $resident */
        $resident = $request->user();

        abort_if($visitor->resident_id !== $resident->id, 404);

        $expireVisitors->applyToVisitor($visitor);

        if (! $visitor->canResidentEdit()) {
            throw ValidationException::withMessages([
                'visitor' => 'Visitor hanya dapat diubah saat status masih Pending.',
            ]);
        }

        $validated = $this->validatePayload($request, true);

        DB::transaction(function () use ($request, $validated, $visitor) {
            $attributes = [
                'visitor_name' => $validated['visitor_name'],
                'visitor_phone' => $validated['visitor_phone'],
                'visit_date' => $validated['visit_date'],
                'estimated_arrival_time' => $validated['estimated_arrival_time'],
                'guest_count' => $validated['guest_count'],
                'visit_purpose' => $validated['visit_purpose'],
                'expires_at' => Carbon::parse($validated['visit_date'].' 23:59:59', config('app.timezone')),
            ];

            if ($request->hasFile('identity_photo')) {
                if ($visitor->identity_photo_path) {
                    Storage::disk('local')->delete($visitor->identity_photo_path);
                }

                $attributes['identity_photo_path'] = $request->file('identity_photo')->store('visitors/identity', 'local');
            }

            $visitor->update($attributes);
        });

        $visitor->refresh()->load('resident.unit');

        return response()->json([
            'message' => 'Registrasi visitor berhasil diperbarui.',
            'data' => $this->payload($visitor, true),
        ]);
    }

    public function cancel(Request $request, Visitor $visitor, ExpireVisitors $expireVisitors): JsonResponse
    {
        /** @var Resident $resident */
        $resident = $request->user();

        abort_if($visitor->resident_id !== $resident->id, 404);

        $expireVisitors->applyToVisitor($visitor);

        if (! $visitor->canResidentCancel()) {
            throw ValidationException::withMessages([
                'visitor' => 'Visitor tidak dapat dibatalkan pada status saat ini.',
            ]);
        }

        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $visitor->update([
            'status' => Visitor::STATUS_CANCELLED,
            'cancelled_at' => now(),
            'cancellation_reason' => $validated['reason'] ?? null,
        ]);

        $visitor->refresh()->load('resident.unit');

        return response()->json([
            'message' => 'Registrasi visitor berhasil dibatalkan.',
            'data' => $this->payload($visitor, true),
        ]);
    }

    public function qr(Request $request, Visitor $visitor, ExpireVisitors $expireVisitors): JsonResponse
    {
        /** @var Resident $resident */
        $resident = $request->user();

        abort_if($visitor->resident_id !== $resident->id, 404);

        $expireVisitors->applyToVisitor($visitor);

        if (!$visitor->qrAvailable()) {
            return response()->json([
                'message' => 'QR visitor belum tersedia atau sudah tidak valid.',
            ], 422);
        }

        return response()->json([
            'data' => [
                'visitor_id' => $visitor->id,
                'qr_payload' => $visitor->access_code,
                'access_code' => $visitor->access_code,
                'valid_until' => $visitor->expires_at?->toIso8601String(),
                'status' => $visitor->status,
            ],
        ]);
    }

    public function identityPhoto(Request $request, Visitor $visitor): StreamedResponse
    {
        /** @var Resident $resident */
        $resident = $request->user();

        abort_if($visitor->resident_id !== $resident->id, 404);
        abort_if(! $visitor->identity_photo_path || ! Storage::disk('local')->exists($visitor->identity_photo_path), 404);

        return Storage::disk('local')->response($visitor->identity_photo_path);
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(Visitor $visitor, bool $includeTimeline = false): array
    {
        $payload = [
            'id' => $visitor->id,
            'visitor_name' => $visitor->visitor_name,
            'visitor_phone' => $visitor->visitor_phone,
            'visit_date' => $visitor->visit_date?->toDateString(),
            'estimated_arrival_time' => $visitor->estimated_arrival_time?->format('H:i:s'),
            'guest_count' => $visitor->guest_count,
            'visit_purpose' => $visitor->visit_purpose,
            'status' => $visitor->status,
            'registration_source' => $visitor->registration_source,
            'qr_available' => $visitor->qrAvailable(),
            'approved_at' => $visitor->approved_at?->toIso8601String(),
            'rejected_at' => $visitor->rejected_at?->toIso8601String(),
            'cancelled_at' => $visitor->cancelled_at?->toIso8601String(),
            'checked_in_at' => $visitor->checked_in_at?->toIso8601String(),
            'checked_out_at' => $visitor->checked_out_at?->toIso8601String(),
            'expires_at' => $visitor->expires_at?->toIso8601String(),
            'access_card_number' => $visitor->access_card_number,
            'identity_photo_url' => $visitor->identity_photo_path
                ? route('api.resident.visitors.identity-photo', $visitor)
                : null,
            'unit' => $visitor->resident?->unit ? [
                'id' => $visitor->resident->unit->id,
                'code' => $visitor->resident->unit->code,
                'tower' => $visitor->resident->unit->tower,
                'floor' => $visitor->resident->unit->floor,
            ] : null,
        ];

        if ($includeTimeline) {
            $payload['timeline'] = $visitor->timeline();
            $payload['cancellation_reason'] = $visitor->cancellation_reason;
            $payload['rejection_reason'] = $visitor->rejection_reason;
        }

        return $payload;
    }

    /**
     * @return array<string, mixed>
     */
    private function validatePayload(Request $request, bool $updating = false): array
    {
        $maxGuests = AppSetting::getInteger(self::GUEST_LIMIT_KEY) ?? 10;

        $rules = [
            'visitor_name' => ['required', 'string', 'max:255'],
            'visitor_phone' => ['required', 'string', 'max:50'],
            'visit_date' => ['required', 'date', 'after_or_equal:today'],
            'estimated_arrival_time' => ['required', 'date_format:H:i'],
            'guest_count' => ['required', 'integer', 'min:1', 'max:'.$maxGuests],
            'visit_purpose' => ['required', 'string', 'max:255'],
            'identity_photo' => [$updating ? 'nullable' : 'nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'resident_id' => ['prohibited'],
            'status' => ['prohibited'],
            'registration_source' => ['prohibited'],
            'access_code' => ['prohibited'],
            'approved_at' => ['prohibited'],
            'rejected_at' => ['prohibited'],
            'cancelled_at' => ['prohibited'],
            'checked_in_at' => ['prohibited'],
            'checked_out_at' => ['prohibited'],
            'expires_at' => ['prohibited'],
            'access_card_number' => ['prohibited'],
        ];

        return $request->validate($rules, [
            'guest_count.max' => "Jumlah tamu maksimal {$maxGuests} orang.",
        ]);
    }

    private function generateAccessCode(): string
    {
        do {
            $code = Str::upper(Str::random(48));
        } while (Visitor::query()->where('access_code', $code)->exists());

        return $code;
    }
}
