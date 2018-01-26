<?php

namespace School\GeralBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
//        return $this->render('@SchoolGeral/Default/index.html.twig');
        return $this->redirectToRoute('fos_user_security_login');
    }
}
