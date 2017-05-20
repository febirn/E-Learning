<?php

namespace App\Models\Subscribes;

class Subscriptions extends \App\Models\BaseModel
{
    protected $table = 'subscriptions';
    protected $column = ['id', 'name', 'price', 'expired_time'];
    protected $check = ['name'];
}