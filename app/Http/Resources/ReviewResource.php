<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $review = $this->whenLoaded('review');
        return [
            'ability' => $this->ability,
            'review' => new ProductResource($review),
        ];
    }
}
