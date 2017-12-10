<?php
namespace App\Blog\Table;

use App\Auth\UserTable;
use App\Blog\Entity\Post;
use Framework\Database\Query;
use Framework\Database\Table;

class PostTable extends Table
{


    protected $entity = Post::class;

    protected $table = 'posts';


    public function findAll(): Query
    {
        $category = new CategoryTable($this->pdo);
        $author = new UserTable($this->pdo);
        return $this->makeQuery()
            ->join($category->getTable() . ' as c', 'c.id = p.category_id')
            ->join($author->getTable() . ' as a', 'a.id = p.author_id')
            ->select('p.*, c.name as category_name, c.slug as category_slug, a.username as author_name')
            ->order('p.created_at DESC');
    }


    public function findPublic(): Query
    {
        return $this->findAll()
            ->where('p.published = 1')
            ->where('p.created_at < NOW()');
    }


    public function findPublicForCategory(int $id): Query
    {
        return $this->findPublic()->where("p.category_id = $id");
    }


    public function findWithCategory(int $postId): Post
    {
        return $this->findPublic()->where("p.id = $postId")->fetch();
    }
}
