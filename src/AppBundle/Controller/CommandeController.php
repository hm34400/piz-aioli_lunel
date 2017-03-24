<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Commande;
use AppBundle\Entity\Statut;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Commande controller.
 *
 * @Route("admin/command")
 */
class CommandeController extends Controller {

    /**
     * Lists all commande entities.
     *
     * @Route("/", name="commande_index")
     * @Method("GET")
     */
    public function indexAction() {
        return $this->render('commande/index.html.twig');
    }

    /**
     * Lists all commande entities.
     *
     * @Route("/notDone")
     * @Method("GET")
     */
    public function getCommand() {
        $em = $this->getDoctrine()->getManager();
//        $commandes = $em->getRepository('AppBundle:Commande')->findby(array('statut' => 2 and 1), array('heure' => 'ASC'));
//        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
                        'SELECT c
                        FROM AppBundle:Commande c
                        WHERE c.statut < :statut
                        ORDER BY c.heure ASC'
                )->setParameter('statut', 3);
        return new JsonResponse($query->getResult());
    }

    /**
     * Lists all commande entities.
     *
     * @Route("/status")
     * @Method("GET")
     */
    public function getStatus() {
        $status = $this->getDoctrine()->getRepository(Statut::class)->findAll();
        return new JsonResponse($status);
    }

    /**
     * Creates a new commande entity.
     *
     * @Route("/new", name="commande_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request) {
        $commande = new Commande();
        $form = $this->createForm('AppBundle\Form\CommandeType', $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            if ($this->getUser() != NULL) {
                $commande->setIdClient($this->getUser());
            }
            $em->persist($commande);
            $em->flush($commande);

            return $this->redirectToRoute('commande_show', array('id' => $commande->getId()));
        }

        return $this->render('commande/new.html.twig', array(
                    'commande' => $commande,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a commande entity.
     *
     * @Route("/{id}", name="commande_show")
     * @Method("GET")
     */
    public function showAction(Commande $commande) {
        $deleteForm = $this->createDeleteForm($commande);

        return $this->render('commande/show.html.twig', array(
                    'commande' => $commande,
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing commande entity.
     *
     * @Route("/{id}/edit", name="commande_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Commande $commande) {
        $deleteForm = $this->createDeleteForm($commande);
        $editForm = $this->createForm('AppBundle\Form\CommandeType', $commande);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('commande_edit', array('id' => $commande->getId()));
        }

        return $this->render('commande/edit.html.twig', array(
                    'commande' => $commande,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing commande entity.
     *
     * @Route("/{id}/update")
     * @Method({"PUT"})
     */
    public function updateCommand(Request $request, $id) {
        $command = $this->getDoctrine()->getRepository(Commande::class)->find($id);
        $status = $this->getDoctrine()->getRepository(Statut::class)->find($request->get("id"));
        $em = $this->getDoctrine()->getManager();
        $command->setStatut($status);
        $em->merge($command);
        $em->flush();
        return new JsonResponse($command);
    }

    /**
     * Deletes a commande entity.
     *
     * @Route("/{id}", name="commande_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Commande $commande) {
        $form = $this->createDeleteForm($commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($commande);
            $em->flush($commande);
        }

        return $this->redirectToRoute('commande_index');
    }

    /**
     * Creates a form to delete a commande entity.
     *
     * @param Commande $commande The commande entity
     *
     * @return Form The form
     */
    private function createDeleteForm(Commande $commande) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('commande_delete', array('id' => $commande->getId())))
                        ->setMethod('DELETE')
                        ->getForm()
        ;
    }

}
