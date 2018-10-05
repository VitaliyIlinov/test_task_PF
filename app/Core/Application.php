<?php

namespace app\Core;


use app\Exceptions\MethodNotAllowedHttpException;
use app\Exceptions\NotFoundHttpException;

class Application
{
    /**
     * All allowed middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [];

    /**
     * The base path of the application installation.
     *
     * @var string
     */
    protected $basePath;

    /**
     * Matched route of route list.
     *
     * @var string
     */
    protected $currentRoute = [];


    /**
     * The Router instance.
     *
     * @var \app\Core\Router
     */
    public $router;

    /**
     * The Request instance.
     *
     * @var \app\Core\Request
     */
    public $request;

    /**
     * The Request instance.
     *
     * @var \app\Core\Response
     */
    public $response;


    public function __construct($basePath)
    {
        $this->basePath = $basePath;
        $this->registerErrorHandling();
        $this->bootstrapRouter();
        $this->bootstrapRequest();
    }

    /**
     * Set the error handling for the application.
     *
     * @return void
     */
    protected function registerErrorHandling()
    {
        error_reporting(E_ALL);
        set_error_handler(function ($level, $message, $file = '', $line = 0) {
            if (error_reporting() & $level) {
                throw new \ErrorException($message, 403, $level, $file, $line);
            }
        });

        set_exception_handler(function ($e) {
            echo($e);
        });

        register_shutdown_function(function () {
            echo(error_get_last());
        });
    }

    /**
     * @return void
     */
    public function bootstrapRouter()
    {
        $this->router = new Router($this);
    }

    /**
     * @return void
     */
    public function bootstrapRequest()
    {
        $this->request = new Request();
    }


    /**
     * Define the route middleware for the application.
     *
     * @param  array $middleware
     * @return $this
     */
    public function routeMiddleware(array $middleware)
    {
        $this->routeMiddleware = array_merge($this->routeMiddleware, $middleware);

        return $this;
    }

    /**
     * Gather the full class names for the middleware.
     *
     * @param  array $middleware
     * @return array
     */
    protected function gatherMiddlewareClassNames($middleware)
    {
        return array_map(function ($name) {
            return $this->routeMiddleware[$name];
        }, $middleware);
    }

    protected function runMiddleware($middleware)
    {
        foreach ($middleware as $class) {
            $object = $this->makeClass($class);
            $object->handle($this);
        }
    }

    /**
     *
     * Call a controller
     *
     * @param $routeInfo
     * @return mixed
     * @throws NotFoundHttpException
     *
     */
    protected function callControllerAction($routeInfo)
    {

        $uses = $routeInfo['uses'];
        list($controller, $method) = explode('@', $uses);

        $class = "{$routeInfo['namespace']}\\{$controller}";

        if (!method_exists($object = $this->makeClass($class), $method)) {
            throw new NotFoundHttpException;
        }
        return call_user_func_array([$object, $method], $routeInfo['args'] ?? []);
    }

    /**
     * Run the application and send the response.
     * @return mixed
     * @throws MethodNotAllowedHttpException
     * @throws NotFoundHttpException
     */
    public function run()
    {
        $routeInfo = $this->foundRoute();

        if (isset($routeInfo['middleware'])) {
            $middleware = $this->gatherMiddlewareClassNames($routeInfo['middleware']);
            $this->runMiddleware($middleware);
        }
        $response = $this->callControllerAction($routeInfo);

        $this->sendRequest($this->response ?: $response);

    }

    protected function sendRequest($response)
    {
        if (!$response instanceof Response) {
            $response = new Response($response);
        }
        $response->send();
    }

    protected function foundRoute()
    {
        $routes = $this->router->getRoutes();

        list($method, $pathInfo) = [$this->request->getMethod(), '/' . trim($this->request->getPathInfo(), '/')];

        if (isset($routes[$method . $pathInfo])) {
            $this->currentRoute = $routes[$method . $pathInfo];
        } else {
            $this->currentRoute = $this->getRouteByPattern($pathInfo);
            if ($this->request->getMethod() != $this->currentRoute['method']) {
                throw new MethodNotAllowedHttpException($this->currentRoute['action']['uses']);
            }
        }
        return $this->currentRoute['action'];
    }

    /**
     * @param string $pathInfo
     * @return mixed
     * @throws NotFoundHttpException
     */
    protected function getRouteByPattern($pathInfo)
    {
        foreach ($this->router->getRoutes() as $v) {
            $str = preg_replace('/(\{)(.*?)(\})/', '(?P<$2>[^\/]*?)', $v['uri']);
            if (preg_match("#^{$str}$#", $pathInfo, $args)) {
                $v['action']['args'] = array_filter($args, function ($v, $k) {
                    return is_string($k);
                }, ARRAY_FILTER_USE_BOTH);
                return $v;
            }
        }
        throw new NotFoundHttpException();
    }

    protected function makeClass($class)
    {
        return (new \ReflectionClass($class))->newInstance();
    }
}