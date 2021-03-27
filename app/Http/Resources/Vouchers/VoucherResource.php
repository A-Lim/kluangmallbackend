<?php

namespace App\Http\Resources\Vouchers;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UserGroups\UserGroupCollection;

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
            'status' => $this->status,
            'image' => $this->image,
            'name' => $this->name,
            'description' => $this->description,
            'points' => $this->points,
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
