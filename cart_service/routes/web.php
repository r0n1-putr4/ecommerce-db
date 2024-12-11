<?php

/** @var \Laravel\Lumen\Routing\Router $router tes */

$router->get('/', function () use ($router) {
    return response()->json([
        'versi' => $router->app->version(),
        'welcome' => 'Selamat datang di API Lumen'
    ]);
});


// $router->get('/carts/{productId}', 'CartController@getProduct');
$router->get('/carts', 'CartController@index');
$router->get('/carts/{id}', 'CartController@show');

$router->post('/carts', 'CartController@store');
