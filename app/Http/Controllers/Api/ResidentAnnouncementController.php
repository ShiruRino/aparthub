<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ResidentAnnouncementController extends Controller
{
    /**
     * @group Resident Announcements
     *
     * Return published announcements visible to the authenticated resident.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = min(max($request->integer('per_page', 10), 1), 20);

        $announcements = Announcement::query()
            ->published()
            ->orderedForResident()
            ->paginate($perPage)
            ->withQueryString();

        return response()->json([
            'data' => $announcements->getCollection()->map(fn (Announcement $announcement) => $this->payload($announcement))->all(),
            'meta' => [
                'current_page' => $announcements->currentPage(),
                'last_page' => $announcements->lastPage(),
                'per_page' => $announcements->perPage(),
                'total' => $announcements->total(),
            ],
        ]);
    }

    /**
     * @group Resident Announcements
     *
     * Return a single published announcement for the authenticated resident.
     */
    public function show(int $announcement): JsonResponse
    {
        $record = Announcement::query()
            ->published()
            ->findOrFail($announcement);

        return response()->json([
            'data' => $this->payload($record),
        ]);
    }

    /**
     * Transform an announcement into a safe resident payload.
     *
     * @return array<string, mixed>
     */
    private function payload(Announcement $announcement): array
    {
        return [
            'id' => $announcement->id,
            'title' => $announcement->title,
            'content' => $announcement->content,
            'category' => $announcement->category,
            'is_pinned' => $announcement->is_pinned,
            'published_at' => optional($announcement->published_at)?->toISOString(),
        ];
    }
}
