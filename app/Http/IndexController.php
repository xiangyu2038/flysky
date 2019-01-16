<?php
  namespace App\Http;
  use Symfony\Component\HttpFoundation\Request;

  class IndexController {
      public function index(Request $request){
dd(__LINE__);
      }
  }