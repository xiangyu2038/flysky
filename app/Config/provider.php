<?php

////服务提供者
return

      [

          Illuminate\Events\EventServiceProvider::class,
          /////////

          App\Providers\EventServiceProvider::class,//事件提供者
          XiangYu2038\FlySky\providers\RouterServiceProvider::class,///路由监听提供者



          ///////第三方组件 可按需注册
          Illuminate\Filesystem\FilesystemServiceProvider::class,//文件引擎

          Illuminate\View\ViewServiceProvider::class,///模板引擎

          Illuminate\Database\DatabaseServiceProvider::class,//数据库引擎
          ///////第三方组件

      ];