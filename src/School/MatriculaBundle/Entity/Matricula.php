<?php

namespace School\MatriculaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use School\CursoBundle\Entity\Curso;
use School\AlunoBundle\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * Matricula
 *
 * @ORM\Table(name="matricula")
 * @ORM\Entity(repositoryClass="School\MatriculaBundle\Repository\MatriculaRepository")
 */
class Matricula
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
     * @ORM\Column(type="integer", length=250)
     */
    private $ano;

    /**
     * @ORM\ManyToOne(targetEntity="\School\CursoBundle\Entity\Curso")
     * @ORM\JoinColumn(name="curso_id", referencedColumnName="id",nullable=false)
     * @Assert\NotBlank
     */
    private $curso;

    /**
     * @ORM\ManyToOne(targetEntity="\School\AlunoBundle\Entity\Aluno")
     * @ORM\JoinColumn(name="aluno_id", referencedColumnName="id" ,nullable=false)
     * @Assert\NotBlank
     */
    private $aluno;

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
     * Set ano
     *
     * @param integer $ano
     *
     * @return Matricula
     */
    public function setAno($ano)
    {
        $this->ano = $ano;

        return $this;
    }

    /**
     * Get ano
     *
     * @return integer
     */
    public function getAno()
    {
        return $this->ano;
    }

    /**
     * Set curso
     *
     * @param \School\CursoBundle\Entity\Curso $curso
     *
     * @return Matricula
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

    /**
     * Set aluno
     *
     * @param \School\AlunoBundle\Entity\Aluno $aluno
     *
     * @return Matricula
     */
    public function setAluno(\School\AlunoBundle\Entity\Aluno $aluno = null)
    {
        $this->aluno = $aluno;

        return $this;
    }

    /**
     * Get aluno
     *
     * @return \School\AlunoBundle\Entity\Aluno
     */
    public function getAluno()
    {
        return $this->aluno;
    }

    public function __toString() {
        return "".$this->ano."-".$this->curso;
    }
}
