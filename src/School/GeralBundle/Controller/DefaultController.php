<?php

namespace School\GeralBundle\Controller;

use School\AlunoBundle\Entity\Aluno;
use School\CursoBundle\Entity\Curso;
use School\MatriculaBundle\Entity\MatriculaAluno;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use School\MatriculaBundle\Entity\Matricula;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
//        return $this->render('@SchoolGeral/Default/index.html.twig');
        if ($this->container->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $user = $this->container->get('security.token_storage')->getToken()->getUser();
            if ($user->hasRole('ROLE_SUPER_ADMIN')) {
                return $this->redirectToRoute('dashboard_admin');
            } else {
                return $this->redirectToRoute('dashboard_aluno');
            }
        } else
            return $this->redirectToRoute('fos_user_security_login');
    }

    /**
     * @Route("/admin/dashboard", name="dashboard_admin")
     */
    public function dashboardAdminAction()
    {
        $curso_rep = $this->getDoctrine()->getRepository(Curso::class);

        $curso = $curso_rep->findAll();

        return $this->render('SchoolAlunoBundle:Default:dashboard_admin.html.twig',
            array('curso' => $curso,));
    }

    /**
     * @Route("/admin/user/list", name="admin_user_list")
     */
    public function usersListAction()
    {
        $aluno_rep = $this->getDoctrine()->getRepository(Aluno::class);
        $alunos = $aluno_rep->findAll();

        return $this->render('SchoolAlunoBundle:Default:user_list_admin.html.twig',
            array('alunos' => $alunos));
    }

    /**
     * @Route("/admin/typeahead/aluno", name="admin_typeahead_aluno")
     */
    public function typeaheadAlunoAction()
    {
        $aluno_rep = $this->getDoctrine()->getRepository(Aluno::class);
        $alunos = $aluno_rep->findAll();
        $response = new JsonResponse();
//        echo "<pre>";print_r(array('data' => 123));die();
        $response->setData(array('name' => 123));
        return $response;

    }

    /**
     * @Route("/admin/remove/user/{id}", name="remove_user")
     */
    public function removeUserAction($id)
    {

        $userManager = $this->get('fos_user.user_manager');
        /* @var $userManager UserManager */

        $user = $userManager->findUserBy(['id' => $id]);

        \assert(!\is_null($user));
        $userManager->deleteUser($user);

        // okay, generate $okayResponse
        $aluno_rep = $this->getDoctrine()->getRepository(Aluno::class);
        $alunos = $aluno_rep->findAll();

        return $this->render('SchoolAlunoBundle:Default:user_list_admin.html.twig',
            array('alunos' => $alunos));

    }

    /**
     * @Route("/admin/filter", name="filter")
     * @Method({"GET", "POST"})
     */
    public function filterAction(Request $request)
    {
        if ($request->getMethod() == 'POST') {
            $pago = $request->request->get('filter_pago');
            $curso_id = $request->request->get('filter_curso');
            $aluno_nome = $request->request->get('filter_aluno');

            $criteria = array();

            if ($pago) {
                $criteria['paga'] = true;
            }
//            if ($curso_id) {
//                $curso_rep = $this->getDoctrine()->getRepository(Curso::class);
//                $curso = $curso_rep->findOneBy(array('id'=>$curso_id));
//
//                $matricula_rep = $this->getDoctrine()->getRepository(Matricula::class);
//                $matricula = $matricula_rep->findBy(array('curso'=>$curso));
//                $criteria['matricula'] = $matricula;
//            }

            if ($aluno_nome) {
                $aluno_rep = $this->getDoctrine()->getRepository(Aluno::class);
                $aluno = $aluno_rep->findOneByName($aluno_nome);
                if (!is_null($aluno)) {
                    $criteria['aluno'] = $aluno;
                }
            }

            $matriculaAluno_rep = $this->getDoctrine()->getRepository(MatriculaAluno::class);
            $em = $this->getDoctrine()->getManager();
            $mat = $em->getRepository('SchoolMatriculaBundle:Matricula')->findAll();
            $cursos = $em->getRepository('SchoolCursoBundle:Curso')->findAll();
            $total = count($mat);


            if (count($criteria) > 0) {
                $query = $matriculaAluno_rep->createQueryBuilder('ma');
                foreach ($criteria as $field => $value) {
                    if (!$value) {
                        continue;
                    }
                    $query->andWhere('ma.' . $field . ' =:' . $field)
                        ->setParameter($field, $value);
                }
                $matriculas = $query->getQuery()->getResult();

                return $this->render('SchoolGeralBundle:Default:search_results.html.twig',
                    array('matriculas' => $matriculas, 'total' => $total, 'cursos' => $cursos));
            }
            return $this->render('SchoolGeralBundle:Default:search_results.html.twig',
                array('matriculas' => null, 'total' => $total, 'cursos' => $cursos));
        }
        return $this->redirectToRoute('dashboard_admin');
    }



}
