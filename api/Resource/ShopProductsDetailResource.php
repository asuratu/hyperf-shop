<?php

namespace Api\Resource;

use Hyperf\Resource\Json\JsonResource;

class ShopProductsDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'image' => $this->image,
            'rating' => $this->rating,
            'sold_count' => $this->sold_count,
            'review_count' => $this->review_count,
            'price' => $this->price,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'skus' => ShopProductSkusResource::collection($this->skus),
        ];
    }
}
