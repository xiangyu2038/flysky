<?php

use Symfony\Component\HttpFoundation\Request;

// 自动加载
require __DIR__.'/../vendor/autoload.php';

$container = \XiangYu2038\FlySky\FlySKy::bootStrap(__DIR__.'/../app');///启动程序

////处理请求


//$container->make(\Symfony\Component\HttpKernel\HttpKernel::class)->handle(Request::createFromGlobals())->send();
$container->make(\XiangYu2038\FlySky\kernel\Kernel::class)->handle(Request::createFromGlobals())->send();




