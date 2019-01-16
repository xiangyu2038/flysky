<?php

use Symfony\Component\HttpFoundation\Request;
// 自动加载
require __DIR__.'/../vendor/autoload.php';

$routes = require __DIR__.'/../app/Routes/route.php';//加载路由配置

$container = \XiangYu2038\FlySky\Bootstrap::bootStrap($routes);///启动程序

////处理请求
$response = $container->make(\Symfony\Component\HttpKernel\HttpKernel::class)->handle(Request::createFromGlobals());

$response->send();