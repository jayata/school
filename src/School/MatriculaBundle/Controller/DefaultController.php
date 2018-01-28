<?php

namespace School\MatriculaBundle\Controller;

use School\MatriculaBundle\Entity\MatriculaAluno;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use School\MatriculaBundle\Entity\Matricula;
use School\AlunoBundle\Entity\Aluno;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/admin/matricular/form", name="matricular_form")
     *
     */
    public function matricularFormAction()
    {
        $aluno_rep = $this->getDoctrine()->getRepository(Aluno::class);
        $alunos = $aluno_rep->findAll();
        $matr_rep = $this->getDoctrine()->getRepository(Matricula::class);
        $matriculas = $matr_rep->findBy(array("ativa" => true));

        return $this->render('SchoolMatriculaBundle:Default:matricular_form.html.twig', array(
            'alunos' => $alunos, 'matriculas' => $matriculas
        ));
    }

    /**
     * @Route("/matricular/", name="matricular_action")
     * @Method({"GET", "POST"})
     *
     */
    public function matricularAction(Request $request)
    {
        if ($request->getMethod() == 'POST') {
            $matriculaAluno = new MatriculaAluno();
            $aluno = null;
            $curso = null;
            $route = "dashboard_aluno";
            if ($this->container->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
                $user = $this->container->get('security.token_storage')->getToken()->getUser();
                if ($user->hasRole('ROLE_SUPER_ADMIN')) {
                    $route = "dashboard_admin";
                    $aluno_id = $request->request->get('aluno');
                    $curso_id = $request->request->get('curso');

                    $aluno_rep = $this->getDoctrine()->getRepository(Aluno::class);
                    $aluno = $aluno_rep->find($aluno_id);

                    $matr_rep = $this->getDoctrine()->getRepository(Matricula::class);
                    $matricula = $matr_rep->find($curso_id);

                    $matriculaAluno->setAluno($aluno);
                    $matriculaAluno->setMatricula($matricula);
                } else {
                    print_r("sdfdf");
                }
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($matriculaAluno);
            $em->flush();

            return $this->redirectToRoute($route);
        }
    }
}
