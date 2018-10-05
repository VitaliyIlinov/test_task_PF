<?php

namespace app\Core;


class Request
{
    /**
     * Request body parameters ($_POST).
     */
    public $request;

    /**
     * Request query parameters ($_GET).
     */
    public $query;

    /**
     * Server and execution environment parameters ($_SERVER).
     *
     */
    public $server;

    /**
     * Cookies ($_COOKIE).
     */
    public $cookies;

    /**
     * Headers (taken from the $_SERVER)
     */
    public $headers;

    /**
     * @var mixed
     */
    public $content;

    /**
     * @var string
     */
    protected $method;


    public function __construct()
    {
        $this->server = $_SERVER;
        $this->query = $_GET;
        $this->request = $_POST;
        $this->cookies = $_COOKIE;
        $this->headers = $this->getHeaders();
    }

    /**
     * Gets the HTTP headers.
     *
     * @return array
     */
    public function getHeaders()
    {
        $headers = [];
        foreach ($this->server as $key => $value) {
            if (0 === strpos($key, 'HTTP_')) {
                $headers[substr($key, 5)] = $value;
            }
        }

        if(isset($this->server['PHP_AUTH_USER'])){
            $headers['PHP_AUTH_USER'] = $this->server['PHP_AUTH_USER'] ?? '';
        }
        if(isset($this->server['PHP_AUTH_PW'])){
            $headers['PHP_AUTH_PW'] = $this->server['PHP_AUTH_PW'] ?? '';
        }

        return $headers;
    }

    /**
     * Get the HTTP headers.
     *
     * @return string
     */

    public function getHeader($key, $default = null)
    {
        $headers = $this->headers;

        if (!array_key_exists($key, $headers)) {
            return $default;
        }

        return $headers[$key];
    }

    public function getServer($key, $default = null)
    {
        return isset ($this->server[$key]) ? $this->server[$key] : $default;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        if (null === $this->method) {
            $this->method = strtoupper($this->getServer('REQUEST_METHOD', 'GET'));
        }

        return $this->method;
    }

    /**
     * @return string path info
     */
    public function getPathInfo()
    {
        if (null === ($requestUri = $this->getServer('REQUEST_URI'))) {
            return '/';
        }

        // Remove the query string from REQUEST_URI
        if (false !== $pos = strpos($requestUri, '?')) {
            $requestUri = substr($requestUri, 0, $pos);
        }

        return $requestUri;
    }


}