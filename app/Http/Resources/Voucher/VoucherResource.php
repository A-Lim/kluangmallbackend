<?php

namespace App\Http\Resources\Voucher;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UserGroup\UserGroupCollection;

class VoucherResource extends JsonResource
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
            'type' => $this->type,
            'display_to_all' => $this->display_to_all,
            'status' => $this->status,
            'image' => $this->image,
            'name' => $this->name,
            'description' => $this->description,
            'points' => $this->points,
            'free_points' => $this->free_points,
            'fromDate' => $this->fromDate->format(env('APP_DATE_FORMAT')),
            'toDate' => $this->toDate->format(env('APP_DATE_FORMAT')),
            'merchants' => $this->merchants->map(function ($merchant) {
                return [
                    'id' => $merchant->id,
                    'name' => $merchant->name
                ];
            })
        ];
    }
}
