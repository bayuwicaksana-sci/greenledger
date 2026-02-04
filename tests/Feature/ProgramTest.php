<?php

namespace Tests\Feature;

use App\Models\Program;
use App\Models\Site;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProgramTest extends TestCase
{
    use RefreshDatabase;

    protected $site;

    protected $user;

    protected $manager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->site = Site::factory()->create(['site_code' => 'KLT']);

        $this->manager = User::factory()->create([
            'primary_site_id' => $this->site->id,
        ]);

        $this->app
            ->make(\Spatie\Permission\PermissionRegistrar::class)
            ->forgetCachedPermissions();

        $permissions = [
            'programs.view.site',
            'programs.view.all',
            'programs.view.own',
            'programs.view.assigned',
            'programs.create.site',
            'programs.create.all',
            'programs.update.site',
            'programs.delete.site',
            'programs.archive.view',
        ];

        foreach ($permissions as $perm) {
            \Spatie\Permission\Models\Permission::firstOrCreate([
                'name' => $perm,
            ]);
        }

        $this->manager->givePermissionTo([
            'programs.view.site',
            'programs.create.site',
            'programs.update.site',
            'programs.delete.site',
            'programs.archive.view',
        ]);
    }

    public function test_manager_can_view_program_list()
    {
        Program::factory()->create([
            'site_id' => $this->site->id,
            'program_name' => 'Test Program KLT',
            'status' => Program::STATUS_ACTIVE,
        ]);

        $response = $this->actingAs($this->manager)->get(
            route('programs.index', $this->site->site_code),
        );

        $response->assertStatus(200);
        $response->assertInertia(
            fn ($page) => $page
                ->component('programs/index')
                ->has('programs', 1)
                ->where('programs.0.program_name', 'Test Program KLT'),
        );
    }

    public function test_programs_are_scoped_to_site()
    {
        $otherSite = Site::factory()->create(['site_code' => 'MGL']);
        Program::factory()->create([
            'site_id' => $otherSite->id,
            'program_name' => 'Test Program MGL',
            'status' => Program::STATUS_ACTIVE,
        ]);

        Program::factory()->create([
            'site_id' => $this->site->id,
            'program_name' => 'Test Program KLT',
            'status' => Program::STATUS_ACTIVE,
        ]);

        $response = $this->actingAs($this->manager)->get(
            route('programs.index', $this->site->site_code),
        );

        $response->assertStatus(200);
        $response->assertInertia(
            fn ($page) => $page
                ->has('programs', 1)
                ->where('programs.0.program_name', 'Test Program KLT'),
        );
    }

    public function test_manager_can_create_program()
    {
        $data = [
            'program_code' => 'KLT-2026-001',
            'program_name' => 'New Program',
            'fiscal_year' => 2026,
            'total_budget' => 1000000,
            'status' => 'DRAFT',
        ];

        $response = $this->actingAs($this->manager)->post(
            route('programs.store', $this->site->site_code),
            $data,
        );

        $response->assertRedirect(
            route('programs.index', $this->site->site_code),
        );

        $this->assertDatabaseHas('programs', [
            'program_code' => 'KLT-2026-001',
            'program_name' => 'New Program',
            'site_id' => $this->site->id,
        ]);
    }

    public function test_program_code_must_be_unique_within_site()
    {
        Program::factory()->create([
            'site_id' => $this->site->id,
            'program_code' => 'DUPLICATE',
        ]);

        $data = [
            'program_code' => 'DUPLICATE',
            'program_name' => 'New Program',
            'fiscal_year' => 2026,
            'total_budget' => 1000000,
            'status' => 'DRAFT',
        ];

        $response = $this->actingAs($this->manager)->post(
            route('programs.store', $this->site->site_code),
            $data,
        );

        $response->assertSessionHasErrors(['program_code']);
    }

    public function test_program_code_can_be_duplicated_across_sites()
    {
        $otherSite = Site::factory()->create(['site_code' => 'MGL']);
        Program::factory()->create([
            'site_id' => $otherSite->id,
            'program_code' => 'DUPLICATE',
        ]);

        $data = [
            'program_code' => 'DUPLICATE',
            'program_name' => 'New Program',
            'fiscal_year' => 2026,
            'total_budget' => 1000000,
            'status' => 'DRAFT',
        ];

        $response = $this->actingAs($this->manager)->post(
            route('programs.store', $this->site->site_code),
            $data,
        );

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('programs', [
            'program_code' => 'DUPLICATE',
            'site_id' => $this->site->id,
        ]);
    }

    public function test_archived_programs_are_hidden_from_index()
    {
        Program::factory()->draft()->create([
            'site_id' => $this->site->id,
            'program_name' => 'Draft Program',
        ]);

        Program::factory()->archived()->create([
            'site_id' => $this->site->id,
            'program_name' => 'Archived Program',
        ]);

        $response = $this->actingAs($this->manager)->get(
            route('programs.index', $this->site->site_code),
        );

        $response->assertInertia(
            fn ($page) => $page
                ->has('programs', 1)
                ->where('programs.0.program_name', 'Draft Program'),
        );
    }

    public function test_show_route_returns_program_details()
    {
        $program = Program::factory()->create([
            'site_id' => $this->site->id,
            'program_name' => 'Shown Program',
        ]);

        $response = $this->actingAs($this->manager)->get(
            route('programs.show', [$this->site->site_code, $program->id]),
        );

        $response->assertStatus(200);
        $response->assertInertia(
            fn ($page) => $page
                ->component('programs/show')
                ->where('program.program_name', 'Shown Program'),
        );
    }

    public function test_update_does_not_fail_when_code_unchanged()
    {
        $program = Program::factory()->draft()->create([
            'site_id' => $this->site->id,
            'program_code' => 'UNCHANGED',
        ]);

        $response = $this->actingAs($this->manager)->put(
            route('programs.update', [$this->site->site_code, $program->id]),
            [
                'program_code' => 'UNCHANGED',
                'program_name' => 'Updated Name',
                'fiscal_year' => 2026,
                'total_budget' => 500000,
                'status' => 'DRAFT',
            ],
        );

        $response->assertRedirect(
            route('programs.index', $this->site->site_code),
        );
    }

    public function test_store_always_creates_program_as_draft()
    {
        $data = [
            'program_code' => 'FORCE-DRAFT',
            'program_name' => 'Should Be Draft',
            'fiscal_year' => 2026,
            'total_budget' => 100000,
            'status' => 'ACTIVE',
        ];

        $this->actingAs($this->manager)->post(
            route('programs.store', $this->site->site_code),
            $data,
        );

        $this->assertDatabaseHas('programs', [
            'program_code' => 'FORCE-DRAFT',
            'status' => 'DRAFT',
        ]);
    }

    public function test_invalid_status_transition_is_rejected()
    {
        $program = Program::factory()->draft()->create([
            'site_id' => $this->site->id,
        ]);

        $response = $this->actingAs($this->manager)->put(
            route('programs.update', [$this->site->site_code, $program->id]),
            [
                'program_code' => $program->program_code,
                'program_name' => $program->program_name,
                'fiscal_year' => $program->fiscal_year,
                'total_budget' => $program->total_budget,
                'status' => 'COMPLETED',
            ],
        );

        $response->assertSessionHasErrors(['status']);
        $this->assertDatabaseHas('programs', [
            'id' => $program->id,
            'status' => 'DRAFT',
        ]);
    }

    public function test_valid_status_transition_draft_to_active()
    {
        $program = Program::factory()->draft()->create([
            'site_id' => $this->site->id,
        ]);

        $response = $this->actingAs($this->manager)->put(
            route('programs.update', [$this->site->site_code, $program->id]),
            [
                'program_code' => $program->program_code,
                'program_name' => $program->program_name,
                'fiscal_year' => $program->fiscal_year,
                'total_budget' => $program->total_budget,
                'status' => 'ACTIVE',
            ],
        );

        $response->assertRedirect(
            route('programs.index', $this->site->site_code),
        );

        $this->assertDatabaseHas('programs', [
            'id' => $program->id,
            'status' => 'ACTIVE',
        ]);
    }

    public function test_archived_page_returns_only_archived_programs()
    {
        Program::factory()->active()->create([
            'site_id' => $this->site->id,
            'program_name' => 'Active Program',
        ]);

        Program::factory()->archived()->create([
            'site_id' => $this->site->id,
            'program_name' => 'Archived Program',
        ]);

        $response = $this->actingAs($this->manager)->get(
            route('programs.archived', $this->site->site_code),
        );

        $response->assertStatus(200);
        $response->assertInertia(
            fn ($page) => $page
                ->component('programs/archived')
                ->has('programs', 1)
                ->where('programs.0.program_name', 'Archived Program'),
        );
    }
}
