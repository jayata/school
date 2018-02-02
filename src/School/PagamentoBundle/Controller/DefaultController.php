<?php

namespace School\PagamentoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use School\AlunoBundle\Entity\Aluno;
use School\CursoBundle\Entity\Curso;
use School\MatriculaBundle\Entity\Matricula;
use School\MatriculaBundle\Entity\MatriculaAluno;

class DefaultController extends Controller
{
    /**
     * Pagar matricula
     *
     * @Route("/pagar/matricula", name="marticula_pagar")
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
        $valorMatricula = $matricula->getMatricula()->getCurso()->getValorMatricula();

        if ($valorMatricula == $value) {
            $change=0;
        } elseif ($valorMatricula < $value) {
            $change = $this->change($value - $valorMatricula);
        }else{
            $this->addFlash("error-taxa", "O valor a ser pago deve ser maior ou igual à taxa inscrição");
            return $this->redirectToRoute('marticula_detalle',array('id'=>$matricula->getId()));
        }
        $matricula->setPaga(true);
        $em->persist($matricula);
        $em->flush();
        $troco = $value - $valorMatricula;
        $troco = number_format($troco,2);
        return $this->render('SchoolAlunoBundle:Default:operaciones_aluno.html.twig',
            array('pago' => $value,'debe'=>$valorMatricula,'change' => $change,'troco'=> $troco));
    }

    /**
     * Pagar mensualidad
     *
     * @Route("/pagar/mensualidad", name="marticula_pagar_mensualidad")
     * @Method("POST")
     */
    public function mensualidadPagarAction(Request $request)
    {
        $id = $request->request->get('matricula_id');
        $value = $request->request->get('mensualidad_payment');
        $change = null;

        $em = $this->getDoctrine()->getManager();

        $matrAluno_rep = $this->getDoctrine()->getRepository(MatriculaAluno::class);
        $matricula = $matrAluno_rep->findOneBy(array('id' => $id));
        $mensualidad=$matricula->getMatricula()->getCurso()->getMensualidade();

        if ($mensualidad == $value) {
            $change=0;
        } elseif ($mensualidad < $value) {
            $change = $this->change($value - $mensualidad);
        }else{
            $this->addFlash("error-mensual", "O valor a pagar deve ser maior ou igual ao valor da mensalidade");
            return $this->redirectToRoute('marticula_detalle',array('id'=>$matricula->getId()));
        }
        $matricula->setMesesPagos($matricula->getMesesPagos()+1);
        $em->persist($matricula);
        $em->flush();
        $troco = $value - $mensualidad;
        $troco = number_format($troco,2);
        return $this->render('SchoolAlunoBundle:Default:operaciones_aluno.html.twig',
            array('pago' => $value,'debe'=>$mensualidad,'change' => $change,'troco'=> $troco));
    }

    function change($value)
    {
        //cédulas de R$100,00, R$50,00, R$10,00, R$5,00;
        //moedas de R$0,50, R$1,00, R$0,10, R$0,05 e R$0,01.
        $coins = [100, 50, 10, 5, 1, 0.5, 0.1, 0.05, 0.01];
        $change = [0, 0, 0, 0, 0, 0, 0, 0, 0];
        $sum = 0;
        if ($value === 0)
            return 0;
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
