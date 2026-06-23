<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\Module;
use App\Models\Role;
use App\Models\User;
use App\Models\UserModule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnnouncementCenterFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_with_community_create_permission_can_create_draft_announcement(): void
    {
        $user = $this->makeCommunityUser([
            'can_create' => true,
        ]);

        $this->actingAs($user)
            ->post(route('community-management.announcements.store'), [
                'title' => 'Draft Community Notice',
                'category' => 'General',
                'content' => 'Draft content for residents.',
                'status' => Announcement::STATUS_DRAFT,
                'is_pinned' => '0',
            ])
            ->assertRedirect(route('community-management.announcements'));

        $this->assertDatabaseHas('announcements', [
            'title' => 'Draft Community Notice',
            'status' => Announcement::STATUS_DRAFT,
            'is_pinned' => false,
        ]);
    }

    public function test_user_with_community_update_permission_can_publish_announcement(): void
    {
        $user = $this->makeCommunityUser([
            'can_update' => true,
        ]);

        $announcement = Announcement::query()->create([
            'title' => 'Publish Me',
            'content' => 'Waiting for publish',
            'category' => 'General',
            'status' => Announcement::STATUS_DRAFT,
            'is_pinned' => false,
        ]);

        $this->actingAs($user)
            ->patch(route('community-management.announcements.publish', $announcement))
            ->assertRedirect(route('community-management.announcements'));

        $announcement->refresh();

        $this->assertSame(Announcement::STATUS_PUBLISHED, $announcement->status);
        $this->assertNotNull($announcement->published_at);
    }

    public function test_announcement_list_supports_search_filter_and_pinned_first_ordering(): void
    {
        $user = $this->makeCommunityUser();

        Announcement::query()->create([
            'title' => 'Regular Maintenance Update',
            'content' => 'Regular content',
            'category' => 'Maintenance',
            'status' => Announcement::STATUS_PUBLISHED,
            'is_pinned' => false,
            'published_at' => now()->subDay(),
        ]);

        Announcement::query()->create([
            'title' => 'Pinned Maintenance Alert',
            'content' => 'Pinned content',
            'category' => 'Maintenance',
            'status' => Announcement::STATUS_PUBLISHED,
            'is_pinned' => true,
            'published_at' => now()->subDays(2),
        ]);

        Announcement::query()->create([
            'title' => 'Draft Internal Notice',
            'content' => 'Draft content',
            'category' => 'Internal',
            'status' => Announcement::STATUS_DRAFT,
            'is_pinned' => false,
        ]);

        $response = $this->actingAs($user)
            ->get(route('community-management.announcements', [
                'search' => 'Maintenance',
                'status' => Announcement::STATUS_PUBLISHED,
                'category' => 'Maintenance',
            ]));

        $response->assertOk()
            ->assertSee('Pinned Maintenance Alert')
            ->assertSee('Regular Maintenance Update')
            ->assertDontSee('Draft Internal Notice');

        $content = $response->getContent();

        $this->assertLessThan(
            strpos($content, 'Regular Maintenance Update'),
            strpos($content, 'Pinned Maintenance Alert')
        );
    }

    private function makeCommunityUser(array $permissionOverrides = []): User
    {
        $role = Role::query()->create([
            'name' => 'Staff',
            'slug' => 'staff',
        ]);

        $module = Module::query()->create([
            'name' => 'Community Management',
            'slug' => 'community-management',
            'description' => 'Community operations module.',
            'sort_order' => 50,
            'is_active' => true,
        ]);

        $user = User::query()->create([
            'role_id' => $role->id,
            'name' => 'Community Staff',
            'username' => 'community-staff-'.uniqid(),
            'password' => 'password',
        ]);

        UserModule::query()->create(array_merge([
            'user_id' => $user->id,
            'module_id' => $module->id,
            'can_create' => false,
            'can_read' => true,
            'can_update' => false,
            'can_delete' => false,
        ], $permissionOverrides));

        return $user;
    }
}
