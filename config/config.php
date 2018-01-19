<?php

use Framework\Middleware\CsrfMiddleware;
use \Framework\Renderer\RendererInterface;
use \Framework\Renderer\TwigRendererFactory;
use Framework\Router;
use Framework\Router\RouterFactory;
use Framework\Router\RouterTwigExtension;
use Framework\Session\PHPSession;
use Framework\Session\SessionInterface;
use Framework\Twig\{
    CsrfExtension,
    FlashExtension,
    FormExtension,
    PagerFantaExtension,
    TextExtension,
    TimeExtension,
    UrlExtension
};

return [
    'env' => \DI\env('ENV', 'production'),
    'database.host' => 'localhost',
    'database.username' => 'root',
    'database.password' => '',
    'database.name' => 'demo_debug',
    'views.path' => dirname(__DIR__) . '/views',
    'twig.extensions' => [
        \DI\get(RouterTwigExtension::class),
        \DI\get(PagerFantaExtension::class),
        \DI\get(TextExtension::class),
        \DI\get(TimeExtension::class),
        \DI\get(FlashExtension::class),
        \DI\get(FormExtension::class),
        \DI\get(CsrfExtension::class),
        \DI\get(UrlExtension::class)
    ],
    SessionInterface::class => \DI\object(PHPSession::class),
    CsrfMiddleware::class => \DI\object()->constructor(\DI\get(SessionInterface::class)),
    Router::class => \DI\factory(RouterFactory::class),
    RendererInterface::class => \DI\factory(TwigRendererFactory::class),
    \PDO::class => function(\Psr\Container\ContainerInterface $c) {
        return new PDO(
            'mysql:host=' . $c->get('database.host') . ';dbname=' . $c->get('database.name'),
            $c->get('database.username'),
            $c->get('database.password'),
            [
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]
        );
    },
    // MAILER
    'mail.to' => 'your_email',
    'mail.username' => 'your_username',
    'mail.password' => 'your_password',
    'mail.host' => 'your_host',
    'mail.port' => 0, // use integer
    'mail.security' => 'your_host_security', // ex: ssl
    Swift_Mailer::class => \DI\factory(\Framework\SwiftMailerFactory::class)
];