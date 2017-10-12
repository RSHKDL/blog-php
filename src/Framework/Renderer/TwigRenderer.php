<?php

namespace Framework\Renderer;

class TwigRenderer implements RendererInterface
{


    private $twig;


    private $loader;


    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * Create a path to load views
     * @param string $namespace
     * @param null|string $path
     */
    public function addPath(string $namespace, ?string $path = null): void
    {
        $this->twig->getLoader()->addPath($path, $namespace);
    }

    /**
     * Render a view
     * The path can be specified with namespace (prefixed by '@') added by addPath(), example :
     * $this->render(@blog/view); or $this->render(view);
     * @param string $view
     * @param array $params
     * @return string
     */
    public function render(string $view, array $params = []): string
    {
        return $this->twig->render($view . '.twig', $params);
    }

    /**
     * Allow globals variables to be added in all views
     * @param string $key
     * @param mixed $value
     */
    public function addGlobal(string $key, $value): void
    {
        $this->twig->addGlobal($key, $value);
    }
}
