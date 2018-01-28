<?php

namespace School\AlunoBundle\Controller;

use School\MatriculaBundle\Entity\Matricula;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/dashboard", name="dashboard_aluno")
     */
    public function indexAction()
    {
        $matr_rep = $this->getDoctrine()->getRepository(Matricula::class);
        $matricula = $matr_rep->findAll();

        return $this->render('SchoolAlunoBundle:Default:dashboard_aluno.html.twig',
            array('matricula'=>$matricula,));
    }


}
