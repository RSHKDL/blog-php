<?php

namespace App\Blog\Actions;

use App\Blog\Entity\Post;
use App\Blog\Table\CategoryTable;
use App\Blog\Table\PostTable;
use Framework\Actions\CrudAction;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Framework\Session\FlashService;
use Psr\Http\Message\ServerRequestInterface;

class PostCrudAction extends CrudAction
{


    protected $viewPath = "@blog/admin/posts";


    protected $routePrefix = "blog.admin";


    /**
     * @var CategoryTable
     */
    private $categoryTable;


    public function __construct(
        RendererInterface $renderer,
        Router $router,
        PostTable $table,
        FlashService $flash,
        CategoryTable $categoryTable
    ) {
        parent::__construct($renderer, $router, $table, $flash);
        $this->categoryTable = $categoryTable;
    }


    protected function formParams(array $params): array
    {
        $params['categories'] = $this->categoryTable->findList();
        return $params;
    }


    protected function getNewEntity()
    {
        $post = new Post();
        $post->created_at = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
        return $post;
    }


    protected function getParams(ServerRequestInterface $request): array
    {
        $params = array_filter($request->getParsedBody(), function ($key) {
            return in_array($key, ['title', 'slug', 'header', 'content', 'created_at', 'category_id']);
        }, ARRAY_FILTER_USE_KEY);
        return array_merge($params, [
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }


    protected function getValidator(ServerRequestInterface $request)
    {
        return parent::getValidator($request)
            ->required('title', 'slug', 'header', 'content', 'created_at', 'category_id')
            ->length('title', 2, 250)
            ->length('slug', 2, 120)
            ->length('header', 2)
            ->length('content', 40)
            ->exists('category_id', $this->categoryTable->getTable(), $this->categoryTable->getPdo())
            ->dateTime('created_at')
            ->slug('slug');
    }
}
