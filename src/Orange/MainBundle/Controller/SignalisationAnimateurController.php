<?php

namespace Orange\MainBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Orange\MainBundle\Entity\SignalisationAnimateur;
use Orange\QuickMakingBundle\Controller\BaseController;
use Orange\MainBundle\Form\SignalisationAnimateurType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Orange\MainBundle\Utils\SignalisationUtils;
use Orange\MainBundle\Utils\Notification;
use Orange\MainBundle\Entity\Statut;
use Orange\QuickMakingBundle\Annotation\QMLogger;
/**
 * SignalisationAnimateur controller.
 *
 * @Route("/signalisationanimateur")
 */
class SignalisationAnimateurController extends BaseController
{

    /**
     * Lists all SignalisationAnimateur entities.
     *
     * @Route("/", name="signalisationanimateur")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('OrangeMainBundle:SignalisationAnimateur')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    
    /**
     * Creates a new Bu entity.
     *
     * @Route("/creer_signalisation_animateur/{signalisation_id}", name="creer_signalisation_animateur")
     * @Method("POST")
     * @Template("OrangeMainBundle:SignalisationAnimateur:new.html.twig")
     */
    public function createAction(Request $request, $signalisation_id)
    {
    	$em = $this->getDoctrine()->getManager();
    	$entity = new SignalisationAnimateur();
    	$form = $this->createCreateForm($entity,'SignalisationAnimateur');
    	$form->handleRequest($request);
    	$helper = $this->get('orange.main.mailer');
    	$now = new \DateTime();
    	$now = $now->format('d-m-Y') . " à " . $now->format('H:i:s');
    	if ($form->isValid()) 
    	{
    		$signalisation = $em->getRepository('OrangeMainBundle:Signalisation')->find($signalisation_id);
    		SignalisationUtils::updateOtherAnimateurState($em, $signalisation);
    		$em->persist($entity);
    		$entity->setSignalisation($signalisation);
    		$entity->setActif(true);
    		$em->flush();
    		$membreEmail = SignalisationUtils::getSignalisationMembresEmail($em, $entity);
    		$subject 	 = 'Prise en charge de la signalisation';
    		$commentaire = 'Le '.$now.', '.$this->getUser().', manager impliqué dans la prise en charge de la signalisation ' . $entity->getSignalisation()->getLibelle() . '
							a modifié l\'ingénieur support affecté à cette signalisation puis a désigné '.$entity->getUtilisateur().' pour la prise en charge de cette signalisation. Ce dernier est invité à 
							ajouter des actions pour corriger cette signalisation .';
    		Notification::notification ( $helper, $subject, $membreEmail, $commentaire, $entity );
    		SignalisationUtils::changeStatutSignalisation($em, $this->getUser(), Statut::SIGNALISATION_PRISE_CHARGE, $signalisation, "La prise en charge de cette signalisation a été validée.");
    		return new JsonResponse(array('url' => $this->generateUrl('details_signalisation', array('id' => $signalisation_id))));
    	}
    	
    	return new Response($this->renderView('OrangeMainBundle:SignalisationAnimateur:new.html.twig', array('entity' => $entity, 'form' => $form->createView())), 303);
    }
	    
    /**
     * Displays a form to create a new Bu entity.
     *
     * @Route("/signalisation_animateur_new/{signalisation_id}", name="nouveau_signalisation_animateur")
     * @Method("GET")
     * @Template()
     */
    public function newAction($signalisation_id)
    {
    	$em = $this->getDoctrine()->getManager();
    	$entity = new SignalisationAnimateur();
    	$form   = $this->createCreateForm($entity,'SignalisationAnimateur');
    	$signalisation = $em->getRepository("OrangeMainBundle:Signalisation")->find($signalisation_id);
    
    	return array(
    			'entity' => $entity,
    			'form'   => $form->createView(),
    			'signalisation' => $signalisation
    	);
    }

    /**
     * Finds and displays a SignalisationAnimateur entity.
     *
     * @Route("/{id}", name="signalisationanimateur_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OrangeMainBundle:SignalisationAnimateur')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SignalisationAnimateur entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing SignalisationAnimateur entity.
     *
     * @Route("/{id}/edit", name="signalisationanimateur_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OrangeMainBundle:SignalisationAnimateur')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SignalisationAnimateur entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a SignalisationAnimateur entity.
    *
    * @param SignalisationAnimateur $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(SignalisationAnimateur $entity)
    {
        $form = $this->createForm(new SignalisationAnimateurType(), $entity, array(
            'action' => $this->generateUrl('signalisationanimateur_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing SignalisationAnimateur entity.
     *
     * @Route("/{id}", name="signalisationanimateur_update")
     * @Method("PUT")
     * @Template("OrangeMainBundle:SignalisationAnimateur:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OrangeMainBundle:SignalisationAnimateur')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SignalisationAnimateur entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('signalisationanimateur_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a SignalisationAnimateur entity.
     *
     * @Route("/{id}", name="signalisationanimateur_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('OrangeMainBundle:SignalisationAnimateur')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find SignalisationAnimateur entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('signalisationanimateur'));
    }

    /**
     * Creates a form to delete a SignalisationAnimateur entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('signalisationanimateur_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
    
    public function getSignalisationMembresEmail($em, $entity)
    {
    	$membreEmail = array ();
    	$source = $entity->getSignalisation()->getSource()->getUtilisateur();
    	array_push ( $membreEmail, $source->getEmail () );
    	$animateur = $em->getRepository('OrangeMainBundle:SignalisationAnimateur')->findOneBy(array('actif' => true, 'signalisation' => $entity->getSignalisation()->getid()));
    	array_push($membreEmail, $animateur->getUtilisateur()->getEmail());
    	$structureAnimateur = $animateur->getUtilisateur()->getStructure();
    	$managerAnimateur = $em->getRepository('OrangeMainBundle:Utilisateur')->findOneBy(array('structure' => $structureAnimateur->getid(), 'manager' => true));
    	array_push($membreEmail, $managerAnimateur->getEmail());
    
    	return $membreEmail;
    }
}
