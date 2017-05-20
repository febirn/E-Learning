<?php

use Phinx\Migration\AbstractMigration;

class CreateArticleTable extends AbstractMigration
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
        $article = $this->table('articles');
        $article->addColumn('user_id', 'integer')
                ->addColumn('title', 'string')
                ->addColumn('title_slug', 'string')
                ->addColumn('content', 'text')
                ->addColumn('create_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
                ->addColumn('update_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
                ->addColumn('is_publish', 'integer', ['default' => 0])
                ->addColumn('publish_at', 'datetime', ['null' => true])
                ->addColumn('deleted', 'integer', ['default' => 0])
                ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
                ->addIndex(['title', 'title_slug'])
                ->create();
    }
}
