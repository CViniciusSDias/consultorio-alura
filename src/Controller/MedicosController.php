<?php

namespace App\Controller;

use App\Entity\HypermidiaResponse;
use App\Entity\Medico;
use App\Helper\MedicoFactory;
use App\Helper\RequestDataExtractor;
use App\Repository\MedicoRepository;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MedicosController extends BaseController
{
    public function __construct(
        MedicoFactory $medicoFactory,
        RequestDataExtractor $requestDataExtractor,
        MedicoRepository $repository,
        CacheItemPoolInterface $cache,
        LoggerInterface $logger
    ) {
        parent::__construct($medicoFactory, $requestDataExtractor, $repository, $cache, $logger);
    }

    /**
     * @Route("/especialidades/{especialidadeId}/medicos", methods={"GET"})
     */
    public function buscarPorEspecialidade(int $especialidadeId, Request $request): Response
    {
        $filterData = ['especialidade' => $especialidadeId] + $this->requestDataExtractor->getFilterData($request);
        $orderData = $this->requestDataExtractor->getOrderData($request);
        $paginationData = $this->requestDataExtractor->getPaginationData($request);
        $itemsPerPage = $_ENV['ITEMS_PER_PAGE'] ?? 10;

        $medicos = $this->repository->findBy($filterData, $orderData, $itemsPerPage, ($paginationData - 1) * 10);

        $hypermidiaResponse = new HypermidiaResponse($medicos, true, Response::HTTP_OK, $paginationData, $itemsPerPage);
        return $hypermidiaResponse->getResponse();
    }

    public function updateExistingEntity(int $id, $entity)
    {
        /** @var Medico $medicoExistente */
        $medicoExistente = $this->getDoctrine()->getRepository(Medico::class)->find($id);
        $medicoExistente->setNome($entity->getNome());
        $medicoExistente->setCrm($entity->getCrm());
        $medicoExistente->setEspecialidade($entity->getEspecialidade());

        return $medicoExistente;
    }

    public function cachePrefix(): string
    {
        return 'medico_';
    }
}
