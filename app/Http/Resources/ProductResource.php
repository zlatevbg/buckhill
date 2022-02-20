<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'category_uuid' => $this->category_uuid,
            'category' => $this->category->title,
            'brand' => $this->brands->title,
            'uuid' => $this->uuid,
            'title' => $this->title,
            'price' => $this->price,
            'description' => $this->description,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at ? $this->created_at->format('d.m.Y') : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format('d.m.Y') : null,
            'deleted_at' => $this->deleted_at ? $this->deleted_at->format('d.m.Y') : null,
        ];
    }
}
