<?php

namespace Tests\Feature;

use App\Models\BottleType;
use App\Models\ExchangeTransaction;
use App\Models\ExchangeTransactionDetail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminBottleTypeTest extends TestCase
{
    use RefreshDatabase, CreatesTestFixtures;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpFixtures();
    }

    public function test_index_lists_bottle_types()
    {
        $response = $this->actingAs($this->adminUser)->get('/admin/bottle-types');
        $response->assertStatus(200);
        $response->assertViewIs('admin.bottle_types.index');
        $response->assertViewHas('bottleTypes');
        $response->assertSee('Botol PET Aqua 600ml');
        $response->assertSee('Botol PET Coca-Cola 390ml');
    }

    public function test_create_form_renders()
    {
        $response = $this->actingAs($this->adminUser)->get('/admin/bottle-types/create');
        $response->assertStatus(200);
        $response->assertViewIs('admin.bottle_types.create');
    }

    public function test_store_creates_bottle_type_and_redirects()
    {
        $response = $this->actingAs($this->adminUser)->post('/admin/bottle-types', [
            'name'         => 'Botol PET Le Minerale 600ml',
            'barcode'      => '8997002120015',
            'description'  => 'Botol air mineral Le Minerale PET bening 600ml',
            'points_value' => 10,
        ]);

        $response->assertRedirect(route('admin.bottle-types.index'));
        $this->assertDatabaseHas('bottle_types', [
            'barcode' => '8997002120015',
            'name'    => 'Botol PET Le Minerale 600ml',
        ]);
    }

    public function test_store_with_duplicate_barcode_fails()
    {
        $response = $this->actingAs($this->adminUser)->post('/admin/bottle-types', [
            'name'         => 'Duplikat Barcode',
            'barcode'      => '11111',
            'points_value' => 5,
        ]);

        $response->assertSessionHasErrors('barcode');
    }

    public function test_store_with_missing_required_fields_fails()
    {
        $response = $this->actingAs($this->adminUser)->post('/admin/bottle-types', [
            'name'         => '',
            'barcode'      => '',
            'points_value' => '',
        ]);

        $response->assertSessionHasErrors(['name', 'barcode', 'points_value']);
    }

    public function test_store_with_zero_points_fails()
    {
        $response = $this->actingAs($this->adminUser)->post('/admin/bottle-types', [
            'name'         => 'Botol Baru',
            'barcode'      => '9999999999999',
            'points_value' => 0,
        ]);

        $response->assertSessionHasErrors('points_value');
    }

    public function test_edit_form_renders()
    {
        $response = $this->actingAs($this->adminUser)->get("/admin/bottle-types/{$this->bottle1->id}/edit");
        $response->assertStatus(200);
        $response->assertViewIs('admin.bottle_types.edit');
        $response->assertViewHas('bottleType');
    }

    public function test_update_bottle_type()
    {
        $response = $this->actingAs($this->adminUser)->put("/admin/bottle-types/{$this->bottle1->id}", [
            'name'         => 'Botol PET Updated 500ml',
            'barcode'      => $this->bottle1->barcode,
            'description'  => 'Deskripsi diperbarui',
            'points_value' => 15,
        ]);

        $response->assertRedirect(route('admin.bottle-types.index'));
        $this->assertDatabaseHas('bottle_types', [
            'id'           => $this->bottle1->id,
            'name'         => 'Botol PET Updated 500ml',
            'points_value' => 15,
        ]);
    }

    public function test_update_with_duplicate_barcode_fails()
    {
        $response = $this->actingAs($this->adminUser)->put("/admin/bottle-types/{$this->bottle2->id}", [
            'name'         => 'Botol Renamed',
            'barcode'      => '11111',
            'points_value' => 10,
        ]);

        $response->assertSessionHasErrors('barcode');
    }

    public function test_destroy_bottle_type()
    {
        $bottle = BottleType::create([
            'name'         => 'Botol Baru to Delete',
            'barcode'      => '9998887776665',
            'description'  => 'Ready to be deleted',
            'points_value' => 20,
        ]);

        $response = $this->actingAs($this->adminUser)->delete("/admin/bottle-types/{$bottle->id}");
        $response->assertRedirect(route('admin.bottle-types.index'));
        $this->assertDatabaseMissing('bottle_types', ['id' => $bottle->id]);
    }

    public function test_destroy_bottle_type_used_in_transaction_fails()
    {
        $bottle = BottleType::create([
            'name'         => 'Botol Baru Used in Tx',
            'barcode'      => '9998887776664',
            'description'  => 'Will be used in a transaction',
            'points_value' => 10,
        ]);

        $transaction = ExchangeTransaction::create([
            'user_id'       => $this->normalUser->id,
            'employee_id'   => $this->employeeUser->id,
            'total_points'  => 10,
            'transacted_at' => now(),
        ]);

        ExchangeTransactionDetail::create([
            'transaction_id'  => $transaction->id,
            'bottle_type_id'  => $bottle->id,
            'quantity'        => 1,
            'points_earned'   => 10,
        ]);

        $response = $this->actingAs($this->adminUser)->delete("/admin/bottle-types/{$bottle->id}");
        $response->assertRedirect(route('admin.bottle-types.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('bottle_types', ['id' => $bottle->id]);
    }

    public function test_non_admin_cannot_access_bottle_type_management()
    {
        $routes = [
            'GET /admin/bottle-types',
            'GET /admin/bottle-types/create',
            'POST /admin/bottle-types',
            'GET /admin/bottle-types/1/edit',
            'PUT /admin/bottle-types/1',
            'DELETE /admin/bottle-types/1',
        ];

        foreach ($routes as $route) {
            $method = explode(' ', $route)[0];
            $path = explode(' ', $route)[1];

            $response = $this->actingAs($this->employeeUser)->call($method, $path);
            $response->assertStatus(403);
        }
    }
}
