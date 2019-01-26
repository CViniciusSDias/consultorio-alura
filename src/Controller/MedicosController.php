<?php

namespace App\Controller;

use App\Entity\Medico;
use App\Helper\MedicoFactory;
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

    public function __construct(MedicoFactory $medicoFactory)
    {
        $this->medicoFactory = $medicoFactory;
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
    public function buscarTodos(): Response
    {
        $repository = $this->getDoctrine()->getRepository(Medico::class);
        return new JsonResponse($repository->findAll());
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
}
