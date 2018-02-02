<?php

namespace School\MatriculaBundle\Controller;

use School\MatriculaBundle\Entity\Matricula;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use School\MatriculaBundle\Entity\MatriculaAluno;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Matricula controller.
 *
 * @Route("/admin/matricula")
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
        $cursos = $em->getRepository('SchoolCursoBundle:Curso')->findAll();

        $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
        $qb
            ->select('matriculas')
            ->from('School\MatriculaBundle\Entity\Matricula', 'matriculas');

        if (!$all) {
            $qb->where('matriculas.ativa =:a')
                ->setParameter('a','1');
        }
        $query = $qb->getQuery();
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('matricula/index.html.twig', array(
            'matriculas' => $pagination, 'all' => $all,'cursos' =>$cursos
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
        $form = $this->createForm('School\MatriculaBundle\Form\MatriculaType', $matricula);
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

        $matrAluno_rep = $this->getDoctrine()->getRepository(MatriculaAluno::class);
        $matriculaAluno = $matrAluno_rep->findBy(array("matricula" => $matricula));

        return $this->render('matricula/show.html.twig', array(
            'matricula' => $matricula,
            'matriculaAluno' => $matriculaAluno,
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
        if ($matricula->getAtiva()) {
            $matricula->setAtiva(false);
        } else {
            $matricula->setAtiva(true);
        }
        $em->persist($matricula);
        $em->flush();
        return $this->redirectToRoute('matricula_index');
    }


}
