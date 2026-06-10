<?php

namespace Tests\Feature;

use App\Livewire\Customers\CustomerIndex;
use App\Models\Company;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CustomerDocumentTest extends TestCase
{
    use RefreshDatabase;

    private function userWithCompany(): User
    {
        $company = Company::create([
            'name' => 'Empresa Teste',
            'trade_name' => 'Teste',
            'document' => '00.000.000/0001-00',
            'email' => 'teste@local.test',
            'phone' => '(11) 99999-9999',
        ]);

        return User::factory()->create([
            'company_id' => $company->id,
        ]);
    }

    public function test_customer_can_be_saved_with_formatted_cpf_and_phone(): void
    {
        $this->actingAs($this->userWithCompany());

        Livewire::test(CustomerIndex::class)
            ->set('name', 'Cliente Teste')
            ->set('document_type', 'cpf')
            ->set('document', '123.456.789-09')
            ->set('phone', '(11) 98765-4321')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('customers', [
            'name' => 'Cliente Teste',
            'document' => '123.456.789-09',
            'phone' => '(11) 98765-4321',
        ]);
    }

    public function test_customer_can_be_saved_with_unformatted_cnpj_digits(): void
    {
        $this->actingAs($this->userWithCompany());

        Livewire::test(CustomerIndex::class)
            ->set('name', 'Empresa Cliente')
            ->set('document_type', 'cnpj')
            ->set('document', '11222333000181')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('customers', [
            'document' => '11.222.333/0001-81',
        ]);
    }

    public function test_customer_rejects_incomplete_cpf(): void
    {
        $this->actingAs($this->userWithCompany());

        Livewire::test(CustomerIndex::class)
            ->set('name', 'Cliente Teste')
            ->set('document_type', 'cpf')
            ->set('document', '123.456.789-0')
            ->call('save')
            ->assertHasErrors(['document']);
    }

    public function test_customer_rejects_incomplete_phone(): void
    {
        $this->actingAs($this->userWithCompany());

        Livewire::test(CustomerIndex::class)
            ->set('name', 'Cliente Teste')
            ->set('phone', '(11) 9999')
            ->call('save')
            ->assertHasErrors(['phone']);
    }

    public function test_customer_rejects_duplicate_document(): void
    {
        $user = $this->userWithCompany();

        Customer::create([
            'company_id' => $user->company_id,
            'name' => 'Existente',
            'document' => '123.456.789-09',
        ]);

        Livewire::actingAs($user)
            ->test(CustomerIndex::class)
            ->set('name', 'Outro')
            ->set('document_type', 'cpf')
            ->set('document', '123.456.789-09')
            ->call('save')
            ->assertHasErrors(['document']);
    }

    public function test_customer_can_be_updated(): void
    {
        $user = $this->userWithCompany();

        $customer = Customer::create([
            'company_id' => $user->company_id,
            'name' => 'Cliente antigo',
            'document' => '123.456.789-09',
            'phone' => '(11) 91234-5678',
            'email' => 'cliente@teste.com',
        ]);

        Livewire::actingAs($user)
            ->test(CustomerIndex::class)
            ->call('edit', $customer->id)
            ->assertSet('editingId', $customer->id)
            ->assertSet('document_type', 'cpf')
            ->assertSet('name', 'Cliente antigo')
            ->assertSet('document', '123.456.789-09')
            ->assertSet('phone', '(11) 91234-5678')
            ->assertSet('email', 'cliente@teste.com')
            ->set('name', 'Cliente novo')
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('editingId', null);

        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'name' => 'Cliente novo',
            'document' => '123.456.789-09',
        ]);
    }

    public function test_customer_can_be_deleted(): void
    {
        $user = $this->userWithCompany();

        $customer = Customer::create([
            'company_id' => $user->company_id,
            'name' => 'Cliente removível',
            'document' => '123.456.789-09',
        ]);

        Livewire::actingAs($user)
            ->test(CustomerIndex::class)
            ->call('delete', $customer->id)
            ->assertHasNoErrors();

        $this->assertSoftDeleted('customers', [
            'id' => $customer->id,
        ]);
    }
}
