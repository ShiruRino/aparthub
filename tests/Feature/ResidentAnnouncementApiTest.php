<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\Resident;
use App\Models\Unit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ResidentAnnouncementApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_resident_cannot_access_announcement_api(): void
    {
        $this->getJson('/api/resident/announcements')
            ->assertUnauthorized();
    }

    public function test_only_published_announcements_appear_in_resident_list_and_pinned_first(): void
    {
        $resident = $this->createResident();
        $token = $resident->createToken('mobile')->plainTextToken;

        $unpinned = Announcement::query()->create([
            'title' => 'General Update',
            'content' => 'General published announcement',
            'category' => 'General',
            'status' => Announcement::STATUS_PUBLISHED,
            'is_pinned' => false,
            'published_at' => now(),
        ]);

        $pinned = Announcement::query()->create([
            'title' => 'Pinned Notice',
            'content' => 'Pinned published announcement',
            'category' => 'Maintenance',
            'status' => Announcement::STATUS_PUBLISHED,
            'is_pinned' => true,
            'published_at' => now()->subDay(),
        ]);

        Announcement::query()->create([
            'title' => 'Draft Notice',
            'content' => 'Should stay hidden',
            'category' => 'Internal',
            'status' => Announcement::STATUS_DRAFT,
            'is_pinned' => false,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/resident/announcements');

        $response->assertOk()
            ->assertJsonFragment(['title' => $pinned->title])
            ->assertJsonFragment(['title' => $unpinned->title])
            ->assertJsonMissing(['title' => 'Draft Notice']);

        $data = $response->json('data');

        $this->assertSame($pinned->id, $data[0]['id']);
        $this->assertArrayNotHasKey('status', $data[0]);
        $this->assertArrayNotHasKey('updated_at', $data[0]);
    }

    public function test_draft_announcement_detail_returns_not_found(): void
    {
        $resident = $this->createResident();
        $token = $resident->createToken('mobile')->plainTextToken;

        $draft = Announcement::query()->create([
            'title' => 'Hidden Draft',
            'content' => 'Hidden draft detail',
            'category' => 'Internal',
            'status' => Announcement::STATUS_DRAFT,
            'is_pinned' => false,
        ]);

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/resident/announcements/'.$draft->id)
            ->assertNotFound();
    }

    public function test_resident_announcement_per_page_is_capped_at_twenty(): void
    {
        $resident = $this->createResident();
        $token = $resident->createToken('mobile')->plainTextToken;

        foreach (range(1, 25) as $index) {
            Announcement::query()->create([
                'title' => 'Announcement '.$index,
                'content' => 'Body '.$index,
                'category' => 'General',
                'status' => Announcement::STATUS_PUBLISHED,
                'is_pinned' => false,
                'published_at' => now()->subMinutes($index),
            ]);
        }

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/resident/announcements?per_page=50');

        $response->assertOk()
            ->assertJsonPath('meta.per_page', 20);

        $this->assertCount(20, $response->json('data'));
    }

    private function createResident(): Resident
    {
        $unit = Unit::query()->create([
            'code' => 'A-2301',
            'tower' => 'Tower A',
            'floor' => 23,
            'unit_type' => '2BR',
            'occupancy_status' => 'Terisi',
            'payment_status' => 'Lunas',
            'thumbnail_tone' => 'default',
        ]);

        return Resident::query()->create([
            'unit_id' => $unit->id,
            'name' => 'Resident Announcement',
            'email' => 'resident.announcement@example.com',
            'mobile_no' => '081299991111',
            'password' => Hash::make('secret-pass'),
            'resident_type' => 'Penyewa',
            'status' => 'Aktif',
            'move_in_date' => '2026-06-01',
            'contract_end_date' => '2027-06-01',
            'avatar_tone' => 'default',
        ]);
    }
}
