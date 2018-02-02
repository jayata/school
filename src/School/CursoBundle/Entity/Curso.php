<?php

namespace School\CursoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Curso
 *
 * @ORM\Table(name="curso",uniqueConstraints={@ORM\UniqueConstraint(name="IDX_NOMBRE_PERIODO", columns={"nome", "periodo"})}))
 * @ORM\Entity(repositoryClass="School\CursoBundle\Repository\CursoRepository")
 * * @UniqueEntity(fields={"nome", "periodo"})
 */
class Curso
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
     * @var int
     *
     * @ORM\Column(name="id_imported", type="integer",nullable=True)
     *
     */
    private $idImported;

    /**
     * @ORM\Column(type="string", length=250)
     * @Assert\NotBlank()
     *
     */
    private $nome;

    /**
     * @ORM\Column(type="string", length=250)
     * @Assert\NotBlank()
     */
    private $descripcao;

    /**
     * @ORM\Column(type="integer", length=250)
     * @Assert\Range(
     *      min = 1,
     *      minMessage = "Must be an a value bigger than 0",
     * )
     */
    private $mensualidade;

    /**
     * @ORM\Column(type="integer", length=250)
     * @Assert\Range(
     *      min = 1,
     *      minMessage = "Must be an a value bigger than 0",
     * )
     */
    private $valorMatricula;

    /**
     * @ORM\Column(type="string", length=250)
     * @Assert\Choice({"matutino", "vespertino", "noturno"})
     */
    private $periodo;


    /**
     * @ORM\Column(type="integer", length=250)
     * @Assert\Range(
     *      min = 1,
     *      max = 12,
     *      minMessage = "Must be at least {{ limit }} month",
     *      maxMessage = "Must be no more than {{ limit }} months",
     * )
     */
    private $mesesDuracao;

    /**
     * @ORM\OneToMany(targetEntity="School\MatriculaBundle\Entity\Matricula", mappedBy="curso", orphanRemoval=true, cascade={"remove"})
     *
     */
    protected $matriculasAbiertas;

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
     * @return mixed
     */
    public function getIdImported()
    {
        return $this->idImported;
    }

    /**
     * @param mixed $idImported
     *
     * * @return Curso
     */
    public function setIdImported($idImported)
    {
        $this->idImported = $idImported;
        return $this;
    }

    /**


    /**
     * Set nome
     *
     * @param string $nome
     *
     * @return Curso
     */
    public function setNome($nome)
    {
        $this->nome = $nome;

        return $this;
    }

    /**
     * Get nome
     *
     * @return string
     */
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * Set descripcao
     *
     * @param string $descripcao
     *
     * @return Curso
     */
    public function setDescripcao($descripcao)
    {
        $this->descripcao = $descripcao;

        return $this;
    }

    /**
     * Get descripcao
     *
     * @return string
     */
    public function getDescripcao()
    {
        return $this->descripcao;
    }

    /**
     * Set mensualidade
     *
     * @param integer $mensualidade
     *
     * @return Curso
     */
    public function setMensualidade($mensualidade)
    {
        $this->mensualidade = $mensualidade;

        return $this;
    }

    /**
     * Get mensualidade
     *
     * @return integer
     */
    public function getMensualidade()
    {
        return $this->mensualidade;
    }

    /**
     * Set valorMatricula
     *
     * @param integer $valorMatricula
     *
     * @return Curso
     */
    public function setValorMatricula($valorMatricula)
    {
        $this->valorMatricula = $valorMatricula;

        return $this;
    }

    /**
     * Get valorMatricula
     *
     * @return integer
     */
    public function getValorMatricula()
    {
        return $this->valorMatricula;
    }

    /**
     * Set periodo
     *
     * @param integer $periodo
     *
     * @return Curso
     */
    public function setPeriodo($periodo)
    {
        $this->periodo = $periodo;

        return $this;
    }

    /**
     * Get periodo
     *
     * @return integer
     */
    public function getPeriodo()
    {
        return $this->periodo;
    }

    /**
     * Set mesesDuracao
     *
     * @param integer $mesesDuracao
     *
     * @return Curso
     */
    public function setMesesDuracao($mesesDuracao)
    {
        $this->mesesDuracao = $mesesDuracao;

        return $this;
    }

    /**
     * Get mesesDuracao
     *
     * @return integer
     */
    public function getMesesDuracao()
    {
        return $this->mesesDuracao;
    }

    public function __toString() {
        return $this->nome;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->matriculasAbiertas = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add matriculasAbierta
     *
     * @param \School\MatriculaBundle\Entity\Matricula $matriculasAbierta
     *
     * @return Curso
     */
    public function addMatriculasAbierta(\School\MatriculaBundle\Entity\Matricula $matriculasAbierta)
    {
        $this->matriculasAbiertas[] = $matriculasAbierta;

        return $this;
    }

    /**
     * Remove matriculasAbierta
     *
     * @param \School\MatriculaBundle\Entity\Matricula $matriculasAbierta
     */
    public function removeMatriculasAbierta(\School\MatriculaBundle\Entity\Matricula $matriculasAbierta)
    {
        $this->matriculasAbiertas->removeElement($matriculasAbierta);
    }

    /**
     * Get matriculasAbiertas
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMatriculasAbiertas()
    {
        return $this->matriculasAbiertas;
    }
}
