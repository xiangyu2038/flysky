<?php

namespace App\Providers;
use App\Events\Test;

use XiangYu2038\FlySky\Listeners\KernelView;
use XiangYu2038\FlySky\providers\EventServiceProvider as EventService;


use XiangYu2038\FlySky\Listeners\KernelException;
use XiangYu2038\FlySky\Listeners\KernelRequest;
use XiangYu2038\FlySky\Listeners\KernelFinishRequest;

class EventServiceProvider extends EventService
{
    protected $dispatcher;///
    /**
     * 事件监听
     *
     * @var array
     */
    protected $listen = [
        'kernel.request' => [
            KernelRequest::class,
        ],
        'kernel.finish_request' => [
            KernelFinishRequest::class,
        ],
        'kernel.exception' => [
            KernelException::class,
        ],
        'kernel.view' => [
            KernelView::class,
        ],
        Test::class => [
            \App\Listeners\Test::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
     parent::boot();
    }

    public function register(){
        parent::register();
    }
}
