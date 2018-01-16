<?php
namespace Framework\Twig;

class UrlExtension extends \Twig_Extension
{


    public function __construct()
    {
    }


    public function getFunctions(): array
    {
        return [
            new \Twig_SimpleFunction('base_url', [$this, 'getBaseUrl'])
        ];
    }

    public function getBaseUrl()
    {
        return $_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'];
    }
}
