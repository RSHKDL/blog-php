<?php
namespace Framework;

use DI\ContainerBuilder;
use Interop\Http\Server\MiddlewareInterface;
use Interop\Http\Server\RequestHandlerInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class App
 * @package Framework
 */
class App implements RequestHandlerInterface
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
            throw new \Exception("Aucun middleware n'a intercepté cette requête.");
        } elseif (is_callable($middleware)) {
            return call_user_func_array($middleware, [$request, [$this, 'process']]);
        } elseif ($middleware instanceof MiddlewareInterface) {
            return $middleware->process($request, $this);
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
     * @return object
     */
    public function getMiddleware()
    {
        if (array_key_exists($this->index, $this->middlewares)) {
            $middleware = $this->container->get($this->middlewares[$this->index]);
            $this->index++;
            return $middleware;
        }
        return null;
    }


    /**
     * Handle the request and return a response.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->process($request);
    }
}
