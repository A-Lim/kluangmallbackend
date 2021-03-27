<?php

namespace App\Http\Resources\Vouchers;

use Illuminate\Http\Resources\Json\ResourceCollection;

class MyVoucherCollection extends ResourceCollection
{
    public $collects = MyVoucherResource::class;

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
