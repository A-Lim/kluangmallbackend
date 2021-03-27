<?php

namespace App\Http\Resources\Vouchers;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UserGroups\UserGroupCollection;

class RewardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request) {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'image' => $this->image,
            'points' => $this->points,
            'description' => $this->description,
            'terms_and_conditions' => $this->terms_and_conditions,
            'merchants' => $this->merchants->map(function ($merchant) {
                return [
                    'id' => $merchant->id,
                    'name' => $merchant->name,
                    'category' => $merchant->category->name,
                    'category_id' => $merchant->category->id,
                ];
            })
        ];
    }
}
