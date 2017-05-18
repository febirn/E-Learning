<?php

namespace App\Models\Users;

class PremiumUser extends \App\Models\BaseModel
{
    protected $table = 'premium_user';
    protected $column = ['user_id', 'start_at', 'end_at'];
}