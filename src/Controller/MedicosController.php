<?php

namespace App\Controller;

use App\Entity\Medico;
use App\Helper\MedicoFactory;
use App\Helper\RequestDataExtractor;
use App\Repository\MedicoRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MedicosController extends BaseController
{
    public function __construct(MedicoFactory $medicoFactory, RequestDataExtractor $requestDataExtractor, MedicoRepository $repository)
    {
        parent::__construct($medicoFactory, $requestDataExtractor, $repository);
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

    public function updateExistingEntity(int $id, $entity)
    {
        /** @var Medico $medicoExistente */
        $medicoExistente = $this->getDoctrine()->getRepository(Medico::class)->find($id);
        $medicoExistente->setNome($entity->getNome());
        $medicoExistente->setCrm($entity->getCrm());
        $medicoExistente->setEspecialidade($entity->getEspecialidade());

        return $medicoExistente;
    }
}
