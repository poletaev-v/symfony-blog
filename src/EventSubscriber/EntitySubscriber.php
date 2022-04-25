<?php

namespace App\EventSubscriber;

use EasyCorp\Bundle\EasyAdminBundle\Event\AbstractLifecycleEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityDeletedEvent;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class EntitySubscriber implements EventSubscriberInterface
{
    private Application $application;

    public function __construct(KernelInterface $kernel)
    {
        $this->application = new Application($kernel);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            AfterEntityPersistedEvent::class => 'onEntityChanged',
            AfterEntityUpdatedEvent::class => 'onEntityChanged',
            AfterEntityDeletedEvent::class => 'onEntityChanged',
        ];
    }

    public function onEntityChanged(AbstractLifecycleEvent $event): void
    {
        $this->application->setAutoExit(false);
        $this->application->run(new StringInput('cache:pool:clear cache.app'), new NullOutput());
    }
}