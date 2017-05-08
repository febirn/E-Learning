<?php

use Phinx\Migration\AbstractMigration;

class CreateTablePremiumUser extends AbstractMigration
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
        $data = $this->table('premium_user', ['id' => false]);
        $data->addColumn('user_id', 'integer')
             ->addColumn('start_at', 'timestamp', ['default' => "CURRENT_TIMESTAMP"])
             ->addColumn('end_at', 'datetime')
             ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => "NO_ACTION"])
             ->create();
    }
}
