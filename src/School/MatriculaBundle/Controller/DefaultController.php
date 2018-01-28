<?php

namespace School\MatriculaBundle\Controller;

use School\CursoBundle\Entity\Curso;
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
     * @Route("/admin/matricular/form/{id}", name="matricular_form")
     *
     */
    public function matricularFormAction($id)
    {
        $aluno_rep = $this->getDoctrine()->getRepository(Aluno::class);
        $alunos = $aluno_rep->findAll();
        $matr_rep = $this->getDoctrine()->getRepository(Matricula::class);
        if($id == -1){
            $matriculas = $matr_rep->findBy(array("ativa" => true));

            return $this->render('SchoolMatriculaBundle:Default:matricular_form.html.twig', array(
                'alunos' => $alunos, 'matriculas' => $matriculas
            ));
        }

        $matricula = $matr_rep->findOneBy(array("id" => $id));
        return $this->render('SchoolMatriculaBundle:Default:matricular_neste_form.html.twig', array(
            'alunos' => $alunos, 'matricula' => $matricula
        ));
    }

    /**
     * @Route("/matricular/check", name="matricular_check")
     * @Method("POST")
     *
     */
    public function matricularCheckAction(Request $request)
    {
        if ($request->getMethod() == 'POST') {
            $curso_id = $request->request->get('curso');

        }
    }

    /**
     * @Route("/aluno/matricular/{matricula}", name="matricular_aluno_action")
     * @Method({"GET", "POST"})
     *
     */
    public function matricularAlunoAction(Matricula $matricula)
    {
        $matriculaAluno = new MatriculaAluno();

        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $matriculaAluno->setAluno($user);
        $matriculaAluno->setMatricula($matricula);

        $em = $this->getDoctrine()->getManager();
        $em->persist($matriculaAluno);
        $em->flush();

        return $this->redirectToRoute("dashboard_aluno");
    }

    /**
     * @Route("/admin/matricular", name="matricular_admin_action")
     * @Method({"GET", "POST"})
     *
     */
    public function matricularAdminAction(Request $request)
    {
        $matriculaAluno = new MatriculaAluno();

        $aluno_id = $request->request->get('aluno');
        $matricula_id = $request->request->get('matricula');

        $aluno_rep = $this->getDoctrine()->getRepository(Aluno::class);
        $aluno = $aluno_rep->findOneBy(array('id'=>$aluno_id));

        $matr_rep = $this->getDoctrine()->getRepository(Matricula::class);
        $matricula = $matr_rep->findOneBy(array("id" => $matricula_id));

        $matriculaAluno->setAluno($aluno);
        $matriculaAluno->setMatricula($matricula);
        $em = $this->getDoctrine()->getManager();
        $em->persist($matriculaAluno);
        $em->flush();

        return $this->redirectToRoute("dashboard_admin");
    }
}
