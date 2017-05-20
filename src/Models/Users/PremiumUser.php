<?php

namespace App\Models\Users;

class PremiumUser extends \App\Models\BaseModel
{
    protected $table = 'premium_user';
    protected $column = ['user_id', 'start_at', 'end_at'];

    public function setPremium($userId, $time)
    {
    	$find = $this->find('user_id', $userId)->fetch();

    	$userPremi = [
            'user_id'   => $userId,
            'end_at'    => $find ? date('Y-m-d H:i:s', strtotime($find['end_at'].$time)) : date('Y-m-d H:i:s', strtotime($time)),
        ];

        $this->updateOrCreate($userPremi, 'user_id', $userId);
    }
}