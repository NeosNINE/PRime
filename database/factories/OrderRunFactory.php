<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderRun;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderRunFactory extends Factory
{
    protected $model = OrderRun::class;

    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'run_number' => 1,
            'quantity' => 50,
            'status' => 'pending',
            'scheduled_for' => now()->addHour(),
            'dispatched_at' => null,
            'completed_at' => null,
        ];
    }
}
