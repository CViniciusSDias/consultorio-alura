<?php

namespace App\EventListener;

use App\Entity\HypermidiaResponse;
use App\Helper\EntityFactoryException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
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
                ['handleGenericException', -1]
            ],
        ];
    }

    public function handle404Exception(GetResponseForExceptionEvent $event)
    {
        if (!$event->getException() instanceof NotFoundHttpException) {
            return;
        }

        $response = HypermidiaResponse::fromError($event->getException())->getResponse();
        $response->setStatusCode(Response::HTTP_NOT_FOUND);
        $event->setResponse($response);
    }

    public function handleEntityFactoryException(GetResponseForExceptionEvent $event)
    {
        if (!$event->getException() instanceof EntityFactoryException) {
            return;
        }

        $response = HypermidiaResponse::fromError($event->getException())->getResponse();
        $response->setStatusCode(Response::HTTP_BAD_REQUEST);
        $event->setResponse($response);
    }

    public function handleGenericException(GetResponseForExceptionEvent $event)
    {
        $event->setResponse(HypermidiaResponse::fromError(new \Exception('Um erro inesperado ocorreu'))->getResponse());
    }
}
