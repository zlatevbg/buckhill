<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderShippedResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'uuid' => $this->uuid,
            'categories' => $this->categories,
            'customer' => $this->customer,
            'shippingAddress' => $this->shippingAddress,
            'amount' => $this->amount,
            'totalProducts' => $this->totalProducts,
            'status' => $this->orderStatus->title ?? null,
            'shipped_at' => $this->shipped_at ? $this->shipped_at->format('d.m.Y') : null,
        ];
    }
}
