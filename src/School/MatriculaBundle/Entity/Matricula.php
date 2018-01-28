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
     * @ORM\Column(type="integer", length=4)
     */
    private $ano;

    /**
     * @ORM\Column(type="boolean")
     */
    private $ativa;



    /**
     * @ORM\ManyToOne(targetEntity="\School\CursoBundle\Entity\Curso")
     * @ORM\JoinColumn(name="curso_id", referencedColumnName="id",nullable=false)
     * @Assert\NotBlank
     */
    private $curso;

    /**
     * @ORM\OneToMany(targetEntity="School\MatriculaBundle\Entity\MatriculaAluno", mappedBy="matricula")
     */
    private $alunosMatriculados;

    public function __construct() {
        $this->alunosMatriculados = new \Doctrine\Common\Collections\ArrayCollection();
    }

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




    public function __toString() {
        return "".$this->ano."-".$this->curso;
    }

    /**
     * Set ativa
     *
     * @param boolean $ativa
     *
     * @return Matricula
     */
    public function setAtiva($ativa)
    {
        $this->ativa = $ativa;

        return $this;
    }

    /**
     * Get ativa
     *
     * @return boolean
     */
    public function getAtiva()
    {
        return $this->ativa;
    }



    /**
     * Add alunosMatriculado
     *
     * @param \School\MatriculaBundle\Entity\MatriculaAluno $alunosMatriculado
     *
     * @return Matricula
     */
    public function addAlunosMatriculado(\School\MatriculaBundle\Entity\MatriculaAluno $alunosMatriculado)
    {
        $this->alunosMatriculados[] = $alunosMatriculado;

        return $this;
    }

    /**
     * Remove alunosMatriculado
     *
     * @param \School\MatriculaBundle\Entity\MatriculaAluno $alunosMatriculado
     */
    public function removeAlunosMatriculado(\School\MatriculaBundle\Entity\MatriculaAluno $alunosMatriculado)
    {
        $this->alunosMatriculados->removeElement($alunosMatriculado);
    }

    /**
     * Get alunosMatriculados
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAlunosMatriculados()
    {
        return $this->alunosMatriculados;
    }
}
