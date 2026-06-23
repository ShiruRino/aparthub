<?php

namespace Tests\Feature;

use App\Models\Resident;
use App\Models\Unit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ResidentApiAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_resident_login_fields_exist_in_schema(): void
    {
        $this->assertTrue(Schema::hasColumns('residents', [
            'email',
            'mobile_no',
            'password',
            'contract_end_date',
        ]));
    }

    public function test_resident_can_login_using_email(): void
    {
        $resident = $this->createResident([
            'email' => 'resident@example.com',
            'mobile_no' => '081234567890',
            'password' => Hash::make('secret-pass'),
        ]);

        $response = $this->postJson('/api/resident/login', [
            'login' => 'resident@example.com',
            'password' => 'secret-pass',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.id', $resident->id)
            ->assertJsonPath('data.email', 'resident@example.com')
            ->assertJsonPath('data.mobile_no', '081234567890')
            ->assertJsonPath('data.unit.code', $resident->unit->code)
            ->assertJsonPath('data.contract_end_date', $resident->contract_end_date->toDateString());

        $this->assertNotEmpty($response->json('data.token'));
    }

    public function test_resident_can_login_using_mobile_number(): void
    {
        $resident = $this->createResident([
            'email' => 'mobilelogin@example.com',
            'mobile_no' => '081298765432',
            'password' => Hash::make('secret-pass'),
        ]);

        $this->postJson('/api/resident/login', [
            'login' => '081298765432',
            'password' => 'secret-pass',
        ])->assertOk()
            ->assertJsonPath('data.id', $resident->id)
            ->assertJsonPath('data.mobile_no', '081298765432');
    }

    public function test_resident_login_fails_for_invalid_password(): void
    {
        $this->createResident([
            'email' => 'resident@example.com',
            'mobile_no' => '081234567890',
            'password' => Hash::make('secret-pass'),
        ]);

        $this->postJson('/api/resident/login', [
            'login' => 'resident@example.com',
            'password' => 'wrong-pass',
        ])->assertUnauthorized()
            ->assertJsonPath('message', 'Kredensial resident tidak valid.');
    }

    public function test_password_is_never_returned_in_api_json(): void
    {
        $resident = $this->createResident([
            'email' => 'hidden@example.com',
            'mobile_no' => '081211112222',
            'password' => Hash::make('secret-pass'),
        ]);

        $response = $this->postJson('/api/resident/login', [
            'login' => $resident->email,
            'password' => 'secret-pass',
        ]);

        $response->assertOk()
            ->assertJsonMissingPath('data.password')
            ->assertJsonMissingPath('data.password_hash');
    }

    public function test_authenticated_resident_me_endpoint_returns_safe_profile(): void
    {
        $resident = $this->createResident([
            'email' => 'me@example.com',
            'mobile_no' => '081200000001',
            'password' => Hash::make('secret-pass'),
        ]);

        $token = $resident->createToken('test')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/resident/me')
            ->assertOk()
            ->assertJsonPath('data.id', $resident->id)
            ->assertJsonPath('data.email', $resident->email)
            ->assertJsonMissingPath('data.password');
    }

    public function test_logout_revokes_only_current_access_token(): void
    {
        $resident = $this->createResident([
            'email' => 'logout@example.com',
            'mobile_no' => '081200000002',
            'password' => Hash::make('secret-pass'),
        ]);

        $currentToken = $resident->createToken('current')->plainTextToken;
        $otherToken = $resident->createToken('other')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$currentToken)
            ->postJson('/api/resident/logout')
            ->assertOk()
            ->assertJsonPath('message', 'Logout resident berhasil.');

        $resident->refresh();

        $this->assertCount(1, $resident->tokens);
        $this->assertSame('other', $resident->tokens->first()->name);

        $this->withHeader('Authorization', 'Bearer '.$otherToken)
            ->getJson('/api/resident/me')
            ->assertOk()
            ->assertJsonPath('data.id', $resident->id);
    }

    private function createResident(array $overrides = []): Resident
    {
        $unit = Unit::query()->create([
            'code' => 'A-1201-'.str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT),
            'tower' => 'Tower A',
            'floor' => 12,
            'unit_type' => '2BR',
            'occupancy_status' => 'Terisi',
            'payment_status' => 'Lunas',
            'thumbnail_tone' => 'default',
        ]);

        return Resident::query()->create(array_merge([
            'unit_id' => $unit->id,
            'name' => 'Resident API',
            'email' => 'resident+'.uniqid().'@example.com',
            'mobile_no' => '0812'.str_pad((string) random_int(1, 99999999), 8, '0', STR_PAD_LEFT),
            'password' => Hash::make('secret-pass'),
            'resident_type' => 'Penyewa',
            'status' => 'Aktif',
            'move_in_date' => '2026-06-01',
            'contract_end_date' => '2027-06-01',
            'avatar_tone' => 'default',
        ], $overrides));
    }
}
