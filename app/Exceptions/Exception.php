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
