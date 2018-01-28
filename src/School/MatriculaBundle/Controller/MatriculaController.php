<?php

namespace School\MatriculaBundle\Controller;

use School\MatriculaBundle\Entity\Matricula;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Matricula controller.
 *
 * @Route("/matricula")
 */
class MatriculaController extends Controller
{
    /**
     * Lists all matricula entities.
     *
     * @Route("/", name="matricula_index")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $all = $request->query->get('all');
        $em = $this->getDoctrine()->getManager();
        $matriculas = $em->getRepository('SchoolMatriculaBundle:Matricula')->findAll();
        $total = count($matriculas);
        if ($all != 1) {
            $matriculas = $em->getRepository('SchoolMatriculaBundle:Matricula')->findBy(array('ativa'=>1));
        }
        return $this->render('matricula/index.html.twig', array(
            'matriculas' => $matriculas, 'all'=>$all, 'total'=>$total
        ));
    }

    /**
     * Creates a new matricula entity.
     *
     * @Route("/new", name="matricula_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $matricula = new Matricula();
        $form = $this->createForm('School\MatriculaBundle\Form\MatriculaType', $matricula/* array('admin' => $admin,)*/);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($matricula);
            $em->flush();

            return $this->redirectToRoute('matricula_show', array('id' => $matricula->getId()));
        }

        return $this->render('matricula/new.html.twig', array(
            'matricula' => $matricula,
            'form' => $form->createView()
        ));
    }

    /**
     * Finds and displays a matricula entity.
     *
     * @Route("/{id}", name="matricula_show")
     * @Method("GET")
     */
    public function showAction(Matricula $matricula)
    {
        $deleteForm = $this->createDeleteForm($matricula);

        return $this->render('matricula/show.html.twig', array(
            'matricula' => $matricula,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing matricula entity.
     *
     * @Route("/{id}/edit", name="matricula_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Matricula $matricula)
    {
        $deleteForm = $this->createDeleteForm($matricula);
        $editForm = $this->createForm('School\MatriculaBundle\Form\MatriculaType', $matricula);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('matricula_show', array('id' => $matricula->getId()));
        }

        return $this->render('matricula/edit.html.twig', array(
            'matricula' => $matricula,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a matricula entity.
     *
     * @Route("/{id}", name="matricula_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Matricula $matricula)
    {
        $form = $this->createDeleteForm($matricula);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($matricula);
            $em->flush();
        }

        return $this->redirectToRoute('matricula_index');
    }

    /**
     * Creates a form to delete a matricula entity.
     *
     * @param Matricula $matricula The matricula entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Matricula $matricula)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('matricula_delete', array('id' => $matricula->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Activar o desactivar matricula
     *
     * @Route("/active/{id}", name="matricula_activate")
     * @Method("GET")
     */
    public function matriculaActivateAction(Matricula $matricula)
    {
        $em = $this->getDoctrine()->getManager();
        if ($matricula->getAtiva()){
            $matricula->setAtiva(false);
        }else{
            $matricula->setAtiva(true);
        }
        $em->persist($matricula);
        $em->flush();
        return $this->redirectToRoute('matricula_index');
    }
}
