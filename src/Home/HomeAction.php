<?php
namespace App\Home;

use Framework\Renderer\RendererInterface;
use Framework\Response\RedirectResponse;
use Psr\Http\Message\ServerRequestInterface;

class HomeAction
{


    /**
     * @var RendererInterface
     */
    private $renderer;


    public function __construct(RendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * @param ServerRequestInterface $request
     * @return RedirectResponse|string
     */
    public function __invoke(ServerRequestInterface $request)
    {
        return $this->renderer->render('@home/home');
    }
}
