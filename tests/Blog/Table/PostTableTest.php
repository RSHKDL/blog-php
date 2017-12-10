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
        $pdo = $this->getPDO();
        $this->migrateDatabase($pdo);
        $this->postTable = new PostTable($pdo);
    }


    public function testFind()
    {
        $this->seedDatabase($this->postTable->getPdo());
        $post = $this->postTable->find(1);
        $this->assertInstanceOf(Post::class, $post);
    }


    public function testUpdate()
    {
        $this->seedDatabase($this->postTable->getPdo());
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
}
