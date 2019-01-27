<?php
namespace XiangYu2038\FlySky;


use Illuminate\Config\Repository;
use Illuminate\Contracts\Events\Dispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\HttpKernel\EventListener\StreamedResponseListener;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Symfony\Component\Routing\RouteCollection;
use XiangYu2038\FlySky\Listeners\KernelException;
use XiangYu2038\FlySky\Listeners\KernelFinishRequest;
use XiangYu2038\FlySky\Listeners\KernelRequest;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\HttpKernel\Controller\ArgumentResolverInterface;

class Bootstrap {
    protected static $app_path;//当前app目录
    protected static $container;///容器目录

    public static function bootStrap($app_path){
        self::$app_path = $app_path;///app 所在目录
        self::$container = new \Illuminate\Container\Container();;
         ////加载配置文件
        self::loadConfigurationFiles();
        /////注册核心组件
        self::register();

        ////创建事件调度
        self::dispatcher();

        return self::$container;
    }

    public static function loadConfigurationFiles(){
        $config_path = self::$app_path.'/Config';///配置目录
        $files =self::getConfigurationFiles($config_path);///配置文件列表
        self::$container->instance('config', $repository = new Repository());
        foreach ($files as $key => $path) {
            $repository->set($key, require $path);
        }

    }

    public static function getConfigurationFiles($config_path){
       //TODO 此处需要进行缓存 提高性能
        $files = [];
        foreach (Finder::create()->files()->name('*.php')->in($config_path) as $file) {
            $files[basename($file->getRealPath(), '.php')] = $file->getRealPath();
        }
        //ksort($files, SORT_NATURAL);
        return $files;
    }

    public static function register(){
        /////注册监听事件
        self::$container ->singleton(EventDispatcherInterface::class,EventDispatcher\EventDispatcher::class);

        /////注册控制器解析
        self::$container ->singleton(ControllerResolverInterface::class,ControllerResolver::class);

        ////注册方法反射
        //self::$container ->singleton(ArgumentResolverInterface::class,ArgumentResolver::class);
         ////注册路由
        self::registerRoute();
         //////注册服务提供者  服务提供者用于对laravel的
        self::registerServiceProvider();
        return;
    }
    public static function registerRoute(){
        /////注册路由绑定
        $routes = require self::$app_path.'/Routes/route.php';//加载路由配置

        self::$container->singleton(RouteCollection::class, function (\Illuminate\Container\Container $container)use($routes) {
            return $routes;
        });
        return;
    }


    public static function dispatchers(){
        ////分发事件
        $dispatcher = self::$container -> make(\Symfony\Component\EventDispatcher\EventDispatcherInterface::class);


        self::$container ->singleton(DispatcherContract::class,function ()use($dispatcher){
            return $dispatcher;
        });

        $matcher = self::$container -> make(\Symfony\Component\Routing\Matcher\UrlMatcher::class);
        $requestStack = new \Symfony\Component\HttpFoundation\RequestStack();
        ///添加路由订阅者
        ///

        $dispatcher->addSubscriber(new \Symfony\Component\HttpKernel\EventListener\RouterListener($matcher, $requestStack));

        return;
    }

    public static function dispatcher(){
        ////分发事件
        $dispatcher =self::$container -> make('events');
        //////为反射添加条件
        self::$container ->singleton(DispatcherContract::class,function ()use($dispatcher){
            return $dispatcher;
        });

        $matcher = self::$container -> make(\Symfony\Component\Routing\Matcher\UrlMatcher::class);
        $requestStack = new \Symfony\Component\HttpFoundation\RequestStack();
        ///添加路由订阅者
        ///

        ////反射事件
        self::$container ->singleton(EventSubscriberInterface::class,function ()use($matcher,$requestStack){
            return new RouterListener($matcher, $requestStack);
        });

        //////监听 行为事件
        $dispatcher -> listen('kernel.request',KernelRequest::class);
        $dispatcher -> listen('kernel.finish_reques',KernelFinishRequest::class);
        $dispatcher -> listen('kernel.exception',KernelException::class);

//dd($dispatcher);
        // dd($dispatcher);
         return;
}

    public static function registerServiceProvider(){
        ////注册服务提供者
       $service_provider = self::$container['config']['serviceprovider'];
       foreach ($service_provider as $provider){
           if (method_exists($provider =new $provider(self::$container), 'register')) {
               $provider->register();///注册
           }
           if (method_exists($provider, 'boot')) {
               $provider->boot();///初始化
           }

       }

       return;

    }

    public static function test(){
        return self::$container;
    }


}
