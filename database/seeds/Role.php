<?php

use Phinx\Seed\AbstractSeed;

class Role extends AbstractSeed
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
        $data[] = ['name' => 'Super Admin'];
        $data[] = ['name' => 'Admin Courses'];
        $data[] = ['name' => 'User'];

        $this->insert('role', $data);
    }
}
