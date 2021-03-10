<?php
namespace App\Http\Traits;

trait HasSocial {

    public function social() {
        return $this->hasOne(UserGroup::class, 'user_usergroup', 'user_id', 'usergroup_id');
    }
}
