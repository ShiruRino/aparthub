<?php

namespace Tests\Feature;

use App\Models\Resident;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResidentDetailFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_resident_detail_contains_owner_name_family_members_and_move_logs(): void
    {
        $this->seed(DatabaseSeeder::class);
        $admin = User::query()->where('username', 'admin')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('resident-management.residents'))
            ->assertOk()
            ->assertSee('Detail Residen')
            ->assertSee('Owner Name')
            ->assertSee('Ahmad Rizky')
            ->assertSee('Family Members')
            ->assertSee('Alya Rizky')
            ->assertSee('Move In / Move Out Log')
            ->assertSee('MOI-2026-001')
            ->assertDontSee('Pindahan Status');
    }

    public function test_family_member_can_be_added_from_resident_detail_and_redirect_back_to_residents(): void
    {
        $this->seed(DatabaseSeeder::class);
        $admin = User::query()->where('username', 'admin')->firstOrFail();
        $resident = Resident::query()->where('name', 'Ahmad Rizky')->firstOrFail();

        $this->actingAs($admin)
            ->post(route('resident-management.family-members.store'), [
                'resident_id' => $resident->id,
                'name' => 'Modal Family Member',
                'relationship' => 'Anak',
                'birth_date' => '2022-02-02',
                'access_status' => 'Aktif',
                'redirect_to' => 'resident-management.residents',
            ])
            ->assertRedirect(route('resident-management.residents'));

        $this->assertDatabaseHas('resident_family_members', [
            'resident_id' => $resident->id,
            'name' => 'Modal Family Member',
        ]);
    }
}
