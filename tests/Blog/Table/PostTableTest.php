<?php
namespace Tests\App\Blog\Table;

use App\Blog\Entity\Post;
use App\Blog\Table\PostTable;
use Tests\DatabaseTestCase;


class PostTableTest extends DatabaseTestCase
{


    /**
     * @var PostTable
     */
    private $postTable;


    public function setUp()
    {
        parent::setUp();
        $this->postTable = new PostTable($this->pdo);
    }


    public function testFind()
    {
        $this->seedDatabase();
        $post = $this->postTable->find(1);
        $this->assertInstanceOf(Post::class, $post);
    }


    public function testFindNotFound()
    {
        $post = $this->postTable->find(999999);
        $this->assertNull($post);
    }


    public function testUpdate()
    {
        $this->seedDatabase();
        $this->postTable->update(1, ['title' => 'Salut bissame', 'slug' => 'demo']);
        $post = $this->postTable->find(1);
        $this->assertEquals('Salut bissame', $post->title);
        $this->assertEquals('demo', $post->slug);
    }


    public function testInsert()
    {
        $this->postTable->insert(['title' => 'Salut bissame', 'slug' => 'demo']);
        $post = $this->postTable->find(1);
        $this->assertEquals('Salut bissame', $post->title);
        $this->assertEquals('demo', $post->slug);
    }


    // public function testDelete()
    // {
    //    $this->postTable->insert(['title' => 'Salut', 'slug' => 'demo']);
    //    $this->postTable->insert(['title' => 'Salut', 'slug' => 'demo']);
    //    $count = $this->pdo->query('SELECT COUNT(id) FROM posts')->fetchColumn();
    //    $this->assertEquals(2, (int)$count);
    //    $this->postTable->delete($this->pdo->lastInsertId());
    //    $count = $this->pdo->query('SELECT COUNT(id) FROM posts')->fetchColumn();
    //    $this->assertEquals(1, (int)$count);
    //}
}
