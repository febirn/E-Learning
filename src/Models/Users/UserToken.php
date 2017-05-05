<?php

namespace App\Models\Users;

class UserToken extends \App\Models\BaseModel
{
    protected $table = 'user_token';
    protected $column = ['user_id', 'token', 'expire_at'];
    protected $check = 'token';

    public function setToken($id)
    {
        $data = [
            'user_id'   =>  $id,
            'token'     =>  md5(openssl_random_pseudo_bytes(12)),
            'expire_at' =>  date('Y-m-d H:i:s', strtotime('+2 day')),
        ];

        $now = date('Y-m-d H:i:s', strtotime('now'));

        $this->updateOrCreate($data);

        return $data;
    }
}

?>