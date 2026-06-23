<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Visitor;
use App\Services\Visitors\ExpireVisitors;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SecurityVisitorAccessController extends Controller
{
    public function validateCode(Request $request, ExpireVisitors $expireVisitors): JsonResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string'],
        ]);

        $visitor = Visitor::query()
            ->with('resident.unit')
            ->where('access_code', $validated['code'])
            ->first();

        if (! $visitor) {
            return response()->json([
                'data' => [
                    'is_valid' => false,
                    'reason' => 'Visitor tidak ditemukan.',
                ],
            ], 404);
        }

        $expireVisitors->applyToVisitor($visitor);

        if (! $visitor->hasValidAccessCodeNow()) {
            return response()->json([
                'data' => [
                    'is_valid' => false,
                    'reason' => $this->invalidReason($visitor),
                    'visitor_name' => $visitor->visitor_name,
                    'resident_name' => $visitor->resident?->name,
                    'unit' => $visitor->resident?->unit?->code,
                    'visit_date' => $visitor->visit_date?->toDateString(),
                    'estimated_arrival_time' => $visitor->estimated_arrival_time?->format('H:i:s'),
                    'guest_count' => $visitor->guest_count,
                    'visit_purpose' => $visitor->visit_purpose,
                    'status' => $visitor->status,
                    'identity_photo_url' => $visitor->identity_photo_path
                        ? route('api.security.visitors.identity-photo', $visitor)
                        : null,
                ],
            ], 422);
        }

        return response()->json([
            'data' => [
                'is_valid' => true,
                'reason' => null,
                'visitor_name' => $visitor->visitor_name,
                'resident_name' => $visitor->resident?->name,
                'unit' => $visitor->resident?->unit?->code,
                'visit_date' => $visitor->visit_date?->toDateString(),
                'estimated_arrival_time' => $visitor->estimated_arrival_time?->format('H:i:s'),
                'guest_count' => $visitor->guest_count,
                'visit_purpose' => $visitor->visit_purpose,
                'status' => $visitor->status,
                'identity_photo_url' => $visitor->identity_photo_path
                    ? route('api.security.visitors.identity-photo', $visitor)
                    : null,
            ],
        ]);
    }

    public function identityPhoto(Visitor $visitor): StreamedResponse
    {
        abort_if(! $visitor->identity_photo_path || ! Storage::disk('local')->exists($visitor->identity_photo_path), 404);

        return Storage::disk('local')->response($visitor->identity_photo_path);
    }

    private function invalidReason(Visitor $visitor): string
    {
        return match (true) {
            $visitor->status === Visitor::STATUS_PENDING => 'Visitor masih menunggu approval.',
            $visitor->status === Visitor::STATUS_REJECTED => 'Visitor sudah ditolak.',
            $visitor->status === Visitor::STATUS_CANCELLED => 'Visitor sudah dibatalkan.',
            $visitor->status === Visitor::STATUS_CHECKED_IN => 'Visitor sudah check-in.',
            $visitor->status === Visitor::STATUS_CHECKED_OUT => 'Visitor sudah check-out.',
            $visitor->status === Visitor::STATUS_EXPIRED => 'Visitor sudah expired.',
            ! now()->isSameDay($visitor->visit_date) => 'Kode hanya valid pada tanggal kunjungan.',
            $visitor->expires_at && now()->greaterThan($visitor->expires_at) => 'Kode akses sudah melewati masa berlaku.',
            default => 'Kode akses tidak valid.',
        };
    }
}
