<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Enums\OrderStatus;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $products = Product::factory(15)->industrial()->create();
        $customers = Customer::factory(10)->create();

        $statuses = OrderStatus::cases();

        foreach (range(1, 5) as $i) {
            $order = Order::create([
                'customer_id' => $customers->random()->id,
                'status' => $statuses[array_rand($statuses)],
            ]);

            $orderProducts = $products->random(rand(1, 3));
            $total = 0;

            foreach ($orderProducts as $product) {
                $quantity = rand(1, 4);

                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $product->price,
                ]);

                $total += $product->price * $quantity;
            }

            $order->update(['total' => $total]);
        }
    }
}
