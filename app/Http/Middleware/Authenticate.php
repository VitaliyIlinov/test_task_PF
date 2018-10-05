<?php

namespace app\Http\Middleware;

use app\Core\Application;
use app\Core\Response;
use app\Http\Middleware\Interfaces\iMiddleware;
use app\Models\UserAuth;

class Authenticate implements iMiddleware
{
    public function handle(Application $app)
    {
        $user = $app->request->getHeader('PHP_AUTH_USER');
        $password = $app->request->getHeader('PHP_AUTH_PW');

        $auth = current(UserAuth::find($user));

        if (empty($user) && empty($password) || !($user == $auth['name'] && $password == $auth['password'])) {
            $headers = ['WWW-Authenticate' => 'Basic realm="Access denied"'];
            $app->response = new Response('Unauthorized.', 401, $headers);
        }
    }
}