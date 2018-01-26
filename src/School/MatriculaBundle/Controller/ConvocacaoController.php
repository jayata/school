<?php

namespace School\MatriculaBundle\Controller;

use School\MatriculaBundle\Entity\Convocacao;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Convocacao controller.
 *
 * @Route("/convocacao")
 */
class ConvocacaoController extends Controller
{
    /**
     * Lists all convocacao entities.
     *
     * @Route("/", name="convocacao_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $convocacaos = $em->getRepository('SchoolMatriculaBundle:Convocacao')->findAll();

        return $this->render('convocacao/index.html.twig', array(
            'convocacaos' => $convocacaos,
        ));
    }

    /**
     * Creates a new convocacao entity.
     *
     * @Route("/new", name="convocacao_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $convocacao = new Convocacao();
        $form = $this->createForm('School\MatriculaBundle\Form\ConvocacaoType', $convocacao);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($convocacao);
            $em->flush();

            return $this->redirectToRoute('convocacao_show', array('id' => $convocacao->getId()));
        }

        return $this->render('convocacao/new.html.twig', array(
            'convocacao' => $convocacao,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a convocacao entity.
     *
     * @Route("/{id}", name="convocacao_show")
     * @Method("GET")
     */
    public function showAction(Convocacao $convocacao)
    {
        $deleteForm = $this->createDeleteForm($convocacao);

        return $this->render('convocacao/show.html.twig', array(
            'convocacao' => $convocacao,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing convocacao entity.
     *
     * @Route("/{id}/edit", name="convocacao_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Convocacao $convocacao)
    {
        $deleteForm = $this->createDeleteForm($convocacao);
        $editForm = $this->createForm('School\MatriculaBundle\Form\ConvocacaoType', $convocacao);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('convocacao_edit', array('id' => $convocacao->getId()));
        }

        return $this->render('convocacao/edit.html.twig', array(
            'convocacao' => $convocacao,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a convocacao entity.
     *
     * @Route("/{id}", name="convocacao_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Convocacao $convocacao)
    {
        $form = $this->createDeleteForm($convocacao);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($convocacao);
            $em->flush();
        }

        return $this->redirectToRoute('convocacao_index');
    }

    /**
     * Creates a form to delete a convocacao entity.
     *
     * @param Convocacao $convocacao The convocacao entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Convocacao $convocacao)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('convocacao_delete', array('id' => $convocacao->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
