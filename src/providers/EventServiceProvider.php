<?php

namespace XiangYu2038\FlySky\providers;



use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Support\ServiceProvider;
class EventServiceProvider extends ServiceProvider
{
    protected $dispatcher;///
    /**
     * 事件监听
     *
     * @var array
     */
    protected $listen = [

    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //////监听
        $this -> dispatcher = $this -> app -> make('events');
        foreach ($this->listen as $event => $listeners) {
            foreach ($listeners as $listener) {
                $this -> dispatcher -> listen($event,$listener);
            }
        }
    }

    public function register(){
        //////注册事件绑定 为反射创建条件
        $this -> app ->singleton(DispatcherContract::class,function (){
            return $this -> dispatcher;
        });

    }
}
