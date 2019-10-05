<?php

namespace App\Controller;

use App\Entity\HypermidiaResponse;
use App\Helper\EntityFactoryInterface;
use App\Helper\RequestDataExtractor;
use Doctrine\Common\Persistence\ObjectRepository;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
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
     * @var CacheItemPoolInterface
     */
    private $cache;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        EntityFactoryInterface $entityFactory,
        RequestDataExtractor $requestDataExtractor,
        ObjectRepository $repository,
        CacheItemPoolInterface $cache,
        LoggerInterface $logger
    ) {
        $this->entityFactory = $entityFactory;
        $this->requestDataExtractor = $requestDataExtractor;
        $this->repository = $repository;
        $this->cache = $cache;
        $this->logger = $logger;
    }

    public function novo(Request $request): Response
    {
        $entity = $this->entityFactory->createEntity($request->getContent());
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($entity);
        $entityManager->flush();

        $cacheItem = $this->cache->getItem(
            $this->cachePrefix() . $entity->getId()
        );
        $cacheItem->set($entity);
        $this->cache->save($cacheItem);

        $this->logger
            ->notice(
                'Novo registro de {entidade} adicionado com id: {id}.',
                [
                    'entidade' => get_class($entity),
                    'id' => $entity->getId(),
                ]
            );

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
        $entity = $this->cache->hasItem($this->cachePrefix() . $id)
            ? $this->cache->getItem($this->cachePrefix() . $id)->get()
            : $this->repository->find($id);
        $hypermidiaResponse = new HypermidiaResponse($entity, true, Response::HTTP_OK, null);

        return $hypermidiaResponse->getResponse();
    }

    public function atualizar(int $id, Request $request): Response
    {
        $entity = $this->entityFactory->createEntity($request->getContent());
        $existingEntity = $this->updateExistingEntity($id, $entity);

        $this->getDoctrine()->getManager()->flush();

        $cacheItem = $this->cache->getItem($this->cachePrefix() . $id);
        $cacheItem->set($existingEntity);
        $this->cache->save($cacheItem);

        return $this->json($existingEntity);
    }

    public function deletar(int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $entity = $this->repository->find($id);
        $entityManager->remove($entity);
        $entityManager->flush();

        $this->cache->deleteItem($this->cachePrefix() . $id);

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    abstract public function updateExistingEntity(int $id, $entity);
    abstract public function cachePrefix(): string;
}
