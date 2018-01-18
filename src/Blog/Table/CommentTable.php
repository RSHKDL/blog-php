<?php
namespace App\Blog\Table;

use App\Auth\UserTable;
use App\Blog\Entity\Comment;
use Framework\Database\Query;
use Framework\Database\Table;

class CommentTable extends Table
{


    protected $entity = Comment::class;

    protected $table = 'comments';

    public function findAllComments(): Query
    {
        $post = new PostTable($this->pdo);
        $author = new UserTable($this->pdo);
        return $this->makeQuery()
            ->join($post->getTable() . ' as p', 'p.id = c.post_id')
            ->join($author->getTable() . ' as a', 'a.id = c.author_id')
            ->select('c.*, a.username as author_name')
            ->order('c.created_at DESC');

    }

    public function findCommentsForPost(int $postId)
    {
        return $this->findAllComments()
            ->where("post_id = $postId")
            ->limit(10)
            ->fetchAll();
    }
}