<?php

namespace app\Http\Middleware\Interfaces;


use app\Core\Application;

interface iMiddleware
{

    /**
     * @param Application $app
     */
    public function handle(Application $app);
}