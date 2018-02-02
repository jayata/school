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
        $percentage = null;
        $countCursos = count($cursos);
        if ($countCursos > 0) {
            $counter = 0;
            foreach ($cursos as $item) {
                if (count($item->getMatriculasAbiertas()) > 0) {
                    $counter++;
                }
            }
            $percentage = ($counter * 100) / $countCursos;
        }

        $qb = $this->getDoctrine()->getManager()->createQueryBuilder();

        $qb->select('count(alunos.id)');
        $qb->from('School\AlunoBundle\Entity\Aluno','alunos');
        $alunos = $qb->getQuery()->getSingleScalarResult();

        $matriculaAluno_rep = $this->getDoctrine()->getRepository(MatriculaAluno::class);
        $matriculaAlunos = $matriculaAluno_rep->findAll();

        $percentagePaga = null;
        $count=count($matriculaAlunos);
        if ($count > 0) {
            $counterMatriculasAlumno = 0;
            foreach ($matriculaAlunos as $item) {
                if ($item->getPaga()) {
                    $counterMatriculasAlumno++;
                }
            }
            $percentagePaga = ($counterMatriculasAlumno * 100) / $count;
        }
        return $this->render('SchoolAlunoBundle:Default:dashboard_admin.html.twig',
            array('cursos' => $cursos,   'matriculasAlunos' => $count, 'alunos' => $alunos,
                'percentage' => $percentage, 'percentagePaga' => $percentagePaga));
    }

    /**
     * @Route("/admin/user/list", name="admin_user_list")
     */
    public function usersListAction(Request $request)
    {
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
        $qb
            ->select('alunos')
            ->from('School\AlunoBundle\Entity\Aluno', 'alunos');
        $query = $qb->getQuery();
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );


        return $this->render('SchoolAlunoBundle:Default:user_list_admin.html.twig',
            array('alunos' => $pagination));
    }

    /**
     * @Route("/admin/remove/user/{id}", name="remove_user")
     */
    public function removeUserAction(Request $request,$id)
    {

        $userManager = $this->get('fos_user.user_manager');
        /* @var $userManager UserManager */

        $user = $userManager->findUserBy(['id' => $id]);

        \assert(!\is_null($user));
        $userManager->deleteUser($user);

        // okay, generate $okayResponse
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
        $qb
            ->select('a')
            ->from('School\AlunoBundle\Entity\Aluno', 'a');

        $query = $qb->getQuery();
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('SchoolAlunoBundle:Default:user_list_admin.html.twig',
            array('alunos' => $pagination));

    }

    /**
     * @Route("/admin/filter", name="filter")
     * @Method({"GET", "POST"})
     */
    public function filterAction(Request $request)
    {
        $pago = $request->query->get('filter_pago');
        $curso_id = $request->query->get('filter_curso');
        $aluno_nome = $request->query->get('filter_aluno');

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

            $query = $qb->getQuery();
            $paginator = $this->get('knp_paginator');
            $pagination = $paginator->paginate(
                $query,
                $request->query->getInt('page', 1),
                10
            );
            return $this->render('SchoolGeralBundle:Default:search_results.html.twig',
                array('matriculas' => $pagination,));
        }
        return $this->render('SchoolGeralBundle:Default:search_results.html.twig',
            array('matriculas' => null,));
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
            $ok = true;
            $blankMessage = 'This value should not be blank';
            $pattern = '/^(\d{3}\.\d{3}\.\d{3}\-\d{2})$/';
            $success = preg_match($pattern, $request->request->get('cpf'), $match);

            $nome = $request->request->get('name');
            $password = $request->request->get('plainPassword');
            $username = $request->request->get('username');
            $rg = $request->request->get('rg');
            $telefone = $request->request->get('telefone');

            if (!$success) {
                $this->addFlash("error-input-cpf", "Entre un CPF valido");
                $ok = false;
            }
            if (trim($nome) === "") {
                $this->addFlash("error-input-name", $blankMessage);
                $ok = false;
            }
            if (trim($username) == "") {
                $this->addFlash("error-input-username", "Please enter a username");
                $ok = false;
            }
            if (trim($rg) === "") {
                $this->addFlash("error-input-rg", $blankMessage);
                $ok = false;
            }
            if (trim($rg) === "") {
                $this->addFlash("error-input-telefone", $blankMessage);
                $ok = false;
            }
            if (strlen($password) < 4) {
                $this->addFlash("error-input-plainPassword", "The password is too short");
                $ok = false;
            }

            if ($ok) {
                $em = $this->getDoctrine()->getManager();
                $aluno = (new Aluno())
                    ->setName($nome)
                    ->setEnabled(true)
                    ->setPlainPassword($password)
                    ->setUsername($username)
                    ->setEmail($request->request->get('email'))
                    ->setCpf($request->request->get('cpf'))
                    ->setRg($rg)
                    ->setTelefone($telefone)
                    ->setDataNascimento(new \DateTime($request->request->get('dataNascimento')));
                $em->persist($aluno);
                $em->flush();
                return $this->redirectToRoute('admin_user_list');
            } else {
                return $this->redirectToRoute('admin_user_registration_form');
            }
        }
        return $this->redirectToRoute('admin_user_registration_form');
    }
}
