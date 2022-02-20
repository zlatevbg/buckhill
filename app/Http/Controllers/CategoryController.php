<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;

class CategoryController extends Controller
{
    /**
     * Display categories.
     *
     * @param string $uuid
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, $uuid = null)
    {
        if ($uuid) {
            $category = Category::where('uuid', $uuid)->firstOrFail();

            return response()->json(new CategoryResource($category));
        } else {
            $categories = Category::orderBy($request->input('sortBy', 'id'), $request->input('desc') ? 'desc' : 'asc')->paginate($request->input('limit', 10));

            $categoriesData = $categories->toArray();
            $categoriesData['data'] = CategoryResource::collection($categories->items());

            return response()->json($categoriesData);
        }
    }

    /**
     * Create new category
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(StoreCategoryRequest $request)
    {
        $category = Category::create([
            'uuid' => Str::uuid(),
            'title' => $request->input('title'),
            'slug' => Str::slug($request->input('title')),
        ]);

        return response()->json(new CategoryResource($category));
    }

    /**
     * Edit category
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(UpdateCategoryRequest $request, Category $category)
    {
        $category->update([
            'title' => $request->input('title'),
            'slug' => Str::slug($request->input('title')),
        ]);

        return response()->json(new CategoryResource($category));
    }

    /**
     * Delete category.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Category $category)
    {
        $category->delete();

        return response()->json([
            'success' => true,
        ]);
    }
}
