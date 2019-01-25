<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Medico
 * @package App\Entity
 * @ORM\Entity()
 * @ORM\Table("medicos")
 */
class Medico
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    public $id;
    /**
     * @ORM\Column(type="string")
     */
    public $nome;
    /**
     * @ORM\Column(type="integer")
     */
    public $crm;
}
