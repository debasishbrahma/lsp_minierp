<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Quotation;
use App\Models\QuotationItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\QuotationItems>
 */
class QuotationItemFactory extends Factory
{
    protected $model = QuotationItem::class;

    public function definition()
    {
        $product = Product::factory()->create();
        $quantity = $this->faker->numberBetween(1, $product->quantity_available);
        return [
            'quotation_id' => Quotation::factory(),
            'product_id' => $product->id,
            'quantity' => $quantity,
            'unit_price' => $product->unit_price,
            'subtotal' => $quantity * $product->unit_price,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
