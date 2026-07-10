<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        return ProductResource::collection(Product::orderBy('name')->get());
    }

    public function store(ProductRequest $request)
    {
        $this->requireAdmin($request);

        $product = Product::create($request->validated());

        return new ProductResource($product);
    }

    public function show(Product $product)
    {
        return new ProductResource($product);
    }

    public function update(ProductRequest $request, Product $product)
    {
        $this->requireAdmin($request);

        $product->update($request->validated());

        return new ProductResource($product);
    }

    public function destroy(Request $request, Product $product)
    {
        $this->requireAdmin($request);

        return $this->deleteOrConflict(
            $product,
            'This product cannot be deleted because it is referenced by an existing order.'
        );
    }
}
