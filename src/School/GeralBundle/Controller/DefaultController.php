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
        $cursos = $curso_rep->findAll();
        $percentage=null;
        if (count($cursos) > 0) {
            $counter = 0;
            foreach ($cursos as $item) {
                if (count($item->getMatriculasAbiertas()) > 0) {
                    $counter++;
                }
            }
            $percentage = ($counter * 100) / count($cursos);
        }

        $matricula_rep = $this->getDoctrine()->getRepository(Matricula::class);
        $matriculas = $matricula_rep->findAll();

        $aluno_rep = $this->getDoctrine()->getRepository(Aluno::class);
        $alunos = $aluno_rep->findAll();

        $matriculaAluno_rep = $this->getDoctrine()->getRepository(MatriculaAluno::class);
        $matriculaAlunos = $matriculaAluno_rep->findAll();
        $percentagePaga=null;

        if (count($matriculaAlunos) > 0) {
            $counterMatriculasAlumno = 0;
            foreach ($matriculaAlunos as $item) {
                if ($item->getPaga()) {
                    $counterMatriculasAlumno++;
                }
            }
            $percentagePaga = ($counterMatriculasAlumno * 100) / count($matriculaAlunos);
        }
//        echo ($percentagePaga);die();
        return $this->render('SchoolAlunoBundle:Default:dashboard_admin.html.twig',
            array('cursos' => $cursos, 'matriculas' => $matriculas, 'matriculasAlunos' => $matriculaAlunos, 'alunos' => $alunos,
                'percentage' => $percentage, 'percentagePaga' => $percentagePaga));
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
                $criteria['paga'] = 0;
            }
            if ($curso_id) {
                $criteria['curso'] = $curso_id;
            }

            if ($aluno_nome) {
                $aluno_rep = $this->getDoctrine()->getRepository(Aluno::class);
                $aluno = $aluno_rep->findOneByName($aluno_nome);
                if (!is_null($aluno)) {
                    $criteria['aluno'] = $aluno;
                } else {
                    $criteria['aluno'] = null;
                }
            }
            $em = $this->getDoctrine()->getManager();
            $mat = $em->getRepository('SchoolMatriculaBundle:Matricula')->findAll();
            $cursos = $em->getRepository('SchoolCursoBundle:Curso')->findAll();
            $total = count($mat);

            if (count($criteria) > 0) {
                $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
                $qb
                    ->select('ma')
                    ->from('School\MatriculaBundle\Entity\MatriculaAluno', 'ma');

                foreach ($criteria as $field => $value) {
                    if ($field == 'curso') {
                        $qb->innerJoin('ma.matricula', 'm')
                            ->andWhere('m.curso = :curso');
                    }
                    if ($field == 'paga') {
                        $qb->andWhere('ma.paga = :paga');
                    }
                    if ($field == 'aluno') {
                        $qb->andWhere('ma.aluno = :aluno');
                    }
                }
                $qb->setParameters($criteria);
            }
//                var_dump($criteria);
//                echo $qb->getDQL();die();
            $matriculas = $qb->getQuery()->getResult();
            return $this->render('SchoolGeralBundle:Default:search_results.html.twig',
                array('matriculas' => $matriculas, 'total' => $total, 'cursos' => $cursos));

        }
        return $this->redirectToRoute('dashboard_admin');
    }

    /**
     * Admin user registrationn.
     *
     * @Route("/admin/register/form", name="admin_user_registration_form")
     * @Method("GET")
     */
    public function adminRegisterFormAction()
    {
        return $this->render('SchoolAlunoBundle:Default:register_user_admin.html.twig');
    }

    /**
     * Admin user registrationn.
     *
     * @Route("/admin/register", name="admin_user_registration")
     * @Method({"GET", "POST"})
     */
    public function adminRegisterAction(Request $request)
    {
        if ($request->getMethod() == 'POST') {
            $pattern = '/^(\d{3}\.\d{3}\.\d{3}\-\d{2})$/';
            $success = preg_match($pattern, $request->request->get('cpf'), $match);
            if ($success) {
                $em = $this->getDoctrine()->getManager();
                $aluno = (new Aluno())
                    ->setName($request->request->get('name'))
                    ->setEnabled(true)
                    ->setPlainPassword($request->request->get('plainPassword'))
                    ->setUsername($request->request->get('username'))
                    ->setEmail($request->request->get('email'))
                    ->setCpf($request->request->get('cpf'))
                    ->setRg($request->request->get('rg'))
                    ->setTelefone($request->request->get('telefone'))
                    ->setDataNascimento(new \DateTime($request->request->get('dataNascimento')));
                $em->persist($aluno);
                $em->flush();
                return $this->redirectToRoute('admin_user_list');
            } else {
                $this->addFlash("error-input-cpf", "Entre un CPF valido");
                return $this->redirectToRoute('admin_user_registration_form');
            }
        }
        return $this->redirectToRoute('admin_user_registration_form');
    }
}
