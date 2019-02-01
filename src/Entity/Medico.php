<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Medico
 * @package App\Entity
 * @ORM\Entity(repositoryClass="App\Repository\MedicoRepository")
 * @ORM\Table("medicos")
 */
class Medico implements \JsonSerializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\Column(type="string")
     */
    private $nome;
    /**
     * @ORM\Column(type="integer")
     */
    private $crm;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Especialidade")
     * @ORM\JoinColumn(nullable=false)
     */
    private $especialidade;

    public function getEspecialidade(): ?Especialidade
    {
        return $this->especialidade;
    }

    public function setEspecialidade(?Especialidade $especialidade): self
    {
        $this->especialidade = $especialidade;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function setNome(string $nome): self
    {
        $this->nome = $nome;
        return $this;
    }

    public function getCrm(): string
    {
        return $this->crm;
    }

    public function setCrm(string $crm): self
    {
        $this->crm = $crm;
        return $this;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'nome' => $this->getNome(),
            'crm' => $this->getCrm(),
            'especialidadeId' => $this->getEspecialidade()->getId(),
            '_links' => [
                [
                    'rel' => 'self',
                    'path' => '/medicos/' . $this->getId()
                ],
                [
                    'rel' => 'especialidades',
                    'path' => '/especialidades/' . $this->getEspecialidade()->getId()
                ]
            ]
        ];
    }
}
