<?php

namespace App\Helper;

use App\Entity\Medico;
use App\Repository\EspecialidadeRepository;

class MedicoFactory
{
    /**
     * @var EspecialidadeRepository
     */
    private $especialidadeRepository;

    public function __construct(EspecialidadeRepository $especialidadeRepository)
    {
        $this->especialidadeRepository = $especialidadeRepository;
    }

    public function criarMedico(string $json): Medico
    {
        $objetoJson = json_decode($json);
        $medico = new Medico();
        $medico
            ->setNome($objetoJson->nome)
            ->setCrm($objetoJson->crm)
            ->setEspecialidade($this->especialidadeRepository->find($objetoJson->especialidadeId));

        return $medico;
    }
}
