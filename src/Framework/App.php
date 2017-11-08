<?php
namespace Framework;

use DI\ContainerBuilder;
use GuzzleHttp\Psr7\Response;
use Interop\Http\Server\MiddlewareInterface;
use Interop\Http\Server\RequestHandlerInterface;
use Middlewares\Utils\RequestHandler;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class App
 * @package Framework
 */
class App
{

    /**
     * List of modules
     * @var array
     */
    private $modules = [];


    /**
     * @var string
     */
    private $definition;


    /**
     * @var ContainerInterface
     */
    private $container;


    /**
     * @var string[]
     */
    private $middlewares;


    /**
     * @var int
     */
    private $index = 0;


    public function __construct(string $definition)
    {
        $this->definition = $definition;
    }


    /**
     * Add a module to the application
     *
     * @param string $module
     * @return App
     */
    public function addModule(string $module): self
    {
        $this->modules[] = $module;
        return $this;
    }


    /**
     * Add a middleware
     *
     * @param string $middleware
     * @return App
     */
    public function pipe(string $middleware): self
    {
        $this->middlewares[] = $middleware;
        return $this;
    }


    public function process(ServerRequestInterface $request): ResponseInterface
    {
        $middleware = $this->getMiddleware();
        if (is_null($middleware)) {
            throw new \Exception('No middleware intercepted this request');
        } else {
            return call_user_func_array($middleware, [$request, [$this, 'process']]);
        }
    }


    public function run(ServerRequestInterface $request): ResponseInterface
    {
        foreach ($this->modules as $module) {
            $this->getContainer()->get($module);
        }
        return $this->process($request);
    }


    /**
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        if ($this->container === null) {
            $builder = new ContainerBuilder();
            $builder->addDefinitions($this->definition);
            foreach ($this->modules as $module) {
                if ($module::DEFINITIONS) {
                    $builder->addDefinitions($module::DEFINITIONS);
                }
            }
            $this->container = $builder->build();
        }
        return $this->container;
    }


    /**
     * @return callable|null
     */
    public function getMiddleware(): ?callable
    {
        if (array_key_exists($this->index, $this->middlewares)) {
            $middleware = $this->container->get($this->middlewares[$this->index]);
            $this->index++;
            return $middleware;
        }
        return null;
    }
}
