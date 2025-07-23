<?php

namespace Database\Factories;

use App\Models\Quotation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Quotations>
 */
class QuotationFactory extends Factory
{
    protected $model = Quotation::class;

    public function definition()
    {
        return [
            'customer_name' => $this->faker->name,
            'user_id' => User::factory(),
            'total_price' => $this->faker->randomFloat(2, 50, 5000),
            'status' => $this->faker->randomElement(['pending', 'approved', 'rejected']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
