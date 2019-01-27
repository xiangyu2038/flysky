<?php

/**
 * 陈翔宇 996163957@qq.com  15000694560
 */

namespace XiangYu2038\FlySky;

use Illuminate\Config\Repository;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;

use Symfony\Component\Routing\RouteCollection;

class FlySKy
{
    protected static $app_path;//当前app目录
    protected static $container;///当前容器

    public static function bootStrap($app_path)
    {
        self::$app_path = $app_path;///app 所在目录
        self::$container = new \Illuminate\Container\Container();;
        ////加载配置
        self::loadConfigurationFiles();
        /////注册核心组件
        self::register();
        return self::$container;
    }

    public static function loadConfigurationFiles()
    {
        $config_path = self::$app_path . '/Config';///配置目录
        $files = self::getConfigurationFiles($config_path);///配置文件列表
        self::$container->instance('config', $repository = new Repository());
        foreach ($files as $key => $path) {
            $repository->set($key, require $path);
        }
        return;
    }

    public static function getConfigurationFiles($config_path)
    {
        //TODO 此处需要进行缓存 提高性能
        $files = [];
        foreach (Finder::create()->files()->name('*.php')->in($config_path) as $file) {
            $files[basename($file->getRealPath(), '.php')] = $file->getRealPath();
        }
        //ksort($files, SORT_NATURAL);
        return $files;
    }

    public static function register()
    {
        /////注册控制器解析
        self::$container->singleton(ControllerResolverInterface::class, ControllerResolver::class);
        self::registerRoute();
        //////注册服务提供者  服务提供者用于对laravel的兼容支持
        self::registerServiceProvider();
        return;
    }

    public static function registerRoute()
    {
        /////注册路由绑定
        $routes = require self::$app_path . '/Routes/route.php';//加载路由配置

        self::$container->singleton(RouteCollection::class, function (\Illuminate\Container\Container $container) use ($routes) {
            return $routes;
        });
        return;
    }

    public static function registerServiceProvider()
    {
        ////注册服务提供者
        $service_provider = self::$container['config']['provider'];
        foreach ($service_provider as $provider) {
            if (method_exists($provider = new $provider(self::$container), 'register')) {
                $provider->register();///注册
            }
            if (method_exists($provider, 'boot')) {
                $provider->boot();///初始化
            }
        }

        return;

    }

    /**
     * 返回当前容器实例
     * @param
     * @return mixed
     */
    public static function getContainer()
    {
        return self::$container;
    }
}
