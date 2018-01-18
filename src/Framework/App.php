<?php
namespace Framework;

use App\Framework\Middleware\RoutePrefixedMiddleware;
use DI\ContainerBuilder;
use Doctrine\Common\Cache\FilesystemCache;
use Framework\Middleware\CombinedMiddleware;
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
     * @var array
     */
    private $definitions;


    /**
     * @var ContainerInterface
     */
    private $container;


    /**
     * @var string[]
     */
    private $middlewares = [];


    /**
     * @var int
     */
    private $index = 0;


    /**
     * App constructor.
     * @param string|array|null $definitions
     */
    public function __construct($definitions = [])
    {
        if (is_string($definitions)) {
            $definitions = [$definitions];
        }
        if (!$this->isSequential($definitions)) {
            $definitions = [$definitions];
        }
        $this->definitions = $definitions;
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
     * @param string|callable|MiddlewareInterface $routePrefix
     * @param null|string|callable|MiddlewareInterface $middleware
     * @return App
     */
    public function pipe($routePrefix, $middleware = null): self
    {
        if ($middleware === null) {
            $this->middlewares[] = $routePrefix;
        } else {
            $this->middlewares[] = new RoutePrefixedMiddleware(
                $this->getContainer(),
                $routePrefix,
                $middleware
            );
        }
        return $this;
    }


    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws \Exception
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->index++;
        if ($this->index > 1) {
            throw new \Exception();
        }
        $middleware = new CombinedMiddleware($this->getContainer(), $this->middlewares);
        return $middleware->process($request, $this);
    }


    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws \Exception
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function run(ServerRequestInterface $request): ResponseInterface
    {
        foreach ($this->modules as $module) {
            $this->getContainer()->get($module);
        }
        return $this->handle($request);
    }


    /**
     * Build the container
     *
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        if ($this->container === null) {
            $builder = new ContainerBuilder();
            $env = getenv('ENV') ?: 'production';
            if ($env === 'production') {
                $builder->setDefinitionCache(new FilesystemCache('tmp/di'));
                $builder->writeProxiesToFile(true, 'tmp/proxies');
            }
            foreach ($this->definitions as $definition) {
                $builder->addDefinitions($definition);
            }
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
     * @return array
     */
    public function getModules(): array
    {
        return $this->modules;
    }


    /**
     * Check if an array is sequential
     *
     * @param array $array
     * @return bool
     */
    private function isSequential(array $array): bool
    {
        if (empty($array)) {
            return true;
        }
        return array_keys($array) === range(0, count($array) - 1);
    }
}
