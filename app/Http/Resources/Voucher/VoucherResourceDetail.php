<?php

namespace App\Http\Resources\Voucher;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UserGroup\UserGroupCollection;

class VoucherResourceDetail extends JsonResource
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
            'status' => $this->status,
            'name' => $this->name,
            'description' => $this->description,
            'points' => $this->points,
            'free_points' => $this->free_points,
            'data' => $this->data,
            'image' => $this->image,
            'qr' => $this->qr,
            'has_redemption_limit' => $this->has_redemption_limit,
            'fromDate' => $this->fromDate->format(env('APP_DATE_FORMAT')),
            'toDate' => $this->toDate->format(env('APP_DATE_FORMAT')),
            'terms_and_conditions' => $this->terms_and_conditions,
            'redeemed_count' => $this->redeemed_count,
            'limit_count' => $this->limit_count,
            'limits' => $this->limits,
            'merchants' => $this->merchants->map(function ($merchant) {
                return [
                    'id' => $merchant->id,
                    'name' => $merchant->name
                ];
            })
        ];
    }
}
