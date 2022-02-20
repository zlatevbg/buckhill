<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
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
            'uuid' => $this->uuid,
            'type' => $this->type,
            'details' => $this->details,
            'created_at' => $this->created_at ? $this->created_at->format('d.m.Y') : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format('d.m.Y') : null,
        ];
    }
}
