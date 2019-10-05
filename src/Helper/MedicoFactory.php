<?php

namespace App\Helper;

use App\Entity\Medico;
use App\Repository\EspecialidadeRepository;

class MedicoFactory implements EntityFactoryInterface
{
    /**
     * @var EspecialidadeRepository
     */
    private $especialidadeRepository;

    public function __construct(EspecialidadeRepository $especialidadeRepository)
    {
        $this->especialidadeRepository = $especialidadeRepository;
    }

    public function createEntity(string $json): Medico
    {
        $objetoJson = json_decode($json);
        $this->checkAllProperties($objetoJson);

        $medico = new Medico();
        $medico
            ->setNome($objetoJson->nome)
            ->setCrm($objetoJson->crm)
            ->setEspecialidade($this->especialidadeRepository->find($objetoJson->especialidadeId));

        return $medico;
    }

    private function checkAllProperties(object $objetoJson): void
    {
        if (!property_exists($objetoJson, 'nome')) {
            throw new EntityFactoryException('Médico precisa de nome');
        }

        if (!property_exists($objetoJson, 'crm')) {
            throw new EntityFactoryException('Médico precisa de CRM');
        }

        if (!property_exists($objetoJson, 'especialidadeId')) {
            throw new EntityFactoryException('Médico precisa especialidade');
        }
    }
}
