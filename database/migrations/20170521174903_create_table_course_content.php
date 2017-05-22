<?php

use Phinx\Migration\AbstractMigration;

class CreateTableCourseContent extends AbstractMigration
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
        $course_content = $this->table('course_content');
        $course_content->addColumn('course_id', 'integer')
                       ->addColumn('title', 'string')
                       ->addColumn('url_video', 'string')
                       ->addColumn('deleted', 'integer', ['default' => 0, 'limit' => 1])
                       ->addForeignKey('course_id', 'courses', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
                       ->create();
    }
}
