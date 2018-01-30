<?php

namespace School\AlunoBundle\Entity;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Aluno
 *
 * @ORM\Table(name="aluno")
 * @ORM\Entity(repositoryClass="School\AlunoBundle\Repository\AlunoRepository")
 * @UniqueEntity("cpf")
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
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=250, nullable=True)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=250, unique = true, nullable=True)
     *
     * @Assert\NotBlank()
     * @Assert\Regex("/^(\d{3}\.\d{3}\.\d{3}\-\d{2})$/",message="Inserte un CPF valido")
     *
     */
    private $cpf;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=250,nullable=True)
     */
    private $rg;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="datetime")
     * @Assert\Type(type="\DateTime")
     *
     */
    private $dataNascimento;

    /**
     * @Assert\NotBlank()
     * @ORM\OneToMany(targetEntity="School\MatriculaBundle\Entity\MatriculaAluno", mappedBy="aluno",cascade={"remove"})
     */
    private $cursosMatriculados;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=250,nullable=True)
     */
    private $telefone;

    public function __construct()
    {
        parent::__construct();
        $this->dataNascimento = new \DateTime();
        $this->cursosMatriculados = new \Doctrine\Common\Collections\ArrayCollection();
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


    /**
     * Add cursosMatriculado
     *
     * @param \School\MatriculaBundle\Entity\MatriculaAluno $cursosMatriculado
     *
     * @return Aluno
     */
    public function addCursosMatriculado(\School\MatriculaBundle\Entity\MatriculaAluno $cursosMatriculado)
    {
        $this->cursosMatriculados[] = $cursosMatriculado;

        return $this;
    }

    /**
     * Remove cursosMatriculado
     *
     * @param \School\MatriculaBundle\Entity\MatriculaAluno $cursosMatriculado
     */
    public function removeCursosMatriculado(\School\MatriculaBundle\Entity\MatriculaAluno $cursosMatriculado)
    {
        $this->cursosMatriculados->removeElement($cursosMatriculado);
    }

    /**
     * Get cursosMatriculados
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCursosMatriculados()
    {
        return $this->cursosMatriculados;
    }


}
