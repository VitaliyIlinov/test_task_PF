<?php
define('ROOT', __DIR__);

spl_autoload_register(function ($class) {
    include str_replace('\\', '/', ROOT . "/{$class}.php");
});


$app = require_once __DIR__ . '/bootstrap/app.php';

try {
    $app->run();
} catch (Exception $e) {
    (new \app\Core\Response($e->getMessage(),$e->getCode()))->send();
}