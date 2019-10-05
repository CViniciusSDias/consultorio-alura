<?php

namespace App\Helper;

use App\Entity\Especialidade;

class EspecialidadeFactory implements EntityFactoryInterface
{
    public function createEntity(string $json): Especialidade
    {
        $objetoJson = json_decode($json);
        if (!property_exists($objetoJson, 'descricao')) {
            throw new EntityFactoryException(
                'Especialidade precisa de descrição'
            );
        }

        $especialidade = new Especialidade();
        $especialidade->setDescricao($objetoJson->descricao);

        return $especialidade;
    }
}
