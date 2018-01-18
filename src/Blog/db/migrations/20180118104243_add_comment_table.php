<?php


use Phinx\Migration\AbstractMigration;

class AddCommentTable extends AbstractMigration
{

    public function change()
    {
        $this->table('comments')
            ->addColumn('content', 'text', ['limit' => \Phinx\Db\Adapter\MysqlAdapter::TEXT_REGULAR])
            ->addColumn('created_at', 'datetime')
            ->addColumn('author_id', 'integer', ['null' => true])
            ->addForeignKey('author_id', 'users', 'id', [
                'delete' => 'SET NULL'
            ])
            ->addColumn('post_id', 'integer', ['null' => true])
            ->addForeignKey('post_id', 'posts', 'id', [
                'delete' => 'SET NULL'
            ])
            ->create();

    }
}
