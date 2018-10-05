<?php

namespace app\Core;


class Router
{
    /**
     * The application instance.
     *
     * @var \app\Core\Application
     */
    public $app;


    /**
     * All of the routes.
     *
     * @var array
     */
    protected $routes = [];


    /**
     * The route group.
     *
     * @var array
     */
    protected $groupStack = [];


    /**
     * Router constructor.
     *
     * @param  \app\Core\Application $application
     */

    public function __construct(Application $application)
    {
        $this->app = $application;
    }

    /**
     * Register a set of routes with a set of shared attributes.
     *
     * @param  array    $attributes
     * @param  \Closure $callback
     * @return void
     */
    public function group(array $attributes, \Closure $callback)
    {
        if (isset($attributes['middleware']) && is_string($attributes['middleware'])) {
            $attributes['middleware'] = (array)$attributes['middleware'];
        }

        $this->updateGroupStack($attributes);

        call_user_func($callback, $this);

        array_pop($this->groupStack);
    }

    /**
     * Update the group stack with the given attributes.
     *
     * @param  array $attributes
     * @return void
     */
    protected function updateGroupStack(array $attributes)
    {
        if (!empty($this->groupStack)) {
            $attributes = $this->mergeGroup($attributes);
        }

        $this->groupStack[] = $attributes;
    }


    /**
     * Merge the given group attributes with the last added group.
     *
     * @param  array $new
     * @return array
     */
    protected function mergeGroup(array $new)
    {
        $old = end($this->groupStack);

        if (isset($old['prefix'])) {
            $new['prefix'] = trim($old['prefix'], '/') . (isset($new['prefix']) ? '/' . trim($new['prefix'], '/') : '');
        }
        if (isset($old['middleware'])) {
            $new['middleware'] = array_merge($old['middleware'], $new['middleware'] ?? []);
        }
        if (isset($old['namespace'])) {
            $new['namespace'] = $new['namespace'] ?? $old['namespace'];
        }
        return $new;
    }

    /**
     * Add a route to the collection.
     *
     * @param  array|string $method
     * @param  string       $uri
     * @param  mixed        $action
     * @return void
     */
    public function addRoute($method, $uri, $action)
    {
        $action = $this->parseAction($action);
        if (!empty($this->groupStack)) {
            $attributes = $this->mergeGroup($action);
        }

        if (isset($attributes) && is_array($attributes)) {
            if (isset($attributes['prefix'])) {
                $uri = trim($attributes['prefix'], '/') . '/' . trim($uri, '/');
            }
            $action = array_merge($action, $attributes);
        }

        $uri = '/' . trim($uri, '/');

        $this->routes[$method . $uri] = ['method' => $method, 'uri' => $uri, 'action' => $action];

    }


    /**
     * Parse the action into an array format.
     *
     * @param  mixed $action
     * @return array
     */
    protected function parseAction($action)
    {
        if (is_string($action)) {
            return ['uses' => $action];
        } elseif (!is_array($action)) {
            return [$action];
        }
        if (isset($action['middleware']) && is_string($action['middleware'])) {
            $action['middleware'] = (array)$action['middleware'];
        }
        return $action;
    }


    /**
     * Register a route with the application.
     *
     * @param  string $uri
     * @param  mixed  $action
     * @return $this
     */
    public function get($uri, $action)
    {
        $this->addRoute('GET', $uri, $action);

        return $this;
    }

    public function post($uri, $action)
    {
        $this->addRoute('POST', $uri, $action);

        return $this;
    }

    /**
     * @return array
     */
    public function getRoutes()
    {
        return $this->routes;
    }

}