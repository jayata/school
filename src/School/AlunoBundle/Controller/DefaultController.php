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

    /**
     * Pagar matricula
     *
     * @Route("/pagar", name="marticula_pagar")
     * @Method("POST")
     */
    public function matriculaPagarAction(Request $request)
    {
        $id = $request->request->get('matricula_id');
        $value = $request->request->get('matricula_payment');
        $change = null;

        $em = $this->getDoctrine()->getManager();

        $matrAluno_rep = $this->getDoctrine()->getRepository(MatriculaAluno::class);
        $matricula = $matrAluno_rep->findOneBy(array('id' => $id));
        if ($matricula->getMatricula()->getCurso()->getValorMatricula() == $value) {
            $matricula->setPaga(true);
            $em->persist($matricula);
            $em->flush();
            $change=0;
        } elseif ($matricula->getMatricula()->getCurso()->getValorMatricula() < $value) {
            $change = $this->change($value);
        }
        return $this->redirectToRoute('marticula_detalle', array('id' => $matricula->getId(), 'change' => $change));
    }

    function change($value)
    {

        $coins = [100, 50, 10, 5, 1, 0.5, 0.1, 0.05, 0.01];
        $change = [0, 0, 0, 0, 0, 0, 0, 0, 0];
        $sum = 0;
        if ($value === 0)
            return 0;
///    cédulas de R$100,00, R$50,00, R$10,00, R$5,00;
//moedas de R$0,50, R$1,00, R$0,10, R$0,05 e R$0,01.
        for ($i = 0; $i < count($coins); $i++) {

            while (bccomp($value, ($sum + $coins[$i]), 2) >= 0) {
                $sum += $coins[$i];
                $change[$i] = $change[$i] + 1;

            }

            if (bccomp($value, ($sum + $coins[$i]), 2) === 0) {
                return $change;
            }
        }
        return $change;
    }
}
