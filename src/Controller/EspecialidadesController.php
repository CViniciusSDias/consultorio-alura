<?php

namespace App\Controller;

use App\Entity\Especialidade;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class EspecialidadesController extends AbstractController
{
    /**
     * @Route("/especialidades", name="especialidades", methods={"GET"})
     */
    public function buscarTodas()
    {
        return $this->json($this->getDoctrine()->getRepository(Especialidade::class)->findAll());
    }
}
