<?php

namespace Tests\Feature;

use App\Livewire\Products\ProductForm;
use App\Livewire\Products\ProductIndex;
use App\Models\Company;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProductFormTest extends TestCase
{
    use RefreshDatabase;

    private function userWithCompany(): User
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $company = Company::create([
            'name' => 'Empresa Teste',
            'trade_name' => 'Teste',
            'document' => '00.000.000/0001-00',
            'email' => 'teste@local.test',
            'phone' => '(11) 99999-9999',
        ]);

        return User::factory()->create([
            'company_id' => $company->id,
        ])->assignRole('admin');
    }

    public function test_product_can_be_saved_with_name(): void
    {
        $this->actingAs($this->userWithCompany());

        Livewire::test(ProductForm::class)
            ->set('name', 'Camiseta básica')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('products.index', absolute: false));

        $this->assertDatabaseHas('products', [
            'name' => 'Camiseta básica',
        ]);
    }

    public function test_product_can_be_updated(): void
    {
        $user = $this->userWithCompany();
        $supplier = Supplier::create([
            'company_id' => $user->company_id,
            'name' => 'Fornecedor A',
            'document' => '11222333000181',
        ]);

        $product = Product::create([
            'company_id' => $user->company_id,
            'name' => 'Produto antigo',
            'sku' => 'SKU-TESTE01',
        ]);

        Livewire::actingAs($user)
            ->test(ProductForm::class, ['productId' => $product->id])
            ->set('name', 'Produto novo')
            ->set('sku', 'SKU-NOVO01')
            ->set('supplier_id', $supplier->id)
            ->set('stock_quantity', '5')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('products.index', absolute: false));

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Produto novo',
            'sku' => 'SKU-NOVO01',
            'supplier_id' => $supplier->id,
            'stock_quantity' => 5,
        ]);
    }

    public function test_product_rejects_empty_name(): void
    {
        $this->actingAs($this->userWithCompany());

        Livewire::test(ProductForm::class)
            ->set('name', '')
            ->call('save')
            ->assertHasErrors(['name']);

        $this->assertSame(0, Product::count());
    }

    public function test_product_index_search_is_case_insensitive(): void
    {
        $user = $this->userWithCompany();

        Product::create([
            'company_id' => $user->company_id,
            'name' => 'Camiseta Premium',
            'sku' => 'SKU-PREM01',
        ]);

        Livewire::actingAs($user)
            ->test(ProductIndex::class)
            ->set('query', 'camiseta')
            ->assertSee('Camiseta Premium')
            ->set('query', 'PREM01')
            ->assertSee('Camiseta Premium');
    }

    public function test_product_can_be_deleted_from_index(): void
    {
        $user = $this->userWithCompany();

        $product = Product::create([
            'company_id' => $user->company_id,
            'name' => 'Para excluir',
            'sku' => 'SKU-DEL001',
        ]);

        Livewire::actingAs($user)
            ->test(ProductIndex::class)
            ->call('delete', $product->id)
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
    }
}
