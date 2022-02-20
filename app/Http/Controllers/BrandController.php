<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Brand;
use App\Http\Requests\StoreBrandRequest;
use App\Http\Requests\UpdateBrandRequest;
use App\Http\Resources\BrandResource;

class BrandController extends Controller
{
    /**
     * Display brands.
     *
     * @param string $uuid
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, $uuid = null)
    {
        if ($uuid) {
            $brand = Brand::where('uuid', $uuid)->firstOrFail();

            return response()->json(new BrandResource($brand));
        } else {
            $brands = Brand::orderBy($request->input('sortBy', 'id'), $request->input('desc') ? 'desc' : 'asc')->paginate($request->input('limit', 10));

            $brandsData = $brands->toArray();
            $brandsData['data'] = BrandResource::collection($brands->items());

            return response()->json($brandsData);
        }
    }

    /**
     * Create new brand
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(StoreBrandRequest $request)
    {
        $brand = Brand::create([
            'uuid' => Str::uuid(),
            'title' => $request->input('title'),
            'slug' => Str::slug($request->input('title')),
        ]);

        return response()->json(new BrandResource($brand));
    }

    /**
     * Edit brand
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(UpdateBrandRequest $request, Brand $brand)
    {
        $brand->update([
            'title' => $request->input('title'),
            'slug' => Str::slug($request->input('title')),
        ]);

        return response()->json(new BrandResource($brand));
    }

    /**
     * Delete a brand.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Brand $brand)
    {
        $brand->delete();

        return response()->json([
            'success' => true,
        ]);
    }
}
