<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Order;
use App\Models\Category;
use App\Models\OrderStatus;
use App\Models\Payment;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Http\Resources\OrderShippedResource;
use Carbon\Carbon;

class OrderController extends Controller
{
    /**
     * Display orders.
     *
     * @param string $uuid
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, $uuid = null)
    {
        if ($uuid) {
            $order = Order::where('user_id', $request->user()->id)->where('uuid', $uuid)->firstOrFail();

            return response()->json(new OrderResource($order));
        } else {
            $orders = Order::orderBy($request->input('sortBy', 'id'), $request->input('desc') ? 'desc' : 'asc')->paginate($request->input('limit', 10));

            $ordersData = $orders->toArray();
            $ordersData['data'] = OrderResource::collection($orders->items());

            return response()->json($ordersData);
        }
    }

    /**
     * Display orders shipment locator.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function shipmentLocator(Request $request)
    {
        $orders = Order::selectRaw('orders.uuid, orders.products, TRIM(CONCAT(users.first_name, " ", users.last_name)) AS customer, orders.address->>"$.shipping" AS shippingAddress, orders.amount, orders.shipped_at')->leftJoin('users', 'users.id', '=', 'orders.user_id')->when($request->input('customerUuid'), function ($query) use ($request) {
            return $query->where('users.uuid', '=', $request->input('customerUuid'));
        })->orderBy($request->input('sortBy', 'orders.id'), $request->input('desc') ? 'desc' : 'asc');

        $filters = [
            'uuid' => 'orderUuid',
        ];

        $fields = array_flip($filters);

        foreach ($request->only($filters) as $key => $value) {
            $orders = $orders->where('orders.' . $fields[$key], $value);
        }

        if ($request->input('dateRange')) {
            $dateRange = json_decode($request->input('dateRange'));
            $orders = $orders->whereBetween('orders.shipped_at', [Carbon::parse($dateRange->from), Carbon::parse($dateRange->to)]);
        }

        if ($request->input('fixRange')) {
            if ($request->input('fixRange') == 'today') {
                $orders = $orders->whereDate('orders.shipped_at', Carbon::now());
            } elseif ($request->input('fixRange') == 'monthly') {
                $orders = $orders->whereYear('orders.shipped_at', date('Y'))->whereMonth('orders.shipped_at', date('m'));
            } elseif ($request->input('fixRange') == 'yearly') {
                $orders = $orders->whereYear('orders.shipped_at', date('Y'));
            }
        }

        $orders = $orders->paginate($request->input('limit', 10));

        foreach ($orders as $order) {
            $categories = Category::select('categories.title')->leftJoin('products', 'categories.uuid', '=', 'products.category_uuid')->whereIn('products.uuid', array_column($order->products, 'product'))->distinct()->get()->implode('title', ', ');
            $order->categories = $categories;
        }

        $ordersData = $orders->toArray();
        $ordersData['data'] = OrderShippedResource::collection($orders->items());

        return response()->json($ordersData);
    }

    /**
     * Display orders dashboard.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function dashboard(Request $request)
    {
        $orders = Order::with('orderStatus')->selectRaw('orders.uuid, orders.order_status_id, JSON_LENGTH(orders.products) AS totalProducts, TRIM(CONCAT(users.first_name, " ", users.last_name)) AS customer, orders.amount')->leftJoin('users', 'users.id', '=', 'orders.user_id')->orderBy($request->input('sortBy', 'orders.id'), $request->input('desc') ? 'desc' : 'asc');

        $filters = [
            'uuid' => 'orderUuid',
        ];

        $fields = array_flip($filters);

        foreach ($request->only($filters) as $key => $value) {
            $orders = $orders->where('orders.' . $fields[$key], $value);
        }

        if ($request->input('dateRange')) {
            $dateRange = json_decode($request->input('dateRange'));
            $orders = $orders->whereBetween('orders.shipped_at', [Carbon::parse($dateRange->from), Carbon::parse($dateRange->to)]);
        }

        if ($request->input('fixRange')) {
            if ($request->input('fixRange') == 'today') {
                $orders = $orders->whereDate('orders.shipped_at', Carbon::now());
            } elseif ($request->input('fixRange') == 'monthly') {
                $orders = $orders->whereYear('orders.shipped_at', date('Y'))->whereMonth('orders.shipped_at', date('m'));
            } elseif ($request->input('fixRange') == 'yearly') {
                $orders = $orders->whereYear('orders.shipped_at', date('Y'));
            }
        }

        $orders = $orders->paginate($request->input('limit', 10));
        $totalEarnings = $orders->sum('amount');

        $ordersData = $orders->toArray();
        $ordersData['totalEarnings'] = $totalEarnings;
        $ordersData['data'] = OrderShippedResource::collection($orders->items());

        return response()->json($ordersData);
    }

    /**
     * Create new order
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(StoreOrderRequest $request)
    {
        $order = Order::create([
            'uuid' => Str::uuid(),
            'user_id' => $request->user()->id,
            'order_status_id' => OrderStatus::where('uuid', $request->input('order_status_uuid'))->value('id'),
            'payment_id' => Payment::where('uuid', $request->input('payment_uuid'))->value('id'),
            'products' => $request->input('products'),
            'address' => $request->input('address'),
            'amount' => mt_rand(10 * 10, 1000 * 10) / 10,
        ]);

        return response()->json(new OrderResource($order));
    }

    /**
     * Edit order
     *
     * @param string $uuid
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(UpdateOrderRequest $request, $uuid)
    {
        $order = Order::where('user_id', $request->user()->id)->where('uuid', $uuid)->firstOrFail();
        $order->update([
            'order_status_id' => OrderStatus::where('uuid', $request->input('order_status_uuid'))->value('id'),
            'payment_id' => Payment::where('uuid', $request->input('payment_uuid'))->value('id'),
            'products' => $request->input('products'),
            'address' => $request->input('address'),
        ]);

        return response()->json(new OrderResource($order));
    }

    /**
     * Delete order.
     *
     * @param string $uuid
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request, $uuid)
    {
        $order = Order::where('user_id', $request->user()->id)->where('uuid', $uuid)->firstOrFail();
        $order->delete();

        return response()->json([
            'success' => true,
        ]);
    }

    /**
     * Download order invoice.
     *
     * @param string $uuid
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function download(Request $request, $uuid)
    {
        $order = Order::where('user_id', $request->user()->id)->where('uuid', $uuid)->firstOrFail();

        return response()->json(new OrderResource($order));
    }
}
