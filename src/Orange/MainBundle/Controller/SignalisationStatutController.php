<?php
namespace Orange\MainBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Orange\MainBundle\Entity\SignalisationStatut;
use Orange\MainBundle\Entity\Statut;
use Orange\MainBundle\Entity\TypeStatut;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Orange\QuickMakingBundle\Controller\BaseController;
use Orange\MainBundle\OrangeMainEvents;
use Orange\MainBundle\Entity\Action;

/**
 * SignalisationStatut controller.
 * @Route("/signalisationstatut")
 */
class SignalisationStatutController extends BaseController {
	
	/**
	 * Lists all SignalisationStatut entities.
	 * @Route("/", name="signalisationstatut")
	 * @method ("GET")
	 * @Template()
	 */
	public function indexAction() {
		$em = $this->getDoctrine ()->getManager ();
		$entities = $em->getRepository('OrangeMainBundle:SignalisationStatut' )->findAll ();
		return array ('entities' => $entities);
	}
	
	/**
	 * Creates a new SignalisationStatut entity.
	 *
	 * @Route("/creation_statut/{valide}/{id}", name="signalisationstatut_create")
	 * @method ("POST")
	 * @Template("OrangeMainBundle:SignalisationStatut:new.html.twig")
	 */
	public function createAction(Request $request, $valide, $id) {
		$dispatcher = $this->container->get('event_dispatcher' );
		$em = $this->getDoctrine ()->getManager ();
		$entity = new SignalisationStatut ();
		$action = new Action ();
		$signalisation = $em->getRepository('OrangeMainBundle:Signalisation' )->find($id );
		$typeStatut = $em->getRepository('OrangeMainBundle:TypeStatut' )->findOneByLibelle(TypeStatut::TYPE_SIGNALISATION );
		$statutSignalisation = $em->getRepository('OrangeMainBundle:Statut')->findOneBy(array(
				'code' => $valide, 'typeStatut' => $typeStatut->getId()
			));
		$form = $this->createCreateForm($entity, 'SignalisationStatut');
		$form->handleRequest($request);
		if ($form->isValid()) {
			$em->persist($entity);
			$entity->setUtilisateur($this->getUser ());
			$entity->setSignalisation($signalisation );
			$entity->setStatut($statutSignalisation );
			$entity->setCommentaire("'" . $entity->getCommentaire () . "' " . $this->getUser ());
			$entity->setDateStatut(new \DateTime ());
			$em->flush ();
			if ($valide == 'SIGN_PRISE_EN_CHARGE') {
				$event = $this->get('orange_main.signalisation_event' )->createForSignalisation($entity );
				$dispatcher->dispatch(OrangeMainEvents::SIGNALISATION_PRISE_EN_CHARGE, $event );
				$this->get('session' )->getFlashBag ()->add('success', array (
						'title' => 'Notification', 'body' => 'Signalisation prise en charge.' 
					));
			}
			if ($valide == 'SIGN_INVALIDE') {
				$event = $this->get('orange_main.signalisation_event' )->createForSignalisation($entity );
				$dispatcher->dispatch(OrangeMainEvents::SIGNALISATION_NON_PRISE_EN_CHARGE, $event );
				$this->get('session' )->getFlashBag ()->add('success', array (
						'title' => 'Notification', 'body' => 'Enrégistrement effectué avec succès' 
					));
			}
			if ($valide == 'SIGN_TRAIT_EFFICACE') {
				$event = $this->get('orange_main.signalisation_event' )->createForSignalisation($entity );
				$dispatcher->dispatch(OrangeMainEvents::SIGNALISATION_EFFICACE, $event );
				$this->get('session' )->getFlashBag ()->add('success', array (
						'title' => 'Notification', 'body' => 'Enrégistrement effectué avec succès' 
					));
			}
			if($valide == 'SIGN_TRAIT_NON_EFFICACE') {
				$event = $this->get('orange_main.signalisation_event' )->createForSignalisation($entity );
				$dispatcher->dispatch(OrangeMainEvents::SIGNALISATION_NON_EFFICACE, $event );
				$this->get('session' )->getFlashBag ()->add('success', array (
						'title' => 'Notification', 'body' => 'Enregistrement effectué avec succès' 
					));
			}
			return new JsonResponse(array (
					'url' => $this->generateUrl('details_signalisation', array('id' => $entity->getSignalisation()->getId()))
				));
		}
		return new Response($this->renderView('OrangeMainBundle:SignalisationStatut:new.html.twig', array(
				'valide' => $valide, 'id' => $id, 'form' => $form->createView() 
			)), 303);
	}
	
