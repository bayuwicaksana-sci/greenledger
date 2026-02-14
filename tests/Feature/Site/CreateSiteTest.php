<?php

namespace Tests\Feature\Site;

use App\Models\Site;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateSiteTest extends TestCase
{
    use RefreshDatabase;

    protected $manager;

    protected $researchOfficer;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles and permissions
        $this->seed(RolePermissionSeeder::class);

        $this->app
            ->make(\Spatie\Permission\PermissionRegistrar::class)
            ->forgetCachedPermissions();

        // Create Manager user with sites.create permission
        $this->manager = User::factory()->create();
        $this->manager->assignRole('Manager');

        // Create Research Officer user without sites.create permission
        $this->researchOfficer = User::factory()->create();
        $this->researchOfficer->assignRole('Research Officer');
    }

    // Authorization Tests

    public function test_manager_can_view_create_form(): void
    {
        $response = $this->actingAs($this->manager)->get(
            route('research-stations.create'),
        );

        $response->assertStatus(200);
        $response->assertInertia(
            fn ($page) => $page->component('research-stations/create'),
        );
    }

    public function test_user_without_permission_cannot_view_create_form(): void
    {
        $response = $this->actingAs($this->researchOfficer)->get(
            route('research-stations.create'),
        );

        $response->assertStatus(403);
    }

    public function test_manager_can_create_site_with_valid_data(): void
    {
        $data = [
            'site_code' => 'KLA',
            'site_name' => 'Klaten Research Station',
            'address' => 'Jl. Research No. 123, Klaten',
            'phone' => '+62 271 123456',
            'email' => 'klaten@example.com',
            'is_active' => true,
        ];

        $response = $this->actingAs($this->manager)->post(
            route('research-stations.store'),
            $data,
        );

        $response->assertRedirect(route('research-stations.index'));
        $response->assertSessionHas('success', 'Site created successfully.');

        $this->assertDatabaseHas('sites', [
            'site_code' => 'KLA',
            'site_name' => 'Klaten Research Station',
            'address' => 'Jl. Research No. 123, Klaten',
        ]);

        // Verify contact_info JSON structure
        $site = Site::where('site_code', 'KLA')->first();
        expect($site->contact_info)->toBeArray();
        expect($site->contact_info['phone'])->toBe('+62 271 123456');
        expect($site->contact_info['email'])->toBe('klaten@example.com');
    }

    public function test_user_without_permission_cannot_create_site(): void
    {
        $data = [
            'site_code' => 'KLA',
            'site_name' => 'Klaten Research Station',
            'address' => 'Jl. Research No. 123, Klaten',
            'phone' => '+62 271 123456',
            'email' => 'klaten@example.com',
        ];

        $response = $this->actingAs($this->researchOfficer)->post(
            route('research-stations.store'),
            $data,
        );

        $response->assertStatus(403);
    }

    // Validation Tests

    public function test_site_code_must_be_exactly_three_uppercase_letters(): void
    {
        // Test with short code (2 letters)
        $data = [
            'site_code' => 'KL',
            'site_name' => 'Klaten Research Station',
            'address' => 'Jl. Research No. 123',
            'phone' => '+62 271 123456',
            'email' => 'klaten@example.com',
        ];

        $response = $this->actingAs($this->manager)->post(
            route('research-stations.store'),
            $data,
        );

        $response->assertSessionHasErrors(['site_code']);

        // Test with long code (4 letters)
        $data['site_code'] = 'KLAN';

        $response = $this->actingAs($this->manager)->post(
            route('research-stations.store'),
            $data,
        );

        $response->assertSessionHasErrors(['site_code']);

        // Test with numbers
        $data['site_code'] = 'K12';

        $response = $this->actingAs($this->manager)->post(
            route('research-stations.store'),
            $data,
        );

        $response->assertSessionHasErrors(['site_code']);
    }

    public function test_site_code_is_auto_uppercased(): void
    {
        $data = [
            'site_code' => 'kla',
            'site_name' => 'Klaten Research Station',
            'address' => 'Jl. Research No. 123, Klaten',
            'phone' => '+62 271 123456',
            'email' => 'klaten@example.com',
        ];

        $response = $this->actingAs($this->manager)->post(
            route('research-stations.store'),
            $data,
        );

        $response->assertRedirect(route('research-stations.index'));

        $this->assertDatabaseHas('sites', [
            'site_code' => 'KLA',
            'site_name' => 'Klaten Research Station',
        ]);
    }

    public function test_site_code_must_be_unique(): void
    {
        Site::factory()->create(['site_code' => 'KLA']);

        $data = [
            'site_code' => 'KLA',
            'site_name' => 'Another Klaten Station',
            'address' => 'Jl. Research No. 456, Klaten',
            'phone' => '+62 271 789012',
            'email' => 'klaten2@example.com',
        ];

        $response = $this->actingAs($this->manager)->post(
            route('research-stations.store'),
            $data,
        );

        $response->assertSessionHasErrors(['site_code']);
    }

    public function test_all_required_fields_are_validated(): void
    {
        $response = $this->actingAs($this->manager)->post(
            route('research-stations.store'),
            [],
        );

        $response->assertSessionHasErrors([
            'site_code',
            'site_name',
            'address',
            'contact_info.phone',
            'contact_info.email',
        ]);
    }

    public function test_email_must_be_valid_format(): void
    {
        $data = [
            'site_code' => 'KLA',
            'site_name' => 'Klaten Research Station',
            'address' => 'Jl. Research No. 123, Klaten',
            'phone' => '+62 271 123456',
            'email' => 'not-an-email',
        ];

        $response = $this->actingAs($this->manager)->post(
            route('research-stations.store'),
            $data,
        );

        $response->assertSessionHasErrors(['contact_info.email']);
    }

    // Business Logic Tests

    public function test_contact_info_constructed_correctly_from_phone_and_email(): void
    {
        $data = [
            'site_code' => 'KLA',
            'site_name' => 'Klaten Research Station',
            'address' => 'Jl. Research No. 123, Klaten',
            'phone' => '+62 271 123456',
            'email' => 'klaten@example.com',
        ];

        $this->actingAs($this->manager)->post(
            route('research-stations.store'),
            $data,
        );

        $site = Site::where('site_code', 'KLA')->first();

        expect($site)->not->toBeNull();
        expect($site->contact_info)->toBeArray();
        expect($site->contact_info)->toHaveKey('phone');
        expect($site->contact_info)->toHaveKey('email');
        expect($site->contact_info['phone'])->toBe('+62 271 123456');
        expect($site->contact_info['email'])->toBe('klaten@example.com');
    }

    public function test_is_active_defaults_to_true_if_not_provided(): void
    {
        $data = [
            'site_code' => 'KLA',
            'site_name' => 'Klaten Research Station',
            'address' => 'Jl. Research No. 123, Klaten',
            'phone' => '+62 271 123456',
            'email' => 'klaten@example.com',
            // is_active not provided
        ];

        $this->actingAs($this->manager)->post(
            route('research-stations.store'),
            $data,
        );

        $site = Site::where('site_code', 'KLA')->first();

        expect($site->is_active)->toBeTrue();
    }

    public function test_successful_creation_redirects_to_index_with_success_message(): void
    {
        $data = [
            'site_code' => 'KLA',
            'site_name' => 'Klaten Research Station',
            'address' => 'Jl. Research No. 123, Klaten',
            'phone' => '+62 271 123456',
            'email' => 'klaten@example.com',
        ];

        $response = $this->actingAs($this->manager)->post(
            route('research-stations.store'),
            $data,
        );

        $response->assertRedirect(route('research-stations.index'));
        $response->assertSessionHas('success', 'Site created successfully.');
    }

    public function test_successful_creation_persists_all_data_correctly(): void
    {
        $data = [
            'site_code' => 'YOG',
            'site_name' => 'Yogyakarta Research Station',
            'address' => 'Jl. Kaliurang KM 15, Sleman',
            'phone' => '+62 274 567890',
            'email' => 'yogyakarta@example.com',
            'is_active' => false,
        ];

        $this->actingAs($this->manager)->post(
            route('research-stations.store'),
            $data,
        );

        $this->assertDatabaseHas('sites', [
            'site_code' => 'YOG',
            'site_name' => 'Yogyakarta Research Station',
            'address' => 'Jl. Kaliurang KM 15, Sleman',
            'is_active' => false,
        ]);

        $site = Site::where('site_code', 'YOG')->first();
        expect($site->contact_info['phone'])->toBe('+62 274 567890');
        expect($site->contact_info['email'])->toBe('yogyakarta@example.com');
    }

    // Suggest Code Endpoint Tests

    public function test_suggest_code_generates_code_from_site_name(): void
    {
        $response = $this->actingAs($this->manager)->get(
            route('research-stations.suggest-code', [
                'site_name' => 'Klaten Research Station',
            ]),
        );

        $response->assertStatus(200);
        $response->assertJson([
            'suggested_code' => 'KRS',
            'is_available' => true,
        ]);
    }

    public function test_suggest_code_returns_availability_status(): void
    {
        // Create existing site
        Site::factory()->create(['site_code' => 'KRS']);

        $response = $this->actingAs($this->manager)->get(
            route('research-stations.suggest-code', [
                'site_name' => 'Klaten Research Station',
            ]),
        );

        $response->assertStatus(200);
        $response->assertJson([
            'suggested_code' => 'KRS',
            'is_available' => false,
        ]);
    }

    public function test_suggest_code_indicates_unavailability_if_code_exists(): void
    {
        Site::factory()->create(['site_code' => 'YOG']);

        $response = $this->actingAs($this->manager)->get(
            route('research-stations.suggest-code', [
                'site_name' => 'Yogyakarta',
            ]),
        );

        $response->assertStatus(200);
        $response->assertJson([
            'suggested_code' => 'YOG',
            'is_available' => false,
        ]);
    }

    public function test_suggest_code_handles_single_word_site_name(): void
    {
        $response = $this->actingAs($this->manager)->get(
            route('research-stations.suggest-code', [
                'site_name' => 'Yogyakarta',
            ]),
        );

        $response->assertStatus(200);
        $response->assertJson([
            'suggested_code' => 'YOG',
            'is_available' => true,
        ]);
    }

    public function test_suggest_code_pads_with_x_if_less_than_three_characters(): void
    {
        $response = $this->actingAs($this->manager)->get(
            route('research-stations.suggest-code', [
                'site_name' => 'AB',
            ]),
        );

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'suggested_code' => 'ABX',
        ]);
    }
}
