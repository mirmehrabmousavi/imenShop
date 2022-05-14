<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'=> $this->id,
            'slug'=> $this->slug,
            'image'=> $this->image,
            'title'=> $this->title,
            'titleEn'=> $this->titleEn,
            'suggest'=> $this->suggest,
            'off'=> $this->off,
            'price'=> $this->price,
            'type'=> $this->type,
            'count'=> $this->count,
            'offPrice'=> $this->offPrice,
            'review'=> ReviewResource::collection($this->whenLoaded('review')),
            'payMeta'=> PayMetaResource::collection($this->whenLoaded('payMeta'))
        ];
    }
}
