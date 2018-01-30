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
    public function indexAction()
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

        return $this->render('SchoolAlunoBundle:Default:dashboard_aluno.html.twig',
            array('matricula' => $matriculaAluno, 'cursos' => $matriculasAtivas));
    }

    /**
     * @Route("/matricula/{id}/detalle", name="marticula_detalle")
     */
    public function matriculaDetalleAction(MatriculaAluno $matriculaAluno)
    {
        return $this->render('SchoolAlunoBundle:Default:matricula_detalle_aluno.html.twig',
            array('matricula' => $matriculaAluno,));
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
