<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\OrderStatus;
use App\Http\Requests\StoreOrderStatusRequest;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Http\Resources\OrderStatusResource;

class OrderStatusController extends Controller
{
    /**
     * Display order statuses.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, OrderStatus $orderStatus)
    {
        if ($orderStatus->id) {
            return response()->json(new OrderStatusResource($orderStatus));
        } else {
            $orderStatuses = OrderStatus::orderBy($request->input('sortBy', 'id'), $request->input('desc') ? 'desc' : 'asc')->paginate($request->input('limit', 10));

            $orderStatusesData = $orderStatuses->toArray();
            $orderStatusesData['data'] = OrderStatusResource::collection($orderStatuses->items());

            return response()->json($orderStatusesData);
        }
    }

    /**
     * Create new order status
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(StoreOrderStatusRequest $request)
    {
        $orderStatus = OrderStatus::create([
            'uuid' => Str::uuid(),
            'title' => $request->input('title'),
        ]);

        return response()->json(new OrderStatusResource($orderStatus));
    }

    /**
     * Edit order status
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(UpdateOrderStatusRequest $request, OrderStatus $orderStatus)
    {
        $orderStatus->update([
            'title' => $request->input('title'),
        ]);

        return response()->json(new OrderStatusResource($orderStatus));
    }

    /**
     * Delete order status.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(OrderStatus $orderStatus)
    {
        $orderStatus->delete();

        return response()->json([
            'success' => true,
        ]);
    }
}
