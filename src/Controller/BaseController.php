<?php

namespace App\Controller;

use App\Entity\HypermidiaResponse;
use App\Helper\EntityFactoryInterface;
use App\Helper\RequestDataExtractor;
use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseController extends AbstractController
{
    /**
     * @var ObjectRepository
     */
    protected $repository;
    /**
     * @var EntityFactoryInterface
     */
    protected $entityFactory;
    /**
     * @var RequestDataExtractor
     */
    protected $requestDataExtractor;

    public function __construct(EntityFactoryInterface $entityFactory, RequestDataExtractor $requestDataExtractor, ObjectRepository $repository)
    {
        $this->entityFactory = $entityFactory;
        $this->requestDataExtractor = $requestDataExtractor;
        $this->repository = $repository;
    }

    public function novo(Request $request): Response
    {
        $entity = $this->entityFactory->createEntity($request->getContent());
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($entity);
        $entityManager->flush();

        return $this->json($entity, Response::HTTP_CREATED);
    }

    public function buscarTodos(Request $request): Response
    {
        try {
            $filterData = $this->requestDataExtractor->getFilterData($request);
            $orderData = $this->requestDataExtractor->getOrderData($request);
            $paginationData = $this->requestDataExtractor->getPaginationData($request);
            $itemsPerPage = $_ENV['ITEMS_PER_PAGE'] ?? 10;

            $entityList = $this->repository->findBy(
                $filterData,
                $orderData,
                $itemsPerPage,
                ($paginationData - 1) * $itemsPerPage
            );

            $hypermidiaResponse = new HypermidiaResponse($entityList, true, Response::HTTP_OK, $paginationData, $itemsPerPage);
        } catch (\Throwable $erro) {
            $hypermidiaResponse = HypermidiaResponse::fromError($erro);
        }

        return $hypermidiaResponse->getResponse();
    }

    public function deletar(int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $entity = $this->repository->find($id);
        $entityManager->remove($entity);
        $entityManager->flush();

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    public function atualizar(int $id, Request $request): Response
    {
        $entity = $this->entityFactory->createEntity($request->getContent());
        $existingEntity = $this->updateExistingEntity($id, $entity);

        $this->getDoctrine()->getManager()->flush();

        return $this->json($existingEntity);
    }

    abstract public function updateExistingEntity(int $id, $entity);
}
