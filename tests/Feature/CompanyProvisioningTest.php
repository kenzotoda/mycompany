<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use App\Services\CompanyProvisioningService;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyProvisioningTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_creates_company_for_user(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $response = $this->post('/register', [
            'name' => 'KONA',
            'email' => 'kona@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect(route('dashboard', absolute: false));

        $user = User::query()->where('email', 'kona@example.com')->first();

        $this->assertNotNull($user->company_id);
        $this->assertDatabaseHas('companies', [
            'id' => $user->company_id,
            'name' => 'KONA',
        ]);
        $this->assertTrue($user->hasRole('admin'));
    }

    public function test_existing_user_without_company_is_provisioned_on_request(): void
    {
        $user = User::factory()->create([
            'company_id' => null,
        ]);

        $this->actingAs($user)
            ->get(route('suppliers.index'))
            ->assertOk();

        $user->refresh();

        $this->assertNotNull($user->company_id);
        $this->assertSame(1, Company::count());
    }
}
