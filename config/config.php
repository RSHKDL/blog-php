<?php

use \Framework\Renderer\RendererInterface;
use \Framework\Renderer\TwigRendererFactory;
use Framework\Router\RouterTwigExtension;

return [
    'database.host' => 'localhost',
    'database.username' => 'root',
    'database.password' => '',
    'database.name' => 'demo_blog_v2',
    'views.path' => dirname(__DIR__) . '/views',
    'twig.extensions' => [
        \DI\get(RouterTwigExtension::class)
    ],
    \Framework\Router::class => \DI\object(),
    RendererInterface::class => \DI\factory(TwigRendererFactory::class)
];