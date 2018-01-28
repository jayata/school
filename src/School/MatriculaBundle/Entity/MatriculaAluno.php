<?php

namespace School\MatriculaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MatriculaAluno
 *
 * @ORM\Table(name="matricula_aluno")
 * @ORM\Entity(repositoryClass="School\MatriculaBundle\Repository\MatriculaAlunoRepository")
 */
class MatriculaAluno
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
     * @ORM\ManyToOne(targetEntity="School\AlunoBundle\Entity\Aluno")
     * @ORM\JoinColumn(name="aluno_id", referencedColumnName="id")
     */
    private $aluno;

    /**
     * @ORM\ManyToOne(targetEntity="School\MatriculaBundle\Entity\Matricula")
     * @ORM\JoinColumn(name="matricula_id", referencedColumnName="id")
     */
    private $matricula;

    /**
     * @ORM\Column(type="boolean")
     */
    private $paga = false;

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
     * Set aluno
     *
     * @param \School\AlunoBundle\Entity\Aluno $aluno
     *
     * @return MatriculaAluno
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

    /**
     * Set matricula
     *
     * @param \School\MatriculaBundle\Entity\Matricula $matricula
     *
     * @return MatriculaAluno
     */
    public function setMatricula(\School\MatriculaBundle\Entity\Matricula $matricula = null)
    {
        $this->matricula = $matricula;

        return $this;
    }

    /**
     * Get matricula
     *
     * @return \School\MatriculaBundle\Entity\Matricula
     */
    public function getMatricula()
    {
        return $this->matricula;
    }


    /**
     * Set paga
     *
     * @param boolean $paga
     *
     * @return Matricula
     */
    public function setPaga($paga)
    {
        $this->paga = $paga;

        return $this;
    }

    /**
     * Get paga
     *
     * @return boolean
     */
    public function getPaga()
    {
        return $this->paga;
    }

    function __toString()
    {
        // TODO: Implement __toString() method.
        return "";
    }
}
