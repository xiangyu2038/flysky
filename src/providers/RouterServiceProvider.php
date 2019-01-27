<?php

namespace XiangYu2038\FlySky\providers;


use App\Listeners\KernelView;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Support\ServiceProvider;
use XiangYu2038\FlySky\Listeners\KernelException;
use XiangYu2038\FlySky\Listeners\KernelRequest;
use XiangYu2038\FlySky\Listeners\KernelFinishRequest;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RouterServiceProvider extends ServiceProvider
{

    public function boot()
    {
        /////
    }

    public function register(){
        ////路由监听者
        $matcher = $this -> app -> make(\Symfony\Component\Routing\Matcher\UrlMatcher::class);
        $requestStack = new \Symfony\Component\HttpFoundation\RequestStack();
        $this -> app ->singleton(EventSubscriberInterface::class,function ()use($matcher,$requestStack){
            return new RouterListener($matcher, $requestStack);
        });

    }


}
