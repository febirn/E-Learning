<?php

use Phinx\Migration\AbstractMigration;

class CreateTablePasswordReset extends AbstractMigration
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
        $table = $this->table('password_reset');
        $table->addColumn('user_id', 'integer')
              ->addColumn('token', 'string')
              ->addColumn('create_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
              ->addForeignKey('user_id', 'users', 'id', ['update' => 'NO_ACTION', 'delete' => 'CASCADE'])
              ->addIndex('token')
              ->create();
    }
}
