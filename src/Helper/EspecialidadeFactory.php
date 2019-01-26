<?php

namespace App\Helper;

use App\Entity\Especialidade;

class EspecialidadeFactory
{
    public function criaEspecialidade(string $json): Especialidade
    {
        $objetoJson = json_decode($json);
        $especialidade = new Especialidade();
        $especialidade->setDescricao($objetoJson->descricao);

        return $especialidade;
    }
}
