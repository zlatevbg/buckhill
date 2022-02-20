<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Payment;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdatePaymentRequest;
use App\Http\Resources\PaymentResource;

class PaymentController extends Controller
{
    /**
     * Display payments.
     *
     * @param string $uuid
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, $uuid = null)
    {
        if ($uuid) {
            $payment = Payment::where('uuid', $uuid)->firstOrFail();

            return response()->json(new PaymentResource($payment));
        } else {
            $payments = Payment::orderBy($request->input('sortBy', 'id'), $request->input('desc') ? 'desc' : 'asc')->paginate($request->input('limit', 10));

            $paymentsData = $payments->toArray();
            $paymentsData['data'] = PaymentResource::collection($payments->items());

            return response()->json($paymentsData);
        }
    }

    /**
     * Create new payment
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(StorePaymentRequest $request)
    {
        $payment = Payment::create([
            'uuid' => Str::uuid(),
            'type' => $request->input('type'),
            'details' => $request->input('details'),
        ]);

        return response()->json(new PaymentResource($payment));
    }

    /**
     * Edit payment
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(UpdatePaymentRequest $request, Payment $payment)
    {
        $payment->update([
            'type' => $request->input('type'),
            'details' => $request->input('details'),
        ]);

        return response()->json(new PaymentResource($payment));
    }

    /**
     * Delete payment.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Payment $payment)
    {
        $payment->delete();

        return response()->json([
            'success' => true,
        ]);
    }
}
