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
     * @Assert\NotEqualTo(
     *     value = 0
     * )
     */
    private $mensualidade;

    /**
     * @ORM\Column(type="integer", length=250)
     *  @Assert\NotEqualTo(
     *     value = 0
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
     *  @Assert\NotEqualTo(
     *     value = 0
     * )
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
}
