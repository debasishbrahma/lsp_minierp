<?php

use App\Models\User;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\QuotationItem;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;
use App\Notifications\QuotationStatusUpdated;
use Livewire\Livewire;

it('lists quotations for sales user', function () {
    $user = User::factory()->create(['role' => 'sales']);
    $quotations = Quotation::factory()->count(2)->create(['user_id' => $user->id]);

    $this->actingAs($user);
    $response = $this->get(route('quotations.index'));

    $response->assertStatus(200);
    $response->assertViewIs('quotations.index');
    $response->assertViewHas('quotations', fn($viewQuotations) => $viewQuotations->count() === 2);
});

it('lists all quotations including soft-deleted for admin', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $quotations = Quotation::factory()->count(2)->create();
    $deletedQuotation = Quotation::factory()->create();
    $deletedQuotation->delete();

    $this->actingAs($user);
    $response = $this->get(route('quotations.index'));

    $response->assertStatus(200);
    $response->assertViewHas('quotations', fn($viewQuotations) => $viewQuotations->count() === 3);
});

it('creates quotation with valid stock and dispatches toast', function () {
    $user = User::factory()->create(['role' => 'sales']);
    $product = Product::factory()->create(['quantity_available' => 100]);

    //  Livewire::spy()->shouldReceive('dispatch')->with('showToast', 'Quotation created successfully.', 'success')->once();

    $this->actingAs($user);
    $response = $this->post(route('quotations.store'), [
        'customer_name' => 'John Doe',
        'products' => [
            ['product_id' => $product->id, 'quantity' => 50],
        ],
    ]);

    $response->assertRedirect(route('dashboard'));
    $this->assertDatabaseHas('quotations', ['customer_name' => 'John Doe']);
    $this->assertDatabaseHas('quotation_items', ['product_id' => $product->id, 'quantity' => 50]);
});

it('fails to create quotation with insufficient stock and dispatches toast', function () {
    $user = User::factory()->create(['role' => 'sales']);
    $product = Product::factory()->create(['quantity_available' => 10]);

    //  Livewire::spy()->shouldReceive('dispatch')->with('showToast', "Insufficient stock for {$product->name}.", 'error')->once();

    $this->actingAs($user);
    $response = $this->post(route('quotations.store'), [
        'customer_name' => 'John Doe',
        'products' => [
            ['product_id' => $product->id, 'quantity' => 20],
        ],
    ]);

    $response->assertStatus(302);
    $response->assertSessionHasErrors('products');
    $this->assertDatabaseMissing('quotations', ['customer_name' => 'John Doe']);
});

it('allows sales user to edit pending quotation and dispatches toast', function () {
    $user = User::factory()->create(['role' => 'sales']);
    $product = Product::factory()->create(['quantity_available' => 100]);
    $quotation = Quotation::factory()->create(['user_id' => $user->id, 'status' => 'pending']);
    QuotationItem::factory()->create(['quotation_id' => $quotation->id, 'product_id' => $product->id]);

    //  Livewire::spy()->shouldReceive('dispatch')->with('showToast', 'Quotation updated successfully.', 'success')->once();

    $this->actingAs($user);
    $response = $this->put(route('quotations.update', $quotation), [
        'customer_name' => 'Jane Doe',
        'products' => [
            ['product_id' => $product->id, 'quantity' => 30],
        ],
    ]);

    $response->assertRedirect(route('dashboard'));
    $this->assertDatabaseHas('quotations', ['id' => $quotation->id, 'customer_name' => 'Jane Doe']);
});

it('prevents sales user from editing approved quotation and dispatches toast', function () {
    $user = User::factory()->create(['role' => 'sales']);
    $quotation = Quotation::factory()->create(['user_id' => $user->id, 'status' => 'approved']);

    //  Livewire::spy()->shouldReceive('dispatch')->with('showToast', 'Unauthorized or quotation cannot be edited.', 'error')->once();

    $this->actingAs($user);
    $response = $this->put(route('quotations.update', $quotation), [
        'customer_name' => 'Jane Doe',
        'products' => [
            ['product_id' => 1, 'quantity' => 10],
        ],
    ]);

    $response->assertRedirect(route('quotations.index'));
    $this->assertDatabaseMissing('quotations', ['id' => $quotation->id, 'customer_name' => 'Jane Doe']);
});

it('allows admin to edit any quotation and dispatches toast', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $product = Product::factory()->create(['quantity_available' => 100]);
    $quotation = Quotation::factory()->create(['status' => 'approved']);
    QuotationItem::factory()->create(['quotation_id' => $quotation->id, 'product_id' => $product->id]);

    //  Livewire::spy()->shouldReceive('dispatch')->with('showToast', 'Quotation updated successfully.', 'success')->once();

    $this->actingAs($user);
    $response = $this->put(route('quotations.update', $quotation), [
        'customer_name' => 'Jane Doe',
        'products' => [
            ['product_id' => $product->id, 'quantity' => 30],
        ],
    ]);

    $response->assertRedirect(route('dashboard'));
    $this->assertDatabaseHas('quotations', ['id' => $quotation->id, 'customer_name' => 'Jane Doe']);
});

it('allows admin to update quotation status and dispatches toast', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $salesUser = User::factory()->create(['role' => 'sales']);
    $quotation = Quotation::factory()->create(['user_id' => $salesUser->id, 'status' => 'pending']);

    Notification::fake();
    //  Livewire::spy()->shouldReceive('dispatch')->with('showToast', 'Quotation status updated successfully.', 'success')->once();

    $this->actingAs($user);
    $response = $this->post(route('quotations.status', $quotation), ['status' => 'approved']);

    $response->assertRedirect(route('dashboard'));
    $this->assertDatabaseHas('quotations', ['id' => $quotation->id, 'status' => 'approved']);
    Notification::assertSentTo($salesUser, QuotationStatusUpdated::class);
});

it('allows soft delete of quotation and dispatches toast', function () {
    $user = User::factory()->create(['role' => 'sales']);
    $quotation = Quotation::factory()->create(['user_id' => $user->id]);

    //  Livewire::spy()->shouldReceive('dispatch')->with('showToast', 'Quotation deleted successfully.', 'success')->once();

    $this->actingAs($user);
    $response = $this->delete(route('quotations.destroy', $quotation));

    $response->assertRedirect(route('quotations.index'));
    $this->assertSoftDeleted('quotations', ['id' => $quotation->id]);
});

it('clears cache on quotation creation', function () {
    $user = User::factory()->create(['role' => 'sales']);
    $product = Product::factory()->create(['quantity_available' => 100]);
    Cache::store('redis')->put('quotations_user_' . $user->id, collect([]), now()->addMinutes(60));

    $this->actingAs($user);
    $this->post(route('quotations.store'), [
        'customer_name' => 'John Doe',
        'products' => [
            ['product_id' => $product->id, 'quantity' => 50],
        ],
    ]);

    expect(Cache::store('redis')->has('quotations_user_' . $user->id))->toBeFalse();
});
