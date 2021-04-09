<?php

namespace App\Http\Requests\Point;

use App\PointTransaction;
use App\Http\Requests\CustomFormRequest;

class AddDeductPointsRequest extends CustomFormRequest {

    public function __construct() {
        parent::__construct();
    }
    
    public function authorize() {
        return true;
    }

    public function rules() {
        return [
            'type' => 'required|string|in:'.PointTransaction::TYPE_ADD.','.PointTransaction::TYPE_DEDUCT,
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|integer',
            'description' => 'required|string',
        ];
    }

    public function messages() {
        return [
        ];
    }
}