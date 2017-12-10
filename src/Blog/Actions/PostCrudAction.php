<?php

namespace App\Blog\Actions;

use App\Auth\UserTable;
use App\Blog\Entity\Post;
use App\Blog\PostUpload;
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

    /**
     * @var UserTable
     */
    private $userTable;

    /**
     * @var PostUpload
     */
    private $postUpload;


    public function __construct(
        RendererInterface $renderer,
        Router $router,
        PostTable $table,
        FlashService $flash,
        CategoryTable $categoryTable,
        UserTable $userTable,
        PostUpload $postUpload
    ) {
        parent::__construct($renderer, $router, $table, $flash);
        $this->categoryTable = $categoryTable;
        $this->postUpload = $postUpload;
        $this->userTable = $userTable;
    }


    public function delete(ServerRequestInterface $request)
    {
        $post = $this->table->find($request->getAttribute('id'));
        $this->postUpload->delete($post->image);
        return parent::delete($request);
    }


    protected function formParams(array $params): array
    {
        $params['categories'] = $this->categoryTable->findList();
        $params['users'] = $this->userTable->findUserList();
        return $params;
    }


    protected function getNewEntity()
    {
        $post = new Post();
        $post->created_at = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
        return $post;
    }


    /**
     * Filter the parameters received by the request
     *
     * @param ServerRequestInterface $request
     * @param Post $post (data of the post before modification)
     * @return array
     */
    protected function getParams(ServerRequestInterface $request, $post): array
    {
        $params = array_merge($request->getParsedBody(), $request->getUploadedFiles());
        // Upload the file
        $image = $this->postUpload->upload($params['image'], $post->image);
        if ($image) {
            $params['image'] = $image;
        } else {
            unset($params['image']);
        }
        $params = array_filter($params, function ($key) {
            return in_array($key, [
                'title',
                'slug',
                'header',
                'content',
                'created_at',
                'category_id',
                'image',
                'published',
                'author_id'
            ]);
        }, ARRAY_FILTER_USE_KEY);
        return array_merge($params, ['updated_at' => date('Y-m-d H:i:s')]);
    }


    protected function getValidator(ServerRequestInterface $request)
    {
        $validator = parent::getValidator($request)
            ->required('title', 'slug', 'header', 'content', 'created_at', 'category_id')
            ->length('title', 2, 250)
            ->length('slug', 2, 120)
            ->length('header', 2)
            ->length('content', 40)
            ->exists('category_id', $this->categoryTable->getTable(), $this->categoryTable->getPdo())
            ->exists('author_id', $this->userTable->getTable(), $this->userTable->getPdo())
            ->extension('image', ['jpg', 'png'])
            ->dateTime('created_at')
            ->slug('slug');
        if (is_null($request->getAttribute('id'))) {
            $validator->uploaded('image');
        }
        return $validator;
    }
}
