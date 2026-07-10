<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $this->requireAdmin($request);

        return CustomerResource::collection(Customer::orderBy('name')->get());
    }

    public function store(CustomerRequest $request)
    {
        $this->requireAdmin($request);

        $customer = Customer::create($request->validated());

        return new CustomerResource($customer);
    }

    public function show(Request $request, Customer $customer)
    {
        $this->requireAdmin($request);

        return new CustomerResource($customer);
    }

    public function update(CustomerRequest $request, Customer $customer)
    {
        $this->requireAdmin($request);

        $customer->update($request->validated());

        return new CustomerResource($customer);
    }

    public function destroy(Request $request, Customer $customer)
    {
        $this->requireAdmin($request);

        return $this->deleteOrConflict(
            $customer,
            'This customer cannot be deleted because they have existing orders.'
        );
    }
}
