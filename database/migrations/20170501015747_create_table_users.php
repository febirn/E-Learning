<?php

use Phinx\Migration\AbstractMigration;

class CreateTableUsers extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
     public function change()
    {
        $data = $this->table('users');
        $data->addColumn('username', 'string')
             ->addColumn('email', 'string')
             ->addColumn('password', 'string')
             ->addColumn('name', 'string')
             ->addColumn('phone', 'string', ['null' => true])
             ->addColumn('photo', 'string', ['default' => 'default_user.png'])
             ->addColumn('create_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
             ->addColumn('update_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
             ->addColumn('is_active', 'integer', ['default' => 0, 'limit' => 1])
             ->addColumn('active_token', 'string')
             ->addColumn('deleted', 'integer', ['default' => 0])
             ->addIndex(['username', 'email'])
             ->create();
    }
}
