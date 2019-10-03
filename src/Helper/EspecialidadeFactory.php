<?php

namespace App\Helper;

use App\Entity\Especialidade;

class EspecialidadeFactory implements EntityFactoryInterface
{
    public function createEntity(string $json): Especialidade
    {
        $objetoJson = json_decode($json);
        $this->checkIfDescriptionExists($objetoJson);

        $especialidade = new Especialidade();
        $especialidade->setDescricao($objetoJson->descricao);

        return $especialidade;
    }

    /**
     * @param $objetoJson
     * @throws EntityFactoryException
     */
    private function checkIfDescriptionExists($objetoJson): void
    {
        if (!property_exists($objetoJson, 'descricao')) {
            throw new EntityFactoryException('A descrição de uma especialidade é obrigatória');
        }
    }
}
