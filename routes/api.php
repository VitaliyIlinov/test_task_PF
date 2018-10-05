<?php



/**
 * @var $router app\Core\Router
 */

$router->get('/', 'UserController@showAll');
$router->get('/{id}', 'UserController@show');