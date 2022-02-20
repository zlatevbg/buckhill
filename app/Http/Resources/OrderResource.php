<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'id' => $this->id,
            'user_id' => $this->user_id,
            'order_status_id' => $this->order_status_id,
            'payment_id' => $this->payment_id,
            'uuid' => $this->uuid,
            'products' => $this->products,
            'address' => $this->address,
            'delivery_fee' => $this->delivery_fee,
            'amount' => $this->amount,
            'created_at' => $this->created_at ? $this->created_at->format('d.m.Y') : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format('d.m.Y') : null,
            'shipped_at' => $this->shipped_at ? $this->shipped_at->format('d.m.Y') : null,
        ];
    }
}
