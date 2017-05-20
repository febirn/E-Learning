<?php

namespace App\Models\Users;

class User extends \App\Models\BaseModel
{
    protected $table = 'users';
    protected $column = ['id', 'name', 'username', 'email', 'password', 'phone', 'photo', 'active_token', 'is_active'];
    protected $check = ['username', 'email'];

    public function register(array $data)
    {
        $data = [
            'name'          =>  $data['name'],
            'username'      =>  $data['username'],
            'email'         =>  $data['email'],
            'password'      =>  password_hash($data['password'], PASSWORD_DEFAULT),
            'phone'         =>  $data['phone'],
            'active_token'  =>  md5(openssl_random_pseudo_bytes(12)),
        ];

        return $this->checkOrCreate($data);
    }

    public function resetPassword(array $data, $column, $value)
    {
        $data = [
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
        ];

        return $this->update($data, $column, $value);
    }

    public function updateProfile($data, $id, $photo = null)
    {
        $data = [
            'name'  => $data['name'],
            'email' => $data['email'],
            'photo' => $photo,
        ];

        if (!$data['email']) {
            unset($data['email']);
        }

        if ($photo == null) {
            unset($data['photo']);
        }

        return $this->checkOrUpdate($data, 'id', $id);
    }

    public function joinUserAndRole()
    {
        $qb = $this->getBuilder();
        $result = $qb->select('u.id, u.name, u.username, u.email')
            ->from($this->table, 'u')
            ->join('u', 'user_role', 'ur', 'u.id=ur.user_id')
            ->where('role_id = 3')
            ->execute();

        return $result->fetchAll();
    }
}

?>