<?php

namespace School\AlunoBundle\Entity;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Aluno
 *
 * @ORM\Table(name="aluno")
 * @ORM\Entity(repositoryClass="School\AlunoBundle\Repository\AlunoRepository")
 */
class Aluno extends BaseUser
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=250, nullable=True)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=250, unique = true,nullable=True)
     *
     * @Assert\NotBlank()
     *
     */
    private $cpf;

    /**
     * @ORM\Column(type="string", length=250,nullable=True)
     */
    private $rg;

    /**
     * @ORM\Column(type="date", length=250,nullable=True)
     *
     * @Assert\Date()
     *
     */
    private $dataNascimento;

    /**
     * @ORM\Column(type="string", length=250,nullable=True)
     */
    private $nome;

    /**
     * @ORM\Column(type="string", length=250,nullable=True)
     */
    private $telefone;

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
     * Set name
     *
     * @param string $name
     *
     * @return Aluno
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set cpf
     *
     * @param string $cpf
     *
     * @return Aluno
     */
    public function setCpf($cpf)
    {
        $this->cpf = $cpf;

        return $this;
    }

    /**
     * Get cpf
     *
     * @return string
     */
    public function getCpf()
    {
        return $this->cpf;
    }

    /**
     * Set rg
     *
     * @param string $rg
     *
     * @return Aluno
     */
    public function setRg($rg)
    {
        $this->rg = $rg;

        return $this;
    }

    /**
     * Get rg
     *
     * @return string
     */
    public function getRg()
    {
        return $this->rg;
    }

    /**
     * Set dataNascimento
     *
     * @param \DateTime $dataNascimento
     *
     * @return Aluno
     */
    public function setDataNascimento($dataNascimento)
    {
        $this->dataNascimento = $dataNascimento;

        return $this;
    }

    /**
     * Get dataNascimento
     *
     * @return \DateTime
     */
    public function getDataNascimento()
    {
        return $this->dataNascimento;
    }

    /**
     * Set nome
     *
     * @param string $nome
     *
     * @return Aluno
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
     * Set telefone
     *
     * @param string $telefone
     *
     * @return Aluno
     */
    public function setTelefone($telefone)
    {
        $this->telefone = $telefone;

        return $this;
    }

    /**
     * Get telefone
     *
     * @return string
     */
    public function getTelefone()
    {
        return $this->telefone;
    }

    
}
