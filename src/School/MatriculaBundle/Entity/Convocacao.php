<?php

namespace School\MatriculaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Convocacao
 *
 * @ORM\Table(name="convocacao")
 * @ORM\Entity(repositoryClass="School\MatriculaBundle\Repository\ConvocacaoRepository")
 */
class Convocacao
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="\School\CursoBundle\Entity\Curso")
     * @ORM\JoinColumn(name="curso_id", referencedColumnName="id")
     */
    private $curso;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set curso
     *
     * @param \School\CursoBundle\Entity\Curso $curso
     *
     * @return Convocacao
     */
    public function setCurso(\School\CursoBundle\Entity\Curso $curso = null)
    {
        $this->curso = $curso;

        return $this;
    }

    /**
     * Get curso
     *
     * @return \School\CursoBundle\Entity\Curso
     */
    public function getCurso()
    {
        return $this->curso;
    }

    public function __toString() {
        return "".$this->id;
    }
}
