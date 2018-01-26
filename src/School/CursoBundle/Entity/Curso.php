<?php

namespace School\CursoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Curso
 *
 * @ORM\Table(name="curso")
 * @ORM\Entity(repositoryClass="School\CursoBundle\Repository\CursoRepository")
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
     * @ORM\Column(type="string", length=250)
     */
    private $nome;

    /**
     * @ORM\Column(type="integer", length=250)
     */
    private $mensualidade;

    /**
     * @ORM\Column(type="integer", length=250)
     */
    private $valorMatricula;

    /**
     * @ORM\Column(type="string", length=250)
     * @Assert\Choice({"matutino", "vespertino", "noturnoo"})
     */
    private $periodo;


    /**
     * @ORM\Column(type="integer", length=250)
     */
    private $mesesDuracao;

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
}
