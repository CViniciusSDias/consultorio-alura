<?php

namespace App\Controller;

use App\Entity\Especialidade;
use App\Helper\EspecialidadeFactory;
use App\Helper\RequestDataExtractor;
use App\Repository\EspecialidadeRepository;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;

class EspecialidadesController extends BaseController
{
    public function __construct(
        EspecialidadeFactory $especialidadeFactory,
        RequestDataExtractor $requestDataExtractor,
        EspecialidadeRepository $repository,
        CacheItemPoolInterface $cache,
        LoggerInterface $logger
    ) {
        parent::__construct($especialidadeFactory, $requestDataExtractor, $repository, $cache, $logger);
    }

    public function updateExistingEntity(int $id, $entity)
    {
        /** @var Especialidade $especialidadeExistente */
        $especialidadeExistente = $this->getDoctrine()->getRepository(Especialidade::class)->find($id);
        $especialidadeExistente->setDescricao($entity->getDescricao());

        return $especialidadeExistente;
    }

    public function cachePrefix(): string
    {
        return 'especialidade_';
    }
}
