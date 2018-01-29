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
use Symfony\Component\HttpFoundation\JsonResponse;

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
        if ($id == -1) {
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
     * @Route("/admin/matricular/check", name="matricular_check")
     * @Method("POST")
     *
     */
    public function matricularCheckAction(Request $request)
    {
        if ($request->getMethod() == 'POST') {
            $matricula_aluno_id = $request->request->get('matricula_aluno');
            $matricula_curso_id = $request->request->get('matricula_curso');
            $anoleitivo = $request->request->get('anoleitivo');
            $periodo = $request->request->get('periodo');

            if (strlen($anoleitivo) != 4) {
                return new JsonResponse(array('validator' => false));
            }

            $cur_rep = $this->getDoctrine()->getRepository(Curso::class);
            $curso = $cur_rep->findOneBy(array("id" => $matricula_curso_id));

            $aluno_rep = $this->getDoctrine()->getRepository(Aluno::class);
            $aluno = $aluno_rep->findOneBy(array('id' => $matricula_aluno_id));

            $matrAluno_rep = $this->getDoctrine()->getRepository(MatriculaAluno::class);
            $matriculasAluno = $matrAluno_rep->findBy(array("aluno" => $aluno));


            if (count($matriculasAluno) > 0) {
                foreach ($matriculasAluno as $ma) {
                    if ($ma->getMatricula()->getCurso() == $curso) {
                        if ($ma->getMatricula()->getAno()->format('Y') == $anoleitivo) {
                            if ($ma->getMatricula()->getCurso()->getPeriodo() == $periodo) {
                                return new JsonResponse(array('validator' => true, 'data' => false));
                            }
                        }
                    }
                }
            }
            $matr_rep = $this->getDoctrine()->getRepository(Matricula::class);
            $matriculas = $matr_rep->findBy(array("curso" => $curso, "ativa" => true));
            if (count($matriculas) > 0) {
                foreach ($matriculas as $m) {
                    if ($m->getAno()->format('Y') == $anoleitivo && $m->getCurso()->getPeriodo() == $periodo) {
                        return new JsonResponse(array('validator' => true, 'data' => true, 'available' => true));
                    }
                }
            }
        }
        return new JsonResponse(array('validator' => true, 'data' => true, 'available' => false));
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
        $matricula_curso_id = $request->request->get('matricula_curso');

        $aluno_rep = $this->getDoctrine()->getRepository(Aluno::class);
        $aluno = $aluno_rep->findOneBy(array('id' => $aluno_id));

        $curso_rep = $this->getDoctrine()->getRepository(Curso::class);
        $curso = $curso_rep->findOneBy(array('id' => $matricula_curso_id));

        $matr_rep = $this->getDoctrine()->getRepository(Matricula::class);
        $matricula = $matr_rep->findOneBy(array("curso" => $curso));

        $matriculaAluno->setAluno($aluno);
        $matriculaAluno->setMatricula($matricula);
        $em = $this->getDoctrine()->getManager();
        $em->persist($matriculaAluno);
        $em->flush();

        return $this->redirectToRoute("dashboard_admin");
    }
}
