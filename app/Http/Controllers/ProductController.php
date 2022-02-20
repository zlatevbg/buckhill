<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;

class ProductController extends Controller
{
    /**
     * Display products.
     *
     * @param string $uuid
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, $uuid = null)
    {
        if ($uuid) {
            $product = Product::where('uuid', $uuid)->firstOrFail();

            return response()->json(new ProductResource($product));
        } else {
            $products = Product::with(['category', 'brands'])->select('products.*')->leftJoin('categories', 'categories.uuid', '=', 'products.category_uuid')->orderBy($request->input('sortBy', 'products.id'), $request->input('desc') ? 'desc' : 'asc');

            $filters = [
                'price' => 'price',
                'title' => 'title',
            ];

            $fields = array_flip($filters);

            foreach ($request->only($filters) as $key => $value) {
                $products = $products->where('products.' . $fields[$key], $value);
            }

            if ($request->input('category')) {
                $products = $products->where('categories.title', $request->input('category'));
            }

            if ($request->input('brand')) {
                $products = $products->leftJoin('brands', 'brands.uuid', '=', 'products.brand')->where('brands.title', $request->input('brand'));
            }

            $products = $products->paginate($request->input('limit', 10));

            $productsData = $products->toArray();
            $productsData['data'] = ProductResource::collection($products->items());

            return response()->json($productsData);
        }
    }

    /**
     * Create new product
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(StoreProductRequest $request)
    {
        $product = Product::create([
            'uuid' => Str::uuid(),
            'category_uuid' => $request->input('category_uuid'),
            'title' => $request->input('title'),
            'price' => $request->input('price'),
            'description' => $request->input('description'),
            'metadata' => $request->input('metadata'),
        ]);

        $product = Product::find($product->id); // needed for json brand relationship

        return response()->json(new ProductResource($product));
    }

    /**
     * Edit product
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(UpdateProductRequest $request, Product $product)
    {
        $product->update([
            'category_uuid' => $request->input('category_uuid'),
            'title' => $request->input('title'),
            'price' => $request->input('price'),
            'description' => $request->input('description'),
            'metadata' => $request->input('metadata'),
        ]);

        return response()->json(new ProductResource($product));
    }

    /**
     * Delete product.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Product $product)
    {
        $product->delete();

        return response()->json([
            'success' => true,
        ]);
    }
}
