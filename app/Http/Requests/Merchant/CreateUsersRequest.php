<?php

namespace App\Http\Requests\Merchant;

use App\Merchant;
use App\Http\Requests\CustomFormRequest;

class CreateUsersRequest extends CustomFormRequest {

    public function __construct() {
        parent::__construct();
    }
    
    public function authorize() {
        return true;
    }

    public function rules() {
        return [
            'users.*.name' => 'required|string',
            'users.*.email' => 'required|email|unique:users,email',
            'users.*.phone' => 'required|string'
        ];
    }

    public function messages() {
        return [
            'users.*.name.required' => 'Name is required.',
            'users.*.email.required' => 'Email is required.',
            'users.*.phone.required' => 'Phone is required.',
            'users.*.email.unique' => 'Email :input already exists.'
        ];
    }
}