<?php

namespace App\Controller;

use App\Entity\HypermidiaResponse;
use App\Entity\Medico;
use App\Helper\MedicoFactory;
use App\Helper\RequestDataExtractor;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MedicosController extends AbstractController
{
    /**
     * @var MedicoFactory
     */
    private $medicoFactory;
    /**
     * @var RequestDataExtractor
     */
    private $requestDataExtractor;

    public function __construct(MedicoFactory $medicoFactory, RequestDataExtractor $requestDataExtractor)
    {
        $this->medicoFactory = $medicoFactory;
        $this->requestDataExtractor = $requestDataExtractor;
    }

    /**
     * @Route("/medicos", methods={"POST"})
     */
    public function novoMedico(Request $request): Response
    {
        $medico = $this->medicoFactory->criarMedico($request->getContent());
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($medico);
        $entityManager->flush();

        return new JsonResponse($medico, Response::HTTP_CREATED);
    }

    /**
     * @Route("/medicos", methods={"GET"})
     */
    public function buscarTodos(Request $request): Response
    {
        try {
            $filterData = $this->requestDataExtractor->getFilterData($request);
            $orderData = $this->requestDataExtractor->getOrderData($request);
            $paginationData = $this->requestDataExtractor->getPaginationData($request);
            $repository = $this->getDoctrine()->getRepository(Medico::class);
            $itemsPerPage = $_ENV['ITEMS_PER_PAGE'] ?? 10;

            $medicos = $repository->findBy(
                $filterData,
                $orderData,
                $itemsPerPage,
                ($paginationData - 1) * $itemsPerPage
            );

            $hypermidiaResponse = new HypermidiaResponse($medicos, true, Response::HTTP_OK, $paginationData, $itemsPerPage);
        } catch (\Throwable $erro) {
            $hypermidiaResponse = HypermidiaResponse::fromError($erro);
        }

        return $hypermidiaResponse->getResponse();
    }

    /**
     * @Route("/medicos/{id}", methods={"DELETE"}, requirements={"id"="\d+"})
     */
    public function deletar(int $id): Response
    {
        $repository = $this->getDoctrine()->getRepository(Medico::class);
        $entityManager = $this->getDoctrine()->getManager();

        $medico = $repository->find($id);
        $entityManager->remove($medico);
        $entityManager->flush();

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route("/medicos/{id}", methods={"PUT"}, requirements={"id"="\d+"})
     */
    public function atualizar(int $id, Request $request): Response
    {
        $medicoEnviado = $this->medicoFactory->criarMedico($request->getContent());
        /** @var Medico $medicoExistente */
        $medicoExistente = $this->getDoctrine()->getRepository(Medico::class)->find($id);
        $medicoExistente->setNome($medicoEnviado->getNome());
        $medicoExistente->setCrm($medicoEnviado->getCrm());
        $medicoExistente->setEspecialidade($medicoEnviado->getEspecialidade());

        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse($medicoExistente);
    }

    /**
     * @Route("/especialidades/{especialidadeId}/medicos", methods={"GET"})
     */
    public function buscarPorEspecialidade(int $especialidadeId, Request $request): Response
    {
        $orderData = $this->requestDataExtractor->getOrderData($request);
        $repository = $this->getDoctrine()->getRepository(Medico::class);
        $medicos = $repository->findBy(['especialidade' => $especialidadeId], $orderData);

        return new JsonResponse($medicos);
    }
}
