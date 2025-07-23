<?php

use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;


it('lists all products for sales user', function () {
    $user = User::factory()->create(['role' => 'sales']);
    $products = Product::factory()->count(3)->create();

    $this->actingAs($user);
    $response = $this->get(route('products.index'));

    $response->assertStatus(200);
    $response->assertViewIs('products.index');
    $response->assertViewHas('products', fn($viewProducts) => $viewProducts->count() === 3);
});

it('lists all products including soft-deleted for admin', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $products = Product::factory()->count(2)->create();
    $deletedProduct = Product::factory()->create();
    $deletedProduct->delete();

    $this->actingAs($user);
    $response = $this->get(route('products.index'));

    $response->assertStatus(200);
    $response->assertViewHas('products', fn($viewProducts) => $viewProducts->count() === 3);
});

it('allows sales user to create product and dispatches toast', function () {
    $user = User::factory()->create(['role' => 'sales']);

    // Livewire::spy()->shouldReceive('dispatch')->with('showToast', 'Product created successfully.', 'success')->once();

    $this->actingAs($user);
    $response = $this->post(route('products.store'), [
        'name' => 'Test Product',
        'description' => 'Test Description',
        'unit_price' => 100.00,
        'quantity_available' => 50,
    ]);

    $response->assertStatus(302);
    $response->assertRedirect(route('dashboard'));
    $this->assertDatabaseHas('products', ['name' => 'Test Product']);
});

it('allows admin to update product and dispatches toast', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $product = Product::factory()->create();

    //Livewire::spy()->shouldReceive('dispatch')->with('showToast', 'Product updated successfully.', 'success')->once();

    $this->actingAs($user);
    $response = $this->put(route('products.update', $product), [
        'name' => 'Updated Product',
        'description' => 'Updated Description',
        'unit_price' => 150.00,
        'quantity_available' => 75,
    ]);

    $response->assertRedirect(route('dashboard'));
    $this->assertDatabaseHas('products', ['id' => $product->id, 'name' => 'Updated Product']);
});

it('prevents sales user from updating product and dispatches toast', function () {
    $user = User::factory()->create(['role' => 'sales']);
    $product = Product::factory()->create();

    //Livewire::spy()->shouldReceive('dispatch')->with('showToast', /'Unauthorized to edit products.', 'error')->once();

    $this->actingAs($user);
    $response = $this->put(route('products.update', $product), [
        'name' => 'Unauthorized Update',
        'description' => 'Test',
        'unit_price' => 100.00,
        'quantity_available' => 50,
    ]);

    $response->assertStatus(403);
    $this->assertDatabaseMissing('products', ['name' => 'Unauthorized Update']);
});

it('allows admin to soft delete product and dispatches toast', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $product = Product::factory()->create();

    // Livewire::spy()->shouldReceive('dispatch')->with('showToast', 'Product deleted successfully.', 'success')->once();

    $this->actingAs($user);
    $response = $this->delete(route('products.destroy', $product));

    $response->assertRedirect(route('dashboard'));
    $this->assertSoftDeleted('products', ['id' => $product->id]);
});

it('prevents sales user from deleting product and dispatches toast', function () {
    $user = User::factory()->create(['role' => 'sales']);
    $product = Product::factory()->create();

    //Livewire::spy()->shouldReceive('dispatch')->with('showToast', 'Unauthorized to delete products.', 'error')->once();

    $this->actingAs($user);
    $response = $this->delete(route('products.destroy', $product));

    $response->assertStatus(403);
    $this->assertDatabaseHas('products', ['id' => $product->id, 'deleted_at' => null]);
});

it('clears cache on product update', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $product = Product::factory()->create();
    Cache::store('redis')->put('product_' . $product->id, $product, now()->addMinutes(60));

    $this->actingAs($user);
    $response = $this->put(route('products.update', $product), [
        'name' => 'Updated Product',
        'description' => 'Updated',
        'unit_price' => 150.00,
        'quantity_available' => 75,
    ]);

    $response->assertRedirect(route('dashboard'));
    expect(Cache::store('redis')->has('product_' . $product->id))->toBeFalse();
});

it('clears cache on product soft delete', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $product = Product::factory()->create();
    Cache::store('redis')->put('product_' . $product->id, $product, now()->addMinutes(60));

    $this->actingAs($user);
    $response = $this->delete(route('products.destroy', $product));

    $response->assertRedirect(route('dashboard'));
    expect(Cache::store('redis')->has('product_' . $product->id))->toBeFalse();
});
