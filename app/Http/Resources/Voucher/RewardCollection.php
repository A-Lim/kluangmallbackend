<?php

namespace App\Http\Resources\Voucher;

use Illuminate\Http\Resources\Json\ResourceCollection;

class RewardCollection extends ResourceCollection
{
    public $collects = RewardResource::class;

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request) {
        $class = get_class($this->resource);

        switch ($class) {
            case 'Illuminate\Support\Collection';
                return parent::toArray($request);

            case 'Illuminate\Pagination\LengthAwarePaginator':
                return $this->resource;
        }
    }
}
