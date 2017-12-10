<?php


use Phinx\Migration\AbstractMigration;

class AddUserIdToPost extends AbstractMigration
{
    public function change()
    {
        $this->table('posts')
            ->addColumn('author_id', 'integer', ['null' => true])
            ->addForeignKey('author_id', 'users', 'id', [
                'delete' => 'SET NULL'
            ])
            ->update();
    }
}
