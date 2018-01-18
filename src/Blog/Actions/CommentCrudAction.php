<?php
namespace App\Blog\Actions;

use App\Auth\UserTable;
use App\Blog\Entity\Comment;
use App\Blog\Table\CommentTable;
use App\Blog\Table\PostTable;
use Framework\Actions\CrudAction;
use Framework\Auth;
use Framework\Renderer\RendererInterface;
use Framework\Response\RedirectResponse;
use Framework\Router;
use Framework\Session\FlashService;
use Framework\Database\Hydrator;
use Framework\Database\Table;
use Framework\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CommentCrudAction extends CrudAction
{

    protected $routePrefix = "blog.comment";

    protected $acceptedParams = ['content'];

    /**
     * @var UserTable
     */
    private $userTable;

    /**
     * @var PostTable
     */
    private $postTable;

    /**
     * @var Auth
     */
    private $auth;


    public function __construct(
        RendererInterface $renderer,
        Router $router,
        CommentTable $table,
        FlashService $flash,
        PostTable $postTable,
        UserTable $userTable,
        Auth $auth
    ) {
        parent::__construct($renderer, $router, $table, $flash);
        $this->postTable = $postTable;
        $this->userTable = $userTable;
        $this->auth = $auth;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        return $this->create($request);
    }

    protected function getNewEntity()
    {
        $comment = new Comment();
        $comment->created_at = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
        return $comment;
    }

    /**
     * Filter the parameters received by the request
     *
     * @param ServerRequestInterface $request
     * @param Comment $comment
     * @return array
     */
    protected function getParams(ServerRequestInterface $request, $comment): array
    {
        $params = $request->getParsedBody();
        $postId = $request->getAttribute('id');
        $user = $this->auth->getUser();
        $params['post_id'] = $postId;
        $params['author_id'] = $user->id;
        $datetime = new \DateTime('now');
        $params['created_at'] = $datetime->format('Y-m-d H:i:s');
        $params = array_filter($params, function ($key) {
            return in_array($key, [
                'content',
                'created_at',
                'post_id',
                'author_id'
            ]);
        }, ARRAY_FILTER_USE_KEY);
        return $params;
    }

    /**
     * Create a new record
     *
     * @param ServerRequestInterface$request
     * @return ResponseInterface|string
     */
    public function create(ServerRequestInterface $request)
    {
        $item = $this->getNewEntity();
        $postSlug = $request->getAttribute('slug');
        $postId = $request->getAttribute('id');
        if ($request->getMethod() === 'POST') {
            $validator = (new Validator($this->getParams($request, $item)))
                ->required('content')
                ->length('content', 2, 140);
            if ($validator->isValid()) {
                $this->table->insert($this->getParams($request, $item));
                return $this->redirect('blog.show', [
                    'slug' => $postSlug,
                    'id' => $postId
                ]);
            }
            Hydrator::hydrate($request->getParsedBody(), $item);
            $errors = $validator->getErrors();
        }
        return $this->redirect('blog.show', [
            'errors' => $errors,
            'slug' => $postSlug,
            'id' => $postId
        ]);

        /* return $this->renderer->render(
            $this->viewPath . '/create',
            $this->formParams(compact('item', 'errors'))
        );*/
    }

}