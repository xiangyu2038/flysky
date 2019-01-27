<?php

// 路由配置文件
use Symfony\Component\Routing;



$routes->add('test', new Routing\Route('/test/{hello}', array(
    'year' => null,
    '_controller' => 'App\Http\TestController::index',
)));

return $routes;