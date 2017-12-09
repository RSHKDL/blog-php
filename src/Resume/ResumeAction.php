<?php
namespace App\Resume;

use Framework\Renderer\RendererInterface;
use Framework\Response\RedirectResponse;
use Psr\Http\Message\ServerRequestInterface;

class ResumeAction
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
        if ($request->getMethod() === 'GET') {
            return $this->renderer->render('@resume/resume');
        } else {
            return $this->renderer->render('@resume/resume');
        }
    }
}
