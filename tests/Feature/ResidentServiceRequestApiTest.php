<?php

namespace Tests\Feature;

use App\Models\Module;
use App\Models\Resident;
use App\Models\Role;
use App\Models\ServiceRequest;
use App\Models\ServiceRequestCategory;
use App\Models\ServiceRequestSubcategory;
use App\Models\Unit;
use App\Models\User;
use App\Models\UserModule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ResidentServiceRequestApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_active_categories_and_subcategories_appear_in_catalog_api(): void
    {
        [$resident] = $this->seedResidentCatalogData();
        $token = $resident->createToken('mobile')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/service-request-catalog')
            ->assertOk()
            ->assertJsonFragment(['name' => 'Plumbing'])
            ->assertJsonFragment(['name' => 'Leak Repair'])
            ->assertJsonMissing(['name' => 'Inactive Category'])
            ->assertJsonMissing(['name' => 'Inactive Subcategory']);
    }

    public function test_resident_can_only_create_ticket_for_themselves(): void
    {
        [$resident, $category, $subcategory] = $this->seedResidentCatalogData();
        $token = $resident->createToken('mobile')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/service-requests', [
                'resident_id' => 999,
                'subcategory_id' => $subcategory->id,
                'title' => 'Unauthorized resident injection',
                'description' => 'Should fail because resident is derived from token.',
                'priority' => ServiceRequest::PRIORITY_HIGH,
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['resident_id']);
    }

    public function test_resident_can_only_see_their_own_tickets(): void
    {
        [$resident, $category, $subcategory, $otherResident] = $this->seedResidentCatalogData();
        $token = $resident->createToken('mobile')->plainTextToken;

        ServiceRequest::query()->create($this->ticketPayload($resident, $category, $subcategory, 'SR-OWN-001'));
        ServiceRequest::query()->create($this->ticketPayload($otherResident, $category, $subcategory, 'SR-OTHER-001'));

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/service-requests')
            ->assertOk()
            ->assertJsonFragment(['ticket_number' => 'SR-OWN-001'])
            ->assertJsonMissing(['ticket_number' => 'SR-OTHER-001']);
    }

    public function test_sla_due_time_is_calculated_correctly_for_each_priority(): void
    {
        [$resident, $category, $subcategory] = $this->seedResidentCatalogData();
        $token = $resident->createToken('mobile')->plainTextToken;

        foreach ([
            ServiceRequest::PRIORITY_LOW => $subcategory->low_sla_minutes,
            ServiceRequest::PRIORITY_MEDIUM => $subcategory->medium_sla_minutes,
            ServiceRequest::PRIORITY_HIGH => $subcategory->high_sla_minutes,
        ] as $priority => $expectedMinutes) {
            $response = $this->withHeader('Authorization', 'Bearer '.$token)
                ->post('/api/service-requests', [
                    'subcategory_id' => $subcategory->id,
                    'title' => 'Ticket '.$priority,
                    'description' => 'Testing '.$priority.' SLA',
                    'priority' => $priority,
                ]);

            $response->assertCreated();

            $ticket = ServiceRequest::query()->where('title', 'Ticket '.$priority)->firstOrFail();

            $this->assertSame($expectedMinutes, $ticket->sla_target_minutes);
            $this->assertNotNull($ticket->sla_due_at);
            $this->assertEquals(
                $ticket->created_at->copy()->addMinutes($expectedMinutes)->format('Y-m-d H:i'),
                $ticket->sla_due_at->format('Y-m-d H:i')
            );
        }
    }

    public function test_emergency_sla_uses_configured_emergency_value(): void
    {
        [$resident, $category, $subcategory] = $this->seedResidentCatalogData();
        $token = $resident->createToken('mobile')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->post('/api/service-requests', [
                'subcategory_id' => $subcategory->id,
                'title' => 'Emergency Ticket',
                'description' => 'Critical issue',
                'priority' => ServiceRequest::PRIORITY_EMERGENCY,
            ])
            ->assertCreated();

        $ticket = ServiceRequest::query()->where('title', 'Emergency Ticket')->firstOrFail();

        $this->assertSame($subcategory->emergency_sla_minutes, $ticket->sla_target_minutes);
    }

    public function test_ticket_api_rejects_arbitrary_status_source_and_ticket_number(): void
    {
        [$resident, $category, $subcategory] = $this->seedResidentCatalogData();
        $token = $resident->createToken('mobile')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/service-requests', [
                'subcategory_id' => $subcategory->id,
                'title' => 'Injected Ticket',
                'description' => 'Trying to force internals',
                'priority' => ServiceRequest::PRIORITY_MEDIUM,
                'ticket_number' => 'CUSTOM-1',
                'status' => ServiceRequest::STATUS_COMPLETED,
                'source' => 'Injected Source',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['ticket_number', 'status', 'source']);
    }

    public function test_attachments_validate_file_count_type_and_size(): void
    {
        Storage::fake('public');

        [$resident, $category, $subcategory] = $this->seedResidentCatalogData();
        $token = $resident->createToken('mobile')->plainTextToken;

        $files = [
            UploadedFile::fake()->image('one.jpg'),
            UploadedFile::fake()->image('two.jpg'),
            UploadedFile::fake()->image('three.jpg'),
            UploadedFile::fake()->create('four.pdf', 100, 'application/pdf'),
        ];

        $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json',
        ])
            ->post('/api/service-requests', [
                'subcategory_id' => $subcategory->id,
                'title' => 'Too many bad files',
                'description' => 'Attachment validation test',
                'priority' => ServiceRequest::PRIORITY_LOW,
                'attachments' => $files,
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['attachments', 'attachments.3']);

        $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json',
        ])
            ->post('/api/service-requests', [
                'subcategory_id' => $subcategory->id,
                'title' => 'Too large image',
                'description' => 'Large file validation test',
                'priority' => ServiceRequest::PRIORITY_LOW,
                'attachments' => [UploadedFile::fake()->image('huge.jpg')->size(6000)],
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['attachments.0']);
    }

    public function test_service_request_api_creates_attachment_records_and_returns_attachment_metadata(): void
    {
        Storage::fake('public');

        [$resident, $category, $subcategory] = $this->seedResidentCatalogData();
        $token = $resident->createToken('mobile')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json',
        ])->post('/api/service-requests', [
            'subcategory_id' => $subcategory->id,
            'title' => 'Attachment Ticket',
            'description' => 'Ticket created with canonical attachments payload.',
            'priority' => ServiceRequest::PRIORITY_MEDIUM,
            'attachments' => [
                UploadedFile::fake()->image('proof-one.jpg'),
                UploadedFile::fake()->image('proof-two.png'),
            ],
        ]);

        $response->assertCreated()
            ->assertJsonCount(2, 'data.attachments')
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'ticket_number',
                    'attachments' => [
                        '*' => ['id', 'file_name', 'mime_type', 'file_size', 'url'],
                    ],
                ],
            ]);

        $this->assertDatabaseCount('service_request_attachments', 2);
        $this->assertStringContainsString('/storage/service-requests/attachments/', $response->json('data.attachments.0.url'));
    }

    public function test_service_request_api_rejects_legacy_images_alias(): void
    {
        Storage::fake('public');

        [$resident, $category, $subcategory] = $this->seedResidentCatalogData();
        $token = $resident->createToken('mobile')->plainTextToken;

        $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json',
        ])->post('/api/service-requests', [
            'subcategory_id' => $subcategory->id,
            'title' => 'Legacy Images Alias Ticket',
            'description' => 'Legacy field should be rejected.',
            'priority' => ServiceRequest::PRIORITY_MEDIUM,
            'images' => [
                UploadedFile::fake()->image('legacy-proof.jpg'),
            ],
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['images']);
    }

    public function test_admin_service_request_detail_renders_uploaded_images(): void
    {
        Storage::fake('public');

        [$resident, $category, $subcategory] = $this->seedResidentCatalogData();
        $admin = $this->createAdminUser();

        $ticket = ServiceRequest::query()->create($this->ticketPayload($resident, $category, $subcategory, 'SR-IMG-001'));
        $ticket->attachments()->create([
            'disk' => 'public',
            'path' => 'service-requests/attachments/proof-admin.jpg',
            'original_name' => 'proof-admin.jpg',
            'mime_type' => 'image/jpeg',
            'file_size' => 12345,
        ]);

        $this->actingAs($admin)
            ->get(route('service-request.ticket-queue'))
            ->assertOk()
            ->assertSee('proof-admin.jpg')
            ->assertSee('/storage/service-requests/attachments/proof-admin.jpg');
    }

    public function test_admin_status_update_is_visible_through_resident_ticket_detail_api(): void
    {
        [$resident, $category, $subcategory] = $this->seedResidentCatalogData();
        $admin = $this->createAdminUser();
        $token = $resident->createToken('mobile')->plainTextToken;

        $ticket = ServiceRequest::query()->create($this->ticketPayload($resident, $category, $subcategory, 'SR-VIS-001'));

        $this->actingAs($admin)
            ->put(route('service-request.update', $ticket), [
                'resident_id' => $resident->id,
                'category_id' => $category->id,
                'subcategory_id' => $subcategory->id,
                'title' => $ticket->title,
                'description' => $ticket->description,
                'priority' => ServiceRequest::PRIORITY_EMERGENCY,
                'status' => ServiceRequest::STATUS_ASSIGNED,
                'source' => 'Front Office',
                'assigned_to' => 'John Technical',
                'completion_notes' => '',
            ])
            ->assertRedirect();

        auth()->logout();
        $this->flushSession();

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/service-requests/'.$ticket->id)
            ->assertOk()
            ->assertJsonPath('data.status', ServiceRequest::STATUS_ASSIGNED)
            ->assertJsonPath('data.assigned_to', 'John Technical')
            ->assertJsonPath('data.priority', ServiceRequest::PRIORITY_EMERGENCY);
    }

    private function seedResidentCatalogData(): array
    {
        $unit = Unit::query()->create([
            'code' => 'A-1701',
            'tower' => 'Tower A',
            'floor' => 17,
            'unit_type' => '2BR',
            'occupancy_status' => 'Terisi',
            'payment_status' => 'Lunas',
            'thumbnail_tone' => 'default',
        ]);

        $resident = Resident::query()->create([
            'unit_id' => $unit->id,
            'name' => 'Resident One',
            'email' => 'resident.one@example.com',
            'mobile_no' => '081200000111',
            'password' => Hash::make('secret-pass'),
            'resident_type' => 'Penyewa',
            'status' => 'Aktif',
            'move_in_date' => '2026-06-01',
            'contract_end_date' => '2027-06-01',
            'avatar_tone' => 'default',
        ]);

        $otherResident = Resident::query()->create([
            'unit_id' => $unit->id,
            'name' => 'Resident Two',
            'email' => 'resident.two@example.com',
            'mobile_no' => '081200000222',
            'password' => Hash::make('secret-pass'),
            'resident_type' => 'Pemilik',
            'status' => 'Aktif',
            'move_in_date' => '2026-06-01',
            'avatar_tone' => 'default',
        ]);

        $category = ServiceRequestCategory::query()->create([
            'name' => 'Plumbing',
            'is_active' => true,
            'sort_order' => 10,
        ]);

        $inactiveCategory = ServiceRequestCategory::query()->create([
            'name' => 'Inactive Category',
            'is_active' => false,
            'sort_order' => 20,
        ]);

        $subcategory = ServiceRequestSubcategory::query()->create([
            'service_request_category_id' => $category->id,
            'name' => 'Leak Repair',
            'is_active' => true,
            'sort_order' => 10,
            'low_sla_minutes' => 360,
            'medium_sla_minutes' => 240,
            'high_sla_minutes' => 120,
            'emergency_sla_minutes' => 45,
        ]);

        ServiceRequestSubcategory::query()->create([
            'service_request_category_id' => $category->id,
            'name' => 'Inactive Subcategory',
            'is_active' => false,
            'sort_order' => 20,
            'low_sla_minutes' => 360,
            'medium_sla_minutes' => 240,
            'high_sla_minutes' => 120,
            'emergency_sla_minutes' => 45,
        ]);

        ServiceRequestSubcategory::query()->create([
            'service_request_category_id' => $inactiveCategory->id,
            'name' => 'Inactive Category Child',
            'is_active' => true,
            'sort_order' => 10,
            'low_sla_minutes' => 360,
            'medium_sla_minutes' => 240,
            'high_sla_minutes' => 120,
            'emergency_sla_minutes' => 45,
        ]);

        return [$resident, $category, $subcategory, $otherResident];
    }

    private function ticketPayload(Resident $resident, ServiceRequestCategory $category, ServiceRequestSubcategory $subcategory, string $ticketNumber): array
    {
        return [
            'ticket_number' => $ticketNumber,
            'resident_id' => $resident->id,
            'service_request_category_id' => $category->id,
            'service_request_subcategory_id' => $subcategory->id,
            'category' => $category->name,
            'title' => 'Request '.$ticketNumber,
            'description' => 'Ticket for testing',
            'priority' => ServiceRequest::PRIORITY_HIGH,
            'status' => ServiceRequest::STATUS_SUBMITTED,
            'source' => 'Resident App',
            'sla_target_minutes' => $subcategory->high_sla_minutes,
            'sla_due_at' => now()->addMinutes($subcategory->high_sla_minutes),
        ];
    }

    private function createAdminUser(): User
    {
        $role = Role::query()->create([
            'name' => 'Admin',
            'slug' => 'admin',
        ]);

        $module = Module::query()->firstOrCreate(
            ['slug' => 'service-request'],
            [
                'name' => 'Service Request',
                'description' => 'Service request access',
                'sort_order' => 1,
                'is_active' => true,
            ]
        );

        $admin = User::query()->create([
            'name' => 'Admin User',
            'username' => 'admin-api',
            'password' => 'password',
            'role_id' => $role->id,
        ]);

        UserModule::query()->create([
            'user_id' => $admin->id,
            'module_id' => $module->id,
            'can_create' => true,
            'can_read' => true,
            'can_update' => true,
            'can_delete' => true,
        ]);

        return $admin;
    }
}
