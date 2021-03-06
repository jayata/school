<?php

namespace School\AlunoBundle\Controller;

use School\AlunoBundle\Entity\Aluno;
use School\CursoBundle\Entity\Curso;
use School\MatriculaBundle\Entity\Matricula;
use School\MatriculaBundle\Entity\MatriculaAluno;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/dashboard", name="dashboard_aluno")
     */
    public function indexAction(Request $request)
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        $matrAluno_rep = $this->getDoctrine()->getRepository(MatriculaAluno::class);
        $matriculaAluno = $matrAluno_rep->findBy(array('aluno' => $user));

        $matrAtiva_rep = $this->getDoctrine()->getRepository(Matricula::class);
        $matriculasAtivas = $matrAtiva_rep->findBy(array('ativa' => true));

        $matriculas = array();
        foreach ($matriculaAluno as $matriculado) {
            $matriculas[] = $matriculado->getMatricula();
        }

        foreach ($matriculasAtivas as $key => $open) {
            if (in_array($open, $matriculas)) {
                unset($matriculasAtivas[$key]);
            }
        }
        $paginator = $this->get('knp_paginator');
        $matriculasA = $paginator->paginate(
            $matriculasAtivas,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('SchoolAlunoBundle:Default:dashboard_aluno.html.twig',
            array('matricula' => $matriculaAluno, 'cursos' => $matriculasA));
    }

    /**
     * @Route("/matricula/{id}/detalle", name="marticula_detalle")
     */
    public function matriculaDetalleAction(MatriculaAluno $matriculaAluno)
    {
        $mesesPagos = $matriculaAluno->getMesesPagos();
        $totalMeses = $matriculaAluno->getMatricula()->getCurso()->getMesesDuracao();
        $valorMensual = $matriculaAluno->getMatricula()->getCurso()->getMensualidade();

        $multa = (1*$valorMensual)/100;

        $mesesPorCumplir = $totalMeses - $mesesPagos;
        $multa=$multa*$mesesPorCumplir;

        return $this->render('SchoolAlunoBundle:Default:matricula_detalle_aluno.html.twig',
            array('matricula' => $matriculaAluno,'multa'=>$multa));
    }

    /**
     * Finds and displays a curso entity.
     *
     * @Route("/curso/{id}", name="aluno_curso_show")
     * @Method("GET")
     */
    public function showAction(Curso $curso)
    {
        return $this->render('curso/show.html.twig', array(
            'curso' => $curso,
        ));
    }

    /**
     * @Route("/matricula/{id}/cancelar", name="marticula_cancelar")
     */
    public function matriculaCancellAction(MatriculaAluno $matriculaAluno)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($matriculaAluno);
        $em->flush();

        return $this->redirectToRoute('dashboard_aluno');
    }


}
