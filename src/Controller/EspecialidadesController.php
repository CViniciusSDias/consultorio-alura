<?php

namespace App\Controller;

use App\Entity\Especialidade;
use App\Helper\EspecialidadeFactory;
use App\Helper\RequestDataExtractor;
use App\Repository\EspecialidadeRepository;
use Psr\SimpleCache\CacheInterface;

class EspecialidadesController extends BaseController
{
    public function __construct(EspecialidadeFactory $especialidadeFactory, RequestDataExtractor $requestDataExtractor, EspecialidadeRepository $repository, CacheInterface $cache)
    {
        parent::__construct($especialidadeFactory, $requestDataExtractor, $repository, $cache);
    }

    protected function updateExistingEntity(int $id, $entity)
    {
        /** @var Especialidade $especialidadeExistente */
        $especialidadeExistente = $this->getDoctrine()->getRepository(Especialidade::class)->find($id);
        $especialidadeExistente->setDescricao($entity->getDescricao());

        return $especialidadeExistente;
    }

    protected function cachePrefix(): string
    {
        return 'especialidade_';
    }
}
