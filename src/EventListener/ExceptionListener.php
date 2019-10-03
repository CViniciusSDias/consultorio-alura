<?php

namespace App\EventListener;

use App\Entity\HypermidiaResponse;
use App\Helper\EntityFactoryException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => [
                ['handleEntityFactoryException', 1],
                ['handle404Exception', 0],
            ],
        ];
    }

    public function handle404Exception(GetResponseForExceptionEvent $event)
    {
        if (!$event->getException() instanceof NotFoundHttpException) {
            return;
        }

        $event->setResponse(HypermidiaResponse::fromError($event->getException())->getResponse());
    }

    public function handleEntityFactoryException(GetResponseForExceptionEvent $event)
    {
        if (!$event->getException() instanceof EntityFactoryException) {
            return;
        }

        $event->setResponse(HypermidiaResponse::fromError($event->getException())->getResponse());
    }
}
