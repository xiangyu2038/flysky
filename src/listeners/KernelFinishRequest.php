<?php
namespace XiangYu2038\FlySky\Listeners;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
class KernelFinishRequest {
    public $routeLister;
    public function __construct(EventSubscriberInterface $routeLister)
    {
       $this -> routeLister = $routeLister;
    }


    public function handle($event)
    {
        $this -> routeLister -> onKernelFinishRequest($event);
    }
}
