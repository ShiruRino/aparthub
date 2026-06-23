<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CommunityManagementController extends Controller
{
    public function announcements(Request $request): View
    {
        return $this->page('announcements', $this->announcementWorkspace($request));
    }

    public function events(): View
    {
        return $this->page('events');
    }

    public function pollingSurvey(): View
    {
        return $this->page('polling-survey');
    }

    public function forum(): View
    {
        return $this->page('forum');
    }

    public function broadcasts(): View
    {
        return $this->page('broadcasts');
    }

    public function programs(): View
    {
        return $this->page('programs');
    }

    public function calendar(): View
    {
        return $this->page('calendar');
    }

    public function engagement(): View
    {
        return $this->page('engagement');
    }

    public function archive(): View
    {
        return $this->page('archive');
    }

    public function settings(): View
    {
        return $this->page('settings');
    }

    public function storeAnnouncement(Request $request): RedirectResponse
    {
        Announcement::query()->create($this->validatedAnnouncement($request));

        return redirect()
            ->route('community-management.announcements', $this->announcementRedirectQuery($request))
            ->with('status', 'Announcement berhasil ditambahkan.');
    }

    public function updateAnnouncement(Request $request, Announcement $announcement): RedirectResponse
    {
        $announcement->update($this->validatedAnnouncement($request, $announcement));

        return redirect()
            ->route('community-management.announcements', $this->announcementRedirectQuery($request))
            ->with('status', 'Announcement berhasil diperbarui.');
    }

    public function destroyAnnouncement(Request $request, Announcement $announcement): RedirectResponse
    {
        $announcement->delete();

        return redirect()
            ->route('community-management.announcements', $this->announcementRedirectQuery($request))
            ->with('status', 'Announcement berhasil dihapus.');
    }

    public function toggleAnnouncementPublish(Request $request, Announcement $announcement): RedirectResponse
    {
        $announcement->update([
            'status' => $announcement->status === Announcement::STATUS_PUBLISHED
                ? Announcement::STATUS_DRAFT
                : Announcement::STATUS_PUBLISHED,
            'published_at' => $announcement->status === Announcement::STATUS_PUBLISHED
                ? $announcement->published_at
                : ($announcement->published_at ?: now()),
        ]);

        return redirect()
            ->route('community-management.announcements', $this->announcementRedirectQuery($request))
            ->with('status', 'Status publish announcement berhasil diperbarui.');
    }

    public function toggleAnnouncementPin(Request $request, Announcement $announcement): RedirectResponse
    {
        $announcement->update([
            'is_pinned' => ! $announcement->is_pinned,
        ]);

        return redirect()
            ->route('community-management.announcements', $this->announcementRedirectQuery($request))
            ->with('status', 'Status pin announcement berhasil diperbarui.');
    }

    private function page(string $page, array $data = []): View
    {
        return view('community-management.index', array_merge([
            'pageKey' => $page,
        ], $data));
    }

    /**
     * Build the announcement workspace payload.
     *
     * @return array<string, mixed>
     */
    private function announcementWorkspace(Request $request): array
    {
        $query = Announcement::query();
        $search = trim((string) $request->string('search'));
        $status = $request->string('status')->toString();
        $category = trim((string) $request->string('category'));

        if ($search !== '') {
            $query->where(function ($builder) use ($search): void {
                $builder
                    ->where('title', 'like', '%'.$search.'%')
                    ->orWhere('content', 'like', '%'.$search.'%');
            });
        }

        if ($status !== '') {
            $query->where('status', $status);
        }

        if ($category !== '') {
            $query->where('category', $category);
        }

        $announcements = $query
            ->orderedForAdmin()
            ->get();

        return [
            'announcementWorkspace' => [
                'records' => $announcements,
                'filters' => [
                    'search' => $search ?? '',
                    'status' => $status ?? '',
                    'category' => $category ?? '',
                ],
                'categories' => Announcement::query()
                    ->select('category')
                    ->distinct()
                    ->orderBy('category')
                    ->pluck('category')
                    ->filter()
                    ->values(),
                'metrics' => [
                    ['label' => 'Published', 'value' => (string) Announcement::query()->published()->count(), 'sub' => 'Visible to residents', 'icon' => 'P', 'tone' => 'green'],
                    ['label' => 'Draft', 'value' => (string) Announcement::query()->where('status', Announcement::STATUS_DRAFT)->count(), 'sub' => 'Need review', 'icon' => 'D', 'tone' => 'gold'],
                    ['label' => 'Pinned', 'value' => (string) Announcement::query()->where('is_pinned', true)->count(), 'sub' => 'Highlighted first', 'icon' => 'I', 'tone' => 'purple'],
                    ['label' => 'Category', 'value' => (string) Announcement::query()->distinct()->count('category'), 'sub' => 'Active labels', 'icon' => 'C', 'tone' => 'blue'],
                    ['label' => 'Results', 'value' => (string) $announcements->count(), 'sub' => 'Current filter result', 'icon' => 'R', 'tone' => 'red'],
                ],
            ],
        ];
    }

    /**
     * Validate and normalize announcement input.
     *
     * @return array<string, mixed>
     */
    private function validatedAnnouncement(Request $request, ?Announcement $announcement = null): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:100'],
            'content' => ['required', 'string'],
            'status' => ['required', 'in:'.Announcement::STATUS_DRAFT.','.Announcement::STATUS_PUBLISHED],
            'is_pinned' => ['nullable', 'boolean'],
        ]);

        $status = $data['status'];
        $publishedAt = $announcement?->published_at;

        if ($status === Announcement::STATUS_PUBLISHED && $publishedAt === null) {
            $publishedAt = now();
        }

        return [
            'title' => $data['title'],
            'category' => $data['category'] ?: 'General',
            'content' => $data['content'],
            'status' => $status,
            'is_pinned' => (bool) ($data['is_pinned'] ?? false),
            'published_at' => $publishedAt,
        ];
    }

    /**
     * Preserve current listing filters after state-changing actions.
     *
     * @return array<string, string>
     */
    private function announcementRedirectQuery(Request $request): array
    {
        return collect([
            'search' => $request->input('redirect_search'),
            'status' => $request->input('redirect_status'),
            'category' => $request->input('redirect_category'),
        ])
            ->filter(fn (?string $value) => filled($value))
            ->map(fn (?string $value) => (string) $value)
            ->all();
    }
}
