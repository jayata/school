<?php

namespace School\GeralBundle\Controller;

use School\AlunoBundle\Entity\Aluno;
use School\CursoBundle\Entity\Curso;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use School\MatriculaBundle\Entity\Matricula;

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
        }else
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
            array('curso'=>$curso,));
    }

    /**
     * @Route("/admin/user/list", name="admin_user_list")
     */
    public function usersListAction()
    {
        $aluno_rep = $this->getDoctrine()->getRepository(Aluno::class);
        $alunos = $aluno_rep->findAll();

        return $this->render('SchoolAlunoBundle:Default:user_list_admin.html.twig',
            array('alunos'=>$alunos));
    }

    /**
     * @Route("/admin/remove/user/{id}", name="remove_user")
     */
    public function removeUserAction($id)
    {

        $userManager = $this->get('fos_user.user_manager');
        /* @var $userManager UserManager */

        $user = $userManager->findUserBy(['id'=>$id]);

        \assert(!\is_null($user));
        $userManager->deleteUser($user);

        // okay, generate $okayResponse
        $aluno_rep = $this->getDoctrine()->getRepository(Aluno::class);
        $alunos = $aluno_rep->findAll();

        return $this->render('SchoolAlunoBundle:Default:user_list_admin.html.twig',
            array('alunos'=>$alunos));

    }
}
