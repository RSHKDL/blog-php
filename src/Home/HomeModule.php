<?php
namespace App\Home;

use Framework\Module;
use Framework\Renderer\RendererInterface;
use Framework\Router;

class HomeModule extends Module
{

    public function __construct(Router $router, RendererInterface $renderer)
    {
        $renderer->addPath('home', __DIR__);
        $router->get('/', HomeAction::class, 'home');
    }
}
