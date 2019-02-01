<?php

namespace App\Controller;

use App\Entity\Especialidade;
use App\Helper\EspecialidadeFactory;
use App\Helper\RequestDataExtractor;
use App\Repository\EspecialidadeRepository;

class EspecialidadesController extends BaseController
{
    public function __construct(EspecialidadeFactory $especialidadeFactory, RequestDataExtractor $requestDataExtractor, EspecialidadeRepository $repository)
    {
        parent::__construct($especialidadeFactory, $requestDataExtractor, $repository);
    }

    public function updateExistingEntity(int $id, $entity)
    {
        /** @var Especialidade $especialidadeExistente */
        $especialidadeExistente = $this->getDoctrine()->getRepository(Especialidade::class)->find($id);
        $especialidadeExistente->setDescricao($entity->getDescricao());

        return $especialidadeExistente;
    }
}
