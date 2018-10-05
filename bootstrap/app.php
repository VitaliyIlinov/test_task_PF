<?php

$app = new \app\Core\Application(ROOT);

$app->routeMiddleware([
    'Authenticate' => \app\Http\Middleware\Authenticate::class,
]);

$app->router->group(['namespace' => 'app\Http\Controllers','middleware'=>'Authenticate'], function ($router) {
    require ROOT . '/routes/api.php';
});


return $app;