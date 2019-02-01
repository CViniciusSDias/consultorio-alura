<?php

namespace App\Controller;

use App\Entity\Especialidade;
use App\Entity\HypermidiaResponse;
use App\Helper\EspecialidadeFactory;
use App\Helper\RequestDataExtractor;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EspecialidadesController extends AbstractController
{
    /**
     * @var EspecialidadeFactory
     */
    private $especialidadeFactory;
    /**
     * @var RequestDataExtractor
     */
    private $requestDataExtractor;

    public function __construct(EspecialidadeFactory $especialidadeFactory, RequestDataExtractor $requestDataExtractor)
    {
        $this->especialidadeFactory = $especialidadeFactory;
        $this->requestDataExtractor = $requestDataExtractor;
    }

    /**
     * @Route("/especialidades", name="nova_especialidade", methods={"POST"})
     */
    public function novaEspecialidade(Request $request): Response
    {
        $especialidade = $this->especialidadeFactory->criaEspecialidade($request->getContent());
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($especialidade);
        $entityManager->flush();

        return $this->json($especialidade, Response::HTTP_CREATED);
    }

    /**
     * @Route("/especialidades", name="especialidades", methods={"GET"})
     */
    public function buscarTodas(Request $request)
    {
        try {
            $filterData = $this->requestDataExtractor->getFilterData($request);
            $orderData = $this->requestDataExtractor->getOrderData($request);
            $paginationData = $this->requestDataExtractor->getPaginationData($request);
            $repository = $this->getDoctrine()->getRepository(Especialidade::class);
            $itemsPerPage = $_ENV['ITEMS_PER_PAGE'] ?? 10;

            $especialidades = $repository->findBy(
                $filterData,
                $orderData,
                $itemsPerPage,
                ($paginationData - 1) * $itemsPerPage
            );

            $hypermidiaResponse = new HypermidiaResponse($especialidades, true, Response::HTTP_OK, $paginationData, $itemsPerPage);
        } catch (\Throwable $erro) {
            $hypermidiaResponse = HypermidiaResponse::fromError($erro);
        }

        return $hypermidiaResponse->getResponse();
    }

    /**
     * @Route("/especialidades/{id}", name="remover_especialidade", methods={"DELETE"})
     */
    public function deletar(int $id): Response
    {
        $repository = $this->getDoctrine()->getRepository(Especialidade::class);
        $entityManager = $this->getDoctrine()->getManager();
        $especialidade = $repository->find($id);
        $entityManager->remove($especialidade);
        $entityManager->flush();

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route("/especialidades/{id}", name="atualizar_especialidade", methods={"PUT"})
     */
    public function atualizar(int $id, Request $request)
    {
        $especialidadeEnviada = $this->especialidadeFactory->criaEspecialidade($request->getContent());
        /** @var Especialidade $especialidadeExistente */
        $especialidadeExistente = $this->getDoctrine()->getRepository(Especialidade::class)->find($id);
        $especialidadeExistente->setDescricao($especialidadeEnviada->getDescricao());
        $this->getDoctrine()->getManager()->flush();

        return $this->json($especialidadeExistente);
    }
}
