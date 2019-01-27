<?php
  namespace App\Http;
  use App\Events\Test;

  use App\Models\TestModel;
  use Illuminate\View\View;
  use Symfony\Component\HttpFoundation\Request;
  use Symfony\Component\HttpFoundation\Response;
  use Symfony\Component\HttpFoundation\StreamedResponse;
  use Symfony\Component\HttpKernel\EventListener\ExceptionListener;
  use XiangYu2038\FlySky\FlySKy;

  class TestController {
      public function index(Request $request){


          ///触发test 事件
          $dispatcher = FlySKy::getContainer()->make('events');
          $dispatcher -> dispatch(new Test());

         // $a =   TestModel::first();

          $aa = ['da'];
          return FlySKy::getContainer()->make('view')->make('test',compact('aa'));
      }
  }