<?php

namespace App\Models\Users;

class UserToken extends \App\Models\BaseModel
{
    protected $table = 'user_token';
    protected $column = ['id','user_id', 'token', 'expire_at'];
    protected $check = ['user_id'];

    public function setToken($id)
    {
        $data = [
            'user_id'   =>  $id,
            'token'     =>  md5(openssl_random_pseudo_bytes(12)),
            'expire_at' =>  date('Y-m-d H:i:s', strtotime('+2 day')),
        ];

        return $this->updateOrCreate($data);
    }
}

?>