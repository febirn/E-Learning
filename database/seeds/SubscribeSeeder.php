<?php

use Phinx\Seed\AbstractSeed;

class SubscribeSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        $data[] = [
            'name'          => 'Monthly',
            'price'         => 3.99,
            'expired_time'  => '+1 month',
        ];
        $data[] = [
            'name'          => 'Weekly',
            'price'         => 0.99,
            'expired_time'  => '+7 day',
        ];
        $data[] = [
            'name'          => 'Yearly',
            'price'         => 39.99,
            'expired_time'  => '+1 year',
        ];

        $this->insert('subscriptions', $data);
    }
}
