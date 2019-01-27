<?php
namespace XiangYu2038\FlySky\Listeners;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\EventListener\ExceptionListener;
class OnkernelException {
    public $routeLister;
    public function __construct(EventSubscriberInterface $routeLister)
    {
        $this -> routeLister = $routeLister;
    }


    public function handle($event)
    {
        dd(__LINE__);
        $this -> routeLister =  new ExceptionListener(
            'Calendar\\Controller\\handle::exceptionAction');
        $this -> routeLister -> onKernelException($event);
    }
}
