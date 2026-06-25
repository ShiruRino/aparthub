<?php

namespace Tests\Feature;

use App\Models\AppSetting;
use App\Models\Resident;
use App\Models\ServiceRequest;
use App\Models\ServiceRequestCategory;
use App\Models\ServiceRequestSubcategory;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TechnicianApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_technician_can_login_using_email(): void
    {
        $this->seed(DatabaseSeeder::class);

        $technician = User::query()->where('username', 'tech.budi')->firstOrFail();

        $this->postJson('/api/technician/login', [
            'login' => $technician->email,
            'password' => 'password',
        ])
            ->assertOk()
            ->assertJsonPath('data.email', $technician->email)
            ->assertJsonStructure(['data' => ['token', 'teams', 'profile']]);
    }

    public function test_technician_can_login_using_mobile_number(): void
    {
        $this->seed(DatabaseSeeder::class);

        $technician = User::query()->where('username', 'tech.rizky')->firstOrFail();

        $this->postJson('/api/technician/login', [
            'login' => $technician->mobile_no,
            'password' => 'password',
        ])
            ->assertOk()
            ->assertJsonPath('data.mobile_no', $technician->mobile_no);
    }

    public function test_inactive_technician_cannot_authenticate(): void
    {
        $this->seed(DatabaseSeeder::class);

        $technician = User::query()->where('username', 'tech.budi')->firstOrFail();
        $technician->update(['is_active' => false]);

        $this->postJson('/api/technician/login', [
            'login' => $technician->email,
            'password' => 'password',
        ])->assertUnauthorized();
    }

    public function test_technician_only_sees_tickets_assigned_to_their_team(): void
    {
        $this->seed(DatabaseSeeder::class);

        $technician = User::query()->where('username', 'tech.budi')->firstOrFail();
        $token = $technician->createToken('technician-test')->plainTextToken;

        $visibleTicket = ServiceRequest::query()->where('ticket_number', 'SR-2026-002')->firstOrFail();
        $hiddenTicket = ServiceRequest::query()->where('ticket_number', 'SR-2026-003')->firstOrFail();

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/technician/service-requests')
            ->assertOk()
            ->assertJsonFragment(['ticket_number' => $visibleTicket->ticket_number])
            ->assertJsonMissing(['ticket_number' => $hiddenTicket->ticket_number]);
    }

    public function test_technician_cannot_access_another_teams_ticket(): void
    {
        $this->seed(DatabaseSeeder::class);

        $technician = User::query()->where('username', 'tech.budi')->firstOrFail();
        $token = $technician->createToken('technician-test')->plainTextToken;
        $hiddenTicket = ServiceRequest::query()->where('ticket_number', 'SR-2026-003')->firstOrFail();

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/technician/service-requests/'.$hiddenTicket->id)
            ->assertNotFound();
    }

    public function test_on_the_way_requires_eta_and_logs_actor(): void
    {
        $this->seed(DatabaseSeeder::class);

        $technician = User::query()->where('username', 'tech.budi')->firstOrFail();
        $token = $technician->createToken('technician-test')->plainTextToken;
        $ticket = $this->makeTechnicianTicket(technicianUsername: 'tech.budi', status: ServiceRequest::STATUS_ASSIGNED);

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/technician/service-requests/'.$ticket->id.'/on-the-way', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['estimated_arrival_minutes']);

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/technician/service-requests/'.$ticket->id.'/on-the-way', [
                'estimated_arrival_minutes' => 18,
            ])
            ->assertOk()
            ->assertJsonPath('data.status', ServiceRequest::STATUS_ON_THE_WAY)
            ->assertJsonPath('data.estimated_arrival_minutes', 18);

        $ticket->refresh();

        $this->assertSame(ServiceRequest::STATUS_ON_THE_WAY, $ticket->status);
        $this->assertDatabaseHas('service_request_events', [
            'service_request_id' => $ticket->id,
            'acted_by_user_id' => $technician->id,
            'event_type' => 'on_the_way',
            'to_status' => ServiceRequest::STATUS_ON_THE_WAY,
        ]);
    }

    public function test_start_requires_before_photos_and_stores_evidence(): void
    {
        Storage::fake('public');
        $this->seed(DatabaseSeeder::class);

        $technician = User::query()->where('username', 'tech.budi')->firstOrFail();
        $token = $technician->createToken('technician-test')->plainTextToken;
        $ticket = $this->makeTechnicianTicket(technicianUsername: 'tech.budi', status: ServiceRequest::STATUS_ON_THE_WAY);

        $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json',
        ])->post('/api/technician/service-requests/'.$ticket->id.'/start', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['before_photos']);

        $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json',
        ])->post('/api/technician/service-requests/'.$ticket->id.'/start', [
            'before_photos' => [
                UploadedFile::fake()->image('before-one.jpg'),
                UploadedFile::fake()->image('before-two.jpg'),
            ],
        ])->assertOk()
            ->assertJsonPath('data.status', ServiceRequest::STATUS_IN_PROGRESS);

        $ticket->refresh();

        $this->assertSame(ServiceRequest::STATUS_IN_PROGRESS, $ticket->status);
        $this->assertSame(2, $ticket->attachments()->where('attachment_type', 'technician_before')->count());
    }

    public function test_complete_requires_after_photos_and_completion_notes(): void
    {
        Storage::fake('public');
        $this->seed(DatabaseSeeder::class);

        $technician = User::query()->where('username', 'tech.budi')->firstOrFail();
        $token = $technician->createToken('technician-test')->plainTextToken;
        $ticket = $this->makeTechnicianTicket(technicianUsername: 'tech.budi', status: ServiceRequest::STATUS_IN_PROGRESS);

        $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json',
        ])->post('/api/technician/service-requests/'.$ticket->id.'/complete', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['completion_notes', 'after_photos']);

        $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json',
        ])->post('/api/technician/service-requests/'.$ticket->id.'/complete', [
            'completion_notes' => 'Issue resolved and resident confirmed.',
            'after_photos' => [
                UploadedFile::fake()->image('after-one.jpg'),
            ],
        ])->assertOk()
            ->assertJsonPath('data.status', ServiceRequest::STATUS_COMPLETED);

        $ticket->refresh();

        $this->assertSame(ServiceRequest::STATUS_COMPLETED, $ticket->status);
        $this->assertSame(1, $ticket->attachments()->where('attachment_type', 'technician_after')->count());
        $this->assertDatabaseHas('service_request_events', [
            'service_request_id' => $ticket->id,
            'acted_by_user_id' => $technician->id,
            'event_type' => 'complete',
            'to_status' => ServiceRequest::STATUS_COMPLETED,
        ]);
    }

    public function test_hotline_returns_configured_values(): void
    {
        $this->seed(DatabaseSeeder::class);

        AppSetting::query()->updateOrCreate(['key' => 'technician_hotline_name'], ['value' => 'Ops Hotline']);
        AppSetting::query()->updateOrCreate(['key' => 'technician_hotline_phone'], ['value' => '0800-111-222']);
        AppSetting::query()->updateOrCreate(['key' => 'technician_hotline_note'], ['value' => 'Gunakan saat eskalasi darurat.']);

        $technician = User::query()->where('username', 'tech.budi')->firstOrFail();
        $token = $technician->createToken('technician-test')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/technician/hotline')
            ->assertOk()
            ->assertJsonPath('data.name', 'Ops Hotline')
            ->assertJsonPath('data.phone', '0800-111-222')
            ->assertJsonPath('data.note', 'Gunakan saat eskalasi darurat.');
    }

    private function makeTechnicianTicket(string $technicianUsername, string $status): ServiceRequest
    {
        $technician = User::query()->where('username', $technicianUsername)->firstOrFail();
        $team = $technician->technicianTeams()->firstOrFail();
        $resident = Resident::query()->firstOrFail();
        $category = ServiceRequestCategory::query()->firstOrFail();
        $subcategory = ServiceRequestSubcategory::query()->where('service_request_category_id', $category->id)->firstOrFail();

        return ServiceRequest::query()->create([
            'ticket_number' => 'SR-TECH-'.str_pad((string) random_int(1, 999), 3, '0', STR_PAD_LEFT),
            'resident_id' => $resident->id,
            'service_request_category_id' => $category->id,
            'service_request_subcategory_id' => $subcategory->id,
            'technician_team_id' => $team->id,
            'category' => $category->name,
            'title' => 'Technician workflow test',
            'description' => 'Testing technician action lifecycle.',
            'priority' => ServiceRequest::PRIORITY_HIGH,
            'status' => $status,
            'source' => 'Front Office',
            'assigned_to' => $team->name,
            'sla_target_minutes' => $subcategory->high_sla_minutes,
            'sla_due_at' => now()->addHours(2),
            'assigned_at' => now()->subMinutes(30),
            'on_the_way_at' => $status === ServiceRequest::STATUS_ON_THE_WAY || $status === ServiceRequest::STATUS_IN_PROGRESS ? now()->subMinutes(20) : null,
            'in_progress_at' => $status === ServiceRequest::STATUS_IN_PROGRESS ? now()->subMinutes(10) : null,
        ]);
    }
}
