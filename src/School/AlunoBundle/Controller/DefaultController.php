<?php

namespace School\AlunoBundle\Controller;

use School\AlunoBundle\Entity\Aluno;
use School\CursoBundle\Entity\Curso;
use School\MatriculaBundle\Entity\Matricula;
use School\MatriculaBundle\Entity\MatriculaAluno;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/dashboard", name="dashboard_aluno")
     */
    public function indexAction()
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        $matr_rep = $this->getDoctrine()->getRepository(MatriculaAluno::class);
        $matricula = $matr_rep->findBy(array('aluno'=>$user));

        $curso_rep = $this->getDoctrine()->getRepository(Matricula::class);
        $cursos = $curso_rep ->findBy(array('ativa'=>true));


        return $this->render('SchoolAlunoBundle:Default:dashboard_aluno.html.twig',
            array('matricula'=>$matricula,'cursos'=>$cursos));
    }

    /**
     * @Route("/matricula/{id}/detalle", name="marticula_detalle")
     */
    public function matriculaDetalleAction(MatriculaAluno $matriculaAluno)
    {


        return $this->render('SchoolAlunoBundle:Default:matricula_detalle_aluno.html.twig',
            array('matricula'=>$matriculaAluno,));
    }


}
