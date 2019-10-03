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
        $this->checkIfAllPropertiesExist($objetoJson);

        $especialidade = $this->especialidadeRepository->find($objetoJson->especialidadeId);
        if (is_null($especialidade)) {
            throw new EntityFactoryException('Especialidade inexistente');
        }

        $medico = new Medico();
        $medico
            ->setNome($objetoJson->nome)
            ->setCrm($objetoJson->crm)
            ->setEspecialidade($especialidade);

        return $medico;
    }

    /**
     * @param $objetoJson
     * @throws EntityFactoryException
     */
    private function checkIfAllPropertiesExist($objetoJson): void
    {
        if (
            !property_exists($objetoJson, 'nome')
            || !property_exists($objetoJson, 'crm')
            || !property_exists($objetoJson, 'especialidadeId')
        ) {
            throw new EntityFactoryException('Nome, CRM e Especialidade de um médico são obrigatórios');
        }
    }
}