	/**
	 * Displays a form to create a new SignalisationStatut entity.
	 * @Route("/signalisation_statut_nouveau/{valide}/{id}", name="signalisationstatut_new")
	 * @method ("GET")
	 * @Template()
	 */
	public function newAction($valide, $id) {
		$entity = new SignalisationStatut();
		$form = $this->createCreateForm($entity, 'SignalisationStatut');
		return array('entity' => $entity, 'form' => $form->createView(), 'valide' => $valide, 'id' => $id);
	}
	
	/**
	 * Displays a form to edit an existing SignalisationStatut entity.
	 * @Route("/{id}/edit", name="signalisationstatut_edit")
	 * @method ("GET")
	 * @Template()
	 */
	public function editAction($id) {
		$em = $this->getDoctrine ()->getManager ();
		$entity = $em->getRepository('OrangeMainBundle:SignalisationStatut' )->find($id );
		if(! $entity) {
			throw $this->createNotFoundException('Unable to find SignalisationStatut entity.' );
		}
		$editForm = $this->createEditForm($entity);
		$deleteForm = $this->createDeleteForm($id);
		return array ('entity' => $entity, 'edit_form' => $editForm->createView(), 'delete_form' => $deleteForm->createView ());
	}
	
	/**
	 * Edits an existing SignalisationStatut entity.
	 * @Route("/{id}", name="signalisationstatut_update")
	 * @method ("PUT")
	 * @Template("OrangeMainBundle:SignalisationStatut:edit.html.twig")
	 */
	public function updateAction(Request $request, $id) {
		$em = $this->getDoctrine ()->getManager ();
		$entity = $em->getRepository('OrangeMainBundle:SignalisationStatut' )->find($id );
		if (! $entity) {
			throw $this->createNotFoundException('Unable to find SignalisationStatut entity.' );
		}
		$deleteForm = $this->createDeleteForm($id );
		$editForm = $this->createEditForm($entity );
		$editForm->handleRequest($request );
		if ($editForm->isValid ()) {
			$em->flush ();
			return $this->redirect($this->generateUrl('signalisationstatut_edit', array('id' => $id)));
		}
		return array ('entity' => $entity, 'edit_form' => $editForm->createView (), 'delete_form' => $deleteForm->createView());
	}
	
	/**
	 * Deletes a SignalisationStatut entity.
	 * @Route("/{id}", name="signalisationstatut_delete")
	 * @method("DELETE")
	 */
	public function deleteAction(Request $request, $id) {
		$form = $this->createDeleteForm($id );
		$form->handleRequest($request );
		if ($form->isValid ()) {
			$em = $this->getDoctrine ()->getManager ();
			$entity = $em->getRepository('OrangeMainBundle:SignalisationStatut' )->find($id );
			if (! $entity) {
				throw $this->createNotFoundException('Unable to find SignalisationStatut entity.' );
			}
			$em->remove($entity );
			$em->flush ();
		}
		return $this->redirect($this->generateUrl('signalisationstatut'));
	}
	
	/**
	 * Creates a form to delete a SignalisationStatut entity by id.
	 * @param mixed $id
	 * @return \Symfony\Component\Form\Form The form
	 */
	private function createDeleteForm($id) {
		return $this->createFormBuilder ()->setAction($this->generateUrl('signalisationstatut_delete', array ('id' => $id)))
				->setMethod('DELETE' )->add('submit', 'submit', array('label' => 'Delete'))
				->getForm();
	}
}
