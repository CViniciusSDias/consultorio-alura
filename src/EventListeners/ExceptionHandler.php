<?php

namespace App\EventListeners;

use App\Entity\HypermidiaResponse;
use App\Helper\EntityFactoryException;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionHandler implements EventSubscriberInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => [
                ['handleEntityException', 1],
                ['handle404Exception', 0],
                ['handleGenericException', -1],
            ],
        ];
    }

    public function handle404Exception(ExceptionEvent $event)
    {
        if ($event->getException() instanceof NotFoundHttpException) {
            $response = HypermidiaResponse::fromError($event->getException())
                ->getResponse();
            $response->setStatusCode(Response::HTTP_NOT_FOUND);
            $event->setResponse($response);
        }
    }

    public function handleEntityException(ExceptionEvent $event)
    {
        if ($event->getException() instanceof EntityFactoryException) {
            $response = HypermidiaResponse::fromError($event->getException())
                ->getResponse();
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            $event->setResponse($response);
        }
    }

    public function handleGenericException(ExceptionEvent $event)
    {
        $this->logger->critical('Uma exceção ocorreu. {stack}', [
            'stack' => $event->getException()->getTraceAsString()
        ]);

        $response = HypermidiaResponse::fromError($event->getException());
        $event->setResponse($response->getResponse());
    }
}
