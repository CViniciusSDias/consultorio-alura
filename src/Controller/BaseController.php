<?php

namespace App\Controller;

use App\Entity\HypermidiaResponse;
use App\Helper\EntityFactoryInterface;
use App\Helper\RequestDataExtractor;
use Doctrine\Common\Persistence\ObjectRepository;
use Psr\SimpleCache\CacheInterface;
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
    /**
     * @var CacheInterface
     */
    private $cache;

    public function __construct(EntityFactoryInterface $entityFactory, RequestDataExtractor $requestDataExtractor, ObjectRepository $repository, CacheInterface $cache)
    {
        $this->entityFactory = $entityFactory;
        $this->requestDataExtractor = $requestDataExtractor;
        $this->repository = $repository;
        $this->cache = $cache;
    }

    public function novo(Request $request): Response
    {
        $entity = $this->entityFactory->createEntity($request->getContent());
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($entity);
        $entityManager->flush();
        $this->cache->set($this->cachePrefix() . $entity->getId(), $entity);

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

    public function buscarUm(int $id)
    {
        $entity = $this->cache->has($this->cachePrefix() . $id)
            ? $this->cache->get($this->cachePrefix() . $id)
            : $this->repository->find($id);
        $hypermidiaResponse = new HypermidiaResponse($entity, true, Response::HTTP_OK, null);

        return $hypermidiaResponse->getResponse();
    }

    public function atualizar(int $id, Request $request): Response
    {
        $entity = $this->entityFactory->createEntity($request->getContent());
        $existingEntity = $this->updateExistingEntity($id, $entity);

        $this->getDoctrine()->getManager()->flush();

        $this->cache->set($this->cachePrefix() . $id, $existingEntity);

        return $this->json($existingEntity);
    }

    public function deletar(int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $entity = $this->repository->find($id);
        $entityManager->remove($entity);
        $entityManager->flush();

        $this->cache->delete($this->cachePrefix() . $id);

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    abstract protected function updateExistingEntity(int $id, $entity);
    abstract protected function cachePrefix(): string;
}
