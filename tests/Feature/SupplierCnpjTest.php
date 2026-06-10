<?php

namespace Tests\Feature;

use App\Livewire\Suppliers\SupplierIndex;
use App\Models\Company;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SupplierCnpjTest extends TestCase
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

    public function test_supplier_can_be_saved_with_formatted_cnpj(): void
    {
        $this->actingAs($this->userWithCompany());

        Livewire::test(SupplierIndex::class)
            ->set('name', 'Fornecedor Teste')
            ->set('document', '11.222.333/0001-81')
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('name', '');

        $this->assertDatabaseHas('suppliers', [
            'name' => 'Fornecedor Teste',
            'document' => '11222333000181',
        ]);
    }

    public function test_supplier_can_be_saved_with_unformatted_cnpj_digits(): void
    {
        $this->actingAs($this->userWithCompany());

        Livewire::test(SupplierIndex::class)
            ->set('name', 'Fornecedor Teste')
            ->set('document', '11222333000181')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('suppliers', [
            'document' => '11222333000181',
        ]);
    }

    public function test_supplier_rejects_incomplete_cnpj(): void
    {
        $this->actingAs($this->userWithCompany());

        Livewire::test(SupplierIndex::class)
            ->set('name', 'Fornecedor Teste')
            ->set('document', '1122233300018')
            ->call('save')
            ->assertHasErrors(['document']);
    }

    public function test_supplier_rejects_duplicate_cnpj(): void
    {
        $user = $this->userWithCompany();

        Supplier::create([
            'company_id' => $user->company_id,
            'name' => 'Existente',
            'document' => '11222333000181',
        ]);

        Livewire::actingAs($user)
            ->test(SupplierIndex::class)
            ->set('name', 'Outro')
            ->set('document', '11.222.333/0001-81')
            ->call('save')
            ->assertHasErrors(['document']);
    }

    public function test_supplier_can_be_updated(): void
    {
        $user = $this->userWithCompany();

        $supplier = Supplier::create([
            'company_id' => $user->company_id,
            'name' => 'Fornecedor antigo',
            'document' => '11222333000181',
            'phone' => '(11) 98765-4321',
            'email' => 'fornecedor@teste.com',
        ]);

        Livewire::actingAs($user)
            ->test(SupplierIndex::class)
            ->call('edit', $supplier->id)
            ->assertSet('editingId', $supplier->id)
            ->assertSet('name', 'Fornecedor antigo')
            ->assertSet('document', '11222333000181')
            ->assertSet('phone', '(11) 98765-4321')
            ->assertSet('email', 'fornecedor@teste.com')
            ->set('name', 'Fornecedor novo')
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('editingId', null);

        $this->assertDatabaseHas('suppliers', [
            'id' => $supplier->id,
            'name' => 'Fornecedor novo',
            'document' => '11222333000181',
        ]);
    }

    public function test_supplier_can_be_deleted(): void
    {
        $user = $this->userWithCompany();

        $supplier = Supplier::create([
            'company_id' => $user->company_id,
            'name' => 'Fornecedor removível',
            'document' => '11222333000181',
        ]);

        Livewire::actingAs($user)
            ->test(SupplierIndex::class)
            ->call('delete', $supplier->id)
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('suppliers', [
            'id' => $supplier->id,
        ]);
    }
}
