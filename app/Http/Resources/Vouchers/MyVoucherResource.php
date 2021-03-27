<?php

namespace App\Http\Resources\Vouchers;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UserGroups\UserGroupCollection;

class MyVoucherResource extends JsonResource
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
            'user_id' => $this->user_id,
            'expiry_date' => $this->expiry_date->format(env('APP_DATE_FORMAT')),
            'status' => $this->status,
            'name' => $this->voucher->name,
            'description' => $this->voucher->description,
            'image' => $this->voucher->image,
            'terms_and_conditions' => $this->voucher->terms_and_conditions,
            'custom' => $this->voucher->data != null ? 1 : 0,
            'merchants' => $this->voucher->merchants->map(function ($merchant) {
                return [
                    'id' => $merchant->id,
                    'name' => $merchant->name
                ];
            })
        ];
    }
}
