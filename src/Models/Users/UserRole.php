<?php 

namespace App\Models\Users;

class UserRole extends \App\Models\BaseModel
{
    protected $table = 'user_role';
    protected $column = ['id', 'user_id', 'role_id'];

    public function createRole($userId)
    {
    	$data['user_id'] = $userId;

    	$this->create($data);
    }
}