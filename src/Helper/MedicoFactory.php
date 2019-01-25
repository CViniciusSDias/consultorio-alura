<?php

namespace App\Helper;

use App\Entity\Medico;

class MedicoFactory
{
    public function criarMedico(string $json): Medico
    {
        $objetoJson = json_decode($json);
        $propriedades = get_object_vars($objetoJson);
        $medico = new Medico();
        foreach ($propriedades as $propriedade => $valor) {
            $medico->$propriedade = $valor;
        }

        return $medico;
    }
}
