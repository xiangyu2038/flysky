<?php
namespace XiangYu2038\FlySky;

use Symfony\Component\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\Routing\RouteCollection;
class Bootstrap {

    public static function bootStrap($routes){
        $container = new \Illuminate\Container\Container();
        /////注册监听事件
        $container ->singleton(EventDispatcherInterface::class,EventDispatcher\EventDispatcher::class);
        /////注册控制器解析
        $container ->singleton(ControllerResolverInterface::class,ControllerResolver::class);
        /////注册路由绑定
        $container->singleton(RouteCollection::class, function (\Illuminate\Container\Container $container)use($routes) {
            return $routes;
        });

        ////分发事件
        $dispatcher = $container -> make(\Symfony\Component\EventDispatcher\EventDispatcherInterface::class);

        $matcher = $container -> make(\Symfony\Component\Routing\Matcher\UrlMatcher::class);
        $requestStack = new \Symfony\Component\HttpFoundation\RequestStack();
        ///添加路由订阅者
        $dispatcher->addSubscriber(new \Symfony\Component\HttpKernel\EventListener\RouterListener($matcher, $requestStack));

        return $container;
    }
}
