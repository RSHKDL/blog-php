<?php

namespace App\Blog\Actions;

use App\Blog\Table\CategoryTable;
use App\Blog\Table\CommentTable;
use App\Blog\Table\PostTable;
use Framework\Actions\RouterAwareAction;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class PostShowAction
{


    /**
     * @var RendererInterface
     */
    private $renderer;


    /**
     * @var Router
     */
    private $router;


    /**
     * @var PostTable
     */
    private $postTable;

    /**
     * @var CommentTable
     */
    private $commentTable;


    use RouterAwareAction;


    public function __construct(
        RendererInterface $renderer,
        Router $router,
        PostTable $postTable,
        CommentTable $commentTable
    ) {
        $this->renderer = $renderer;
        $this->router = $router;
        $this->postTable = $postTable;
        $this->commentTable = $commentTable;
    }


    /**
     * Display one post
     *
     * @param Request $request
     * @return ResponseInterface|string
     */
    public function __invoke(Request $request)
    {
        $slug = $request->getAttribute('slug');
        $post = $this->postTable->findWithCategory($request->getAttribute('id'));
        $comments = $this->commentTable->findCommentsForPost($request->getAttribute('id'));
        if ($comments->valid() == false) {
            $comments = null;
        }
        if ($post->slug !== $slug) {
            return $this->redirect('blog.show', [
                'slug' => $post->slug,
                'id' => $post->id
            ]);
        }
        return $this->renderer->render('@blog/show', [
            'post' => $post,
            'comments' => $comments
        ]);
    }
}
