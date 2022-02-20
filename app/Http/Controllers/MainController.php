<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Promotion;
use App\Http\Resources\PostResource;
use App\Http\Resources\PromotionResource;

class MainController extends Controller
{
    /**
     * Display blog posts.
     *
     * @param string $uuid
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function blog(Request $request, $uuid = null)
    {
        if ($uuid) {
            $post = Post::where('uuid', $uuid)->firstOrFail();

            return response()->json(new PostResource($post));
        } else {
            $posts = Post::orderBy($request->input('sortBy', 'id'), $request->input('desc') ? 'desc' : 'asc')->paginate($request->input('limit', 10));

            $postsData = $posts->toArray();
            $postsData['data'] = PostResource::collection($posts->items());

            return response()->json($postsData);
        }
    }

    /**
     * Display promotions.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function promotions(Request $request)
    {
        $promotions = Promotion::orderBy($request->input('sortBy', 'id'), $request->input('desc') ? 'desc' : 'asc');

        $filters = [
            'valid' => 'valid',
        ];

        $fields = array_flip($filters);

        foreach ($request->only($filters) as $key => $value) {
            $promotions = $promotions->where($fields[$key], $value);
        }

        $promotions = $promotions->paginate($request->input('limit', 10));

        $promotionsData = $promotions->toArray();
        $promotionsData['data'] = PromotionResource::collection($promotions->items());

        return response()->json($promotionsData);
    }
}
