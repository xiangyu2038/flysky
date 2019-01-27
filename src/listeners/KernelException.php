<?php
namespace XiangYu2038\FlySky\Listeners;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\EventListener\ExceptionListener;
class KernelException {
    public $routeLister;
    public function __construct()
    {
        $this -> ExceptionLister = new ExceptionListener(
            'App\\Exceptions\\Exception::handle');
    }

    public function handle($event)
    {
        $this -> ExceptionLister -> onKernelException($event);
    }
}
