<?php

namespace Tests\Feature;

use App\Models\Site;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProgramManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_research_program(): void
    {
        $user = User::factory()->create();
        $site = Site::factory()->create();
        $user->sites()->attach($site);

        $response = $this->actingAs($user)->post(route('programs.store', $site->site_code), [
            'classification' => 'PROGRAM',
            'program_code' => 'RES-001',
            'program_name' => 'Research 1',
            'fiscal_year' => 2026,
            'total_budget' => 0,
            'status' => 'DRAFT',
            'program_category' => 'RESEARCH',
            'background_text' => 'Test Background',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('programs', [
            'program_code' => 'RES-001',
            'classification' => 'PROGRAM',
            'background_text' => 'Test Background',
        ]);
    }

    public function test_can_create_non_program_activity(): void
    {
        $user = User::factory()->create();
        $site = Site::factory()->create();
        $user->sites()->attach($site);

        $response = $this->actingAs($user)->post(route('programs.store', $site->site_code), [
            'classification' => 'NON_PROGRAM',
            'program_code' => 'ACT-001',
            'program_name' => 'Activity 1',
            'fiscal_year' => 2026,
            'total_budget' => 0,
            'status' => 'DRAFT',
            'non_program_category' => 'Infrastructure',
            'description' => 'Test Description',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('programs', [
            'program_code' => 'ACT-001',
            'classification' => 'NON_PROGRAM',
            'non_program_category' => 'Infrastructure',
        ]);
    }
}
