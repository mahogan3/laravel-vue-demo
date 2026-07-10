<?php

namespace App\Http\Controllers\Api;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Http\Resources\OrderResource;
use App\Models\AuthUser;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $user = $this->authUser($request);

        $query = Order::with(['customer', 'items.product'])->orderBy('id');

        if (! $user->isAdmin()) {
            $query->whereHas('customer', fn ($q) => $q->where('user_id', $user->id));
        }

        return OrderResource::collection($query->get());
    }

    public function store(OrderRequest $request)
    {
        $user = $this->authUser($request);

        $order = DB::transaction(function () use ($request, $user) {
            $customerId = $user->isAdmin()
                ? $request->validated('customer_id')
                : $this->resolveOwnCustomer($user)->id;

            $order = Order::create([
                'customer_id' => $customerId,
                'status' => OrderStatus::Pending,
            ]);

            $total = 0;

            foreach ($request->validated('items') as $item) {
                $product = Product::findOrFail($item['product_id']);

                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                ]);

                $total += $product->price * $item['quantity'];
            }

            $order->update(['total' => $total]);

            return $order;
        });

        return new OrderResource($order->load('customer', 'items.product'));
    }

    public function show(Request $request, Order $order)
    {
        $user = $this->authUser($request);

        if (! $user->isAdmin() && $order->customer?->user_id !== $user->id) {
            abort(403);
        }

        return new OrderResource($order->load('customer', 'items.product'));
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order)
    {
        $this->requireAdmin($request);

        $order->update(['status' => $request->validated('status')]);

        return new OrderResource($order->load('customer', 'items.product'));
    }

    public function destroy(Request $request, Order $order)
    {
        $this->requireAdmin($request);

        $order->delete();

        return response()->noContent();
    }

    /**
     * Find-or-create the Customer record linked to a non-admin's account,
     * matching by email first so an admin-created customer gets claimed
     * rather than duplicated.
     */
    private function resolveOwnCustomer(AuthUser $user): Customer
    {
        $customer = Customer::where('email', $user->email)->first();

        if ($customer && ! $customer->user_id) {
            $customer->update(['user_id' => $user->id]);

            return $customer;
        }

        if ($customer) {
            return $customer;
        }

        return Customer::create([
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ]);
    }
}
