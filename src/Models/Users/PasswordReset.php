<?php 

namespace App\Models\Users;

class PasswordReset extends \App\Models\BaseModel
{
    protected $table = 'password_reset';
    protected $column = ['id', 'user_id', 'token'];

    public function setToken($userId)
    {
        $data = [
            'user_id'   => $userId,
            'token'     => md5(openssl_random_pseudo_bytes(12)),
        ];

        return $this->updateOrCreate($data);
    }
}