<?php
namespace App\Resume;

use Framework\Module;
use Framework\Renderer\RendererInterface;
use Framework\Router;

class ResumeModule extends Module
{

    public function __construct(Router $router, RendererInterface $renderer)
    {
        $renderer->addPath('resume', __DIR__ . '/views');
        $router->get('/resume', ResumeAction::class, 'resume');
    }
}
