## flysky 一个使用IOC容器设计思想的PHP框架

为了使本框架尽可能简洁,框架本身仅仅只提供IOC容器($container)  HTTP请求模块($request)  路由分发模块($routes) 事件($dispatcher)  ,其他例如数据库查询,视图模板等可以使用其他框架的优秀模块.例如database使用laravel的Eloquent ORM,view使用laravel的blade模板引擎.本框架兼容laravel的各个模块,只需要用composer安装对应模块,然后在配置文件中添加服务提供者即可.

### 安装
 ```sh
 composer create-project xiangyu2038/flysky
 ```
### 配置
配置文件在app/Config目录下 
配置文件示例
```php
////app/Config/config.php
<?php
  return ['test'=>'test'];
  ```

获取配置文件代码如下  
```php
  <?php
  use XiangYu2038\FlySky\FlySKy;
  $config = FlySKy::getContainer()->make('config') -> get('config.test');
  ```
  其中config 为配置文件名称,test为配置文件的键
  
  ### 路由 
   路由配置文件在app/Routes目录下 示例代码如下
```php
<?php

// 路由配置文件
use Symfony\Component\Routing;

$routes = new Routing\RouteCollection();

$routes->add('test', new Routing\Route('/test/{hello}', array(
    'year' => null,
    '_controller' => 'App\Http\TestController::index',
)));

return $routes;
  ```
 ### 控制器
  控制器在app/Http目录下 示例代码如下
  ```php
  <?php
  namespace App\Http;
  
  use Symfony\Component\HttpFoundation\Request;
  
  class TestController
  {
      public function index(Request $request)
      {
          return 'test';
      }
  }
   ```
   $request 为当前请求对象
  ### 事件 
  框架的事件采用的是laravel的事件模块,所以和laravel的使用方法一致
  事件目录在app/Events  
  事件编写示例 
  ```php
  <?php
  ///app/Events/test.php
  namespace App\Events;
  class Test
  {
      public $order;
      /**
       * 创建一个事件实例。
       *
       * @return void
       */
      public function __construct()
      {
  
      }
  }
   ```
  创建监听者 目录在app/Listeners 示例
  ```php
  <?php
  ///////app/Listeners/test.php
  namespace App\Listeners;
  
  class Test {
      public function __construct()
      {
          //
      }
  
  
      public function handle($event)
      {
  
          //......各种实现
      }
  }

   ```
   创建完事件和监听者后 需要在事件提供者中配置事件和对应的监听者
   示例
   ```php
   <?php
    ///////app/Providers/EventServiceProvider.php
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
    ///////app/Exceptions/Exceptions.php
   ```
   框架本身已定义好四种事件 分别是kernel.request,kernel.finish_request,kernel.exception,kernel.view四个事件,您可以根据自己的需求改写这些事件
   ### 异常处理
   当程序发生异常时,会触发kernel.exception事件,最终会把异常抛给Exception进行处理,用户可以根据自己抛出异常的类型进行相应的处理
   示例
   ```php
   
  
    <?php
    namespace App\Exceptions;
    use Symfony\Component\Debug\Exception\FlattenException;
    class Exception {
        public function __construct()
        {
            //
        }
    
    
        public function handle(FlattenException $exception)
        {
    
            dd($exception);
            //......各种实现
        }
    }

   ```
   ### 服务提供者
   集成第三方服务(尤其是laravel的服务) 可以使用服务提供者 目录在app/Config/provider
   示例 
   ```php
   <?php
   ////app/Config/provider/provider.php
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
   ```
   ### 数据库查询
   本框架没有集成数据库查询功能,用户可以根据个人喜好选择第三方用的顺手的数据库查询功能
   本示例将演示如何安装laravel的Eloquent ORM模型进行查询
   首先 安装Eloquent ORM模块 
   ```sh
    composer require illuminate/database
   ```
   增加服务提供者 在服务提供者种增加配置 
   ```php
       <?php
    [
        ////
        Illuminate\Database\DatabaseServiceProvider::class
        /////
        ];//数据库引擎
  ```
  增加对应的数据库配置文件 database.php 
  此配置文件直接从laravel中复制过来即可
  
  大功告成 现在可以使用laravel的ORM了
  
  ### 视图 
  同样没有集成视图 本示例将演示如何安装laravel的blade模板引擎.
  安装 
  ```sh
      composer require illuminate/view
   ```
  增加服务提供者 在服务提供者种增加配置 
   ```php
         <?php
      [
          ////
          Illuminate\View\ViewServiceProvider::class,///模板引擎
          /////
          ];
   ```
  增加对应的数据库配置文件 view.php 
    此配置文件直接从laravel中复制过来即可
    
   大功告成 现在可以使用laravel的blade模板引擎了




 [我的github地址 ](https://github.com/xiangyu2038/).
