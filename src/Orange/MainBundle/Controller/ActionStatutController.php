<?php
namespace Orange\MainBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Orange\MainBundle\Entity\ActionStatut;
use Orange\MainBundle\Form\ActionStatutType;
use Orange\QuickMakingBundle\Controller\BaseController;
use Orange\MainBundle\Utils\ActionUtils;
use Orange\MainBundle\Entity\Statut;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Orange\MainBundle\OrangeMainEvents;
use Orange\QuickMakingBundle\Annotation\QMLogger;
/**
 * ActionStatut controller.
 * @Route("/actionstatut")
 */
class ActionStatutController extends BaseController
{
	
	
	
	/**
	 * @Route("/traitement/{action_id}", name="traitement_action")
	 * @Template()
	 */
	public function traitementAction($action_id){
		$em = $this->getDoctrine()->getManager();
		$action = $em->getRepository('OrangeMainBundle:Action')->find($action_id);
        $libelleStatut = $em->getRepository('OrangeMainBundle:Statut')->findOneByCode($action->getEtatReel())->getLibelle();
		return array('entity' => $action, 'libelle_statut' => $libelleStatut);
	}
	
	/**
	 * @Route("/liste_invalidation/{action_id}", name="liste_invalidation")
	 * @Template()
	 */
	public function listeInvalidationAction($action_id){
		$em  = $this->getDoctrine()->getManager();
		$statutInvalidation = $em->getRepository('OrangeMainBundle:Statut')->findOneByCode(Statut::EVENEMENT_INVALIDER);
		$listeInvalidations = $em->getRepository('OrangeMainBundle:ActionStatut')->findBy(array('action' => $action_id, 'statut' => $statutInvalidation->getId()));
		var_dump($listeInvalidations);exit;
		return $this->render("OrangeMainBundle:ActionStatut:validationManager.html.twig", array('liste_invalidations' => $listeInvalidations, 'action_id' => $action_id));
	}
	
	/**
	 * @Route("/choix_proposition/{invalidation_id}", name="choix_proposition")
	 * @Template("OrangeMainBundle:ActionStatut:choixProposition.html.twig")
	 * @Method({"GET", "POST"})
	 */
	public function choixPropositionAction(Request $request, $invalidation_id){
		$dispatcher = $this->container->get('event_dispatcher');
		if($request->getMethod() === 'POST'){
			$em = $this->getDoctrine()->getManager();
			$invalidation = $em->getRepository('OrangeMainBundle:ActionStatut')->find($invalidation_id);
			$event = $this->get('orange_main.action_event')->createForAction($invalidation->getAction());
			$dispatcher->dispatch(OrangeMainEvents::ACTION_DEMANDE_ABANDON_ACCEPTEE, $event);
			
// 			ActionUtils::changeStatutAction($em, $invalidation->getAction(), Statut::EVENEMENT_VALIDER, $this->getUser(), $invalidation->getCommentaire());
// 			$this->get('session')->getFlashBag()->add('success', array (
// 								'title' => 'Notification', 'body' => 'Enregistrement effectué avec succès'
// 			));
			return new JsonResponse(array('url' => $this->generateUrl('details_action', array('id' => $invalidation->getAction()->getId()))));
		}
		return array('invalidation_id' => $invalidation_id);
	}
	
	/**
	 * Validation
	 * @Route("/solder_action/{action_id}", name="solder_action")
	 * @Method({"GET", "POST"})
	 * @Template()
	 */
	public function solderAction(Request $request, $action_id) {
		$dispatcher = $this->container->get('event_dispatcher');
		$date = date('Y-m-d');
		$em   = $this->getDoctrine()->getManager();
		$action = $em->getRepository('OrangeMainBundle:Action')->find($action_id);
		$var = 0;
		if ($action->getEtatReel() == 'ACTION_NOUVELLE' &&  $action->getEtatCourant() == 'ACTION_NOUVELLE'){
			$var = 1;
		}
		$entity = new ActionStatut();
		$form = $this->createCreateForm($entity,'ActionStatut');
		if($request->getMethod() === 'POST') {
			$form->handleRequest($request);
			if($form->isValid()) {
				if($action->getEtatReel() === Statut::ACTION_FAIT_HORS_DELAI){
					$statut = $em->getRepository('OrangeMainBundle:Statut')->findOneByCode(Statut::ACTION_SOLDEE_HORS_DELAI);
					$action->setEtatCourant('ACTION_SOLDEE_HORS_DELAI');
					$action->setEtatReel('ACTION_SOLDEE_HORS_DELAI');
				}elseif($action->getEtatReel() === Statut::ACTION_FAIT_DELAI ){
					$statut = $em->getRepository('OrangeMainBundle:Statut')->findOneByCode(Statut::ACTION_SOLDEE_DELAI);
					$action->setEtatCourant('ACTION_SOLDEE_DELAI');
					$action->setEtatReel('ACTION_SOLDEE_DELAI');
				}
				$entity->setStatut($statut);
				$entity->setUtilisateur($this->getUser());
				$entity->setAction($action);
				$em->persist($entity);
				$em->flush();
				$event = $this->get('orange_main.action_event')->createForAction($action);
				$dispatcher->dispatch(OrangeMainEvents::ACTION_CLOTURE, $event);
				return new JsonResponse(array('url' => $this->generateUrl('details_action', array('id' => $action_id))));
			} else {
				return $this->render('OrangeMainBundle:ActionStatut:solder.html.twig', array(
						'entity' => $action, 'form' => $form->createView(), 'var' => $var), new Response(null, 303)
						);
			}
		}
		return array('entity' => $action, 'form' => $form->createView(), 'var' => $var);
	}
	
	/**
	 * Validation
	 * @QMLogger(message="Acceptation de demande")
	 * @Route("/demande_action/{action_id}", name="demande_action")
	 * @Method({"GET", "POST"})
	 * @Template()
	 */
	public function demandeAction(Request $request, $action_id) {
		$em   = $this->getDoctrine()->getManager();
		$statut = $em->getRepository('OrangeMainBundle:Statut')->findOneByCode(Statut::EVENEMENT_VALIDER);
		$dispatcher = $this->container->get('event_dispatcher');
		$date = date('Y-m-d');
		$action = $em->getRepository('OrangeMainBundle:Action')->find($action_id);
		$entity = new ActionStatut();
		$form = $this->createCreateForm($entity,'ActionStatut');
		if($request->getMethod() === 'POST') {
			$form->handleRequest($request);
			if($form->isValid()) {
				$entity->setAction($action);
				$entity->setStatut($statut);
				$entity->setUtilisateur($this->getUser());
				$em->persist($entity);
				if ($action->getEtatReel() == 'EVENEMENT_VALIDATION_ANIMATEUR_ATTENTE'){
					$statut = $em->getRepository('OrangeMainBundle:Statut')->findOneByCode(Statut::EVENEMENT_VALIDATION_ANIMATEUR_ATTENTE);
					$action->setEtatCourant('EVENEMENT_VALIDATION_ANIMATEUR_ATTENTE');
					$entity->setStatut($statut);
				}
				elseif($action->getEtatReel() === Statut::ACTION_DEMANDE_ABANDON ){
					$em->flush();
					$event = $this->get('orange_main.action_event')->createForAction($action);
					$dispatcher->dispatch(OrangeMainEvents::ACTION_DEMANDE_ABANDON_ACCEPTEE, $event);
				}
				elseif($action->getEtatReel() === Statut::ACTION_DEMANDE_REPORT ){
					$em->flush();
					$event = $this->get('orange_main.action_event')->createForAction($action);
					$dispatcher->dispatch(OrangeMainEvents::ACTION_DEMANDE_REPORT_ACCEPTEE, $event);
				}
				return new JsonResponse(array('url' => $this->generateUrl('details_action', array('id' => $action_id))));
			} else {
				return $this->render('OrangeMainBundle:ActionStatut:demande.html.twig', array(
						'entity' => $action, 'form' => $form->createView()), new Response(null, 303)
						);
			}
		}
		return array('entity' => $action, 'form' => $form->createView());
	}
	/**
	 * Validation
	 * @QMLogger(message="Validation")
	 * @Route("/validation_action/{action_id}", name="validation")
	 * @Method({"GET", "POST"})
	 * @Template()
	 */
	public function validationAction(Request $request, $action_id) {
		$dispatcher = $this->container->get('event_dispatcher');
		$date = date('Y-m-d'); 
		$em   = $this->getDoctrine()->getManager();
    	$action = $em->getRepository('OrangeMainBundle:Action')->find($action_id);
    	$var = 0;
    	if ($action->getEtatReel() == 'ACTION_NOUVELLE' &&  $action->getEtatCourant() == 'ACTION_NOUVELLE'){
    		$var = 1;
    	}
    	$entity = new ActionStatut();
    	$form = $this->createCreateForm($entity,'ActionStatut');
		if($request->getMethod() === 'POST') {
	    	$form->handleRequest($request);
	    	if($form->isValid()) {
	    		$entity->setUtilisateur($this->getUser());
	    		$entity->setAction($action);
				if ($action->getEtatReel() == 'ACTION_NOUVELLE'){
					$statut = $em->getRepository('OrangeMainBundle:Statut')->findOneByCode(Statut::ACTION_NON_ECHUE);
					$entity->setStatut($statut);
					$commentaire = "Action prise en charge par ".$this->getUser()->getNomComplet();
					$entity->setCommentaire($commentaire);
					$em->persist($entity);
					$em->flush();
					$event = $this->get('orange_main.action_event')->createForAction($action);
					$dispatcher->dispatch(OrangeMainEvents::ACTION_VALIDATE, $event);
					$this->container->get('session')->getFlashBag()->add('success', array (
							'title' => 'Notification', 'body'  => 'Prise en charge effective'
					));
				}elseif ($action->getEtatReel() == 'EVENEMENT_VALIDATION_ANIMATEUR_ATTENTE'){
					$statut = $em->getRepository('OrangeMainBundle:Statut')->findOneByCode(Statut::EVENEMENT_INVALIDER);
					$entity->setStatut($statut);
					$em->persist($entity);
					$em->flush();
					$event = $this->get('orange_main.action_event')->createForAction($action);
					$dispatcher->dispatch(OrangeMainEvents::ACTION_VALIDATION_ANIMATEUR, $event);
					$this->container->get('session')->getFlashBag()->add('success', array (
							'title' => 'Notification', 'body'  => 'Enregistrement éffectué avec succés.'
					));
				}
				return new JsonResponse(array('url' => $this->generateUrl('details_action', array('id' => $action_id))));
			} else {
				return $this->render('OrangeMainBundle:ActionStatut:validation.html.twig', array(
						'entity' => $action, 'form' => $form->createView(), 'var' => $var), new Response(null, 303)
					);
			}
		}
		return array('entity' => $action, 'form' => $form->createView(), 'var' => $var);
	}
	
    /**
     * Lists all ActionStatut entities.
     *
     * @Route("/", name="actionstatut")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(){
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('OrangeMainBundle:ActionStatut')->findAll();
        return array('entities' => $entities);
    }
    
    /**
     * Creates a new Domaine entity.
     * @QMLogger(message="Nouvelle demande")
     * @Route("/creer_action_statut/{action_id}", name="actionstatut_create")
     * @Method({"POST","GET"})
     * @Template("OrangeMainBundle:ActionStatut:new.html.twig")
     */
    public function createAction(Request $request, $action_id) {
    	$dispatcher = $this->container->get('event_dispatcher');
    	$em = $this->getDoctrine()->getManager();
    	$entity = new ActionStatut();
    	$statut = $em->getRepository('OrangeMainBundle:Statut')->findOneByCode(Statut::EVENEMENT_INVALIDER);
    	$form = $this->createCreateForm($entity,'ActionStatut');
    	$form->handleRequest($request);
   		$action = $em->getRepository('OrangeMainBundle:Action')->find($action_id);
    	if ($form->isValid()) {
    		$entity->setAction($action);
    		$entity->setStatut($statut);
    		$entity->setUtilisateur($this->getUser());
    		$em->persist($entity);
    		if($action->getEtatReel() === Statut::ACTION_DEMANDE_ABANDON){
    			$em->flush();
    			$event = $this->get('orange_main.action_event')->createForAction($action);
    			$dispatcher->dispatch(OrangeMainEvents::ACTION_DEMANDE_ABANDON_REFUSEE, $event);
    		}
    		if($action->getEtatReel() === Statut::EVENEMENT_VALIDATION_ANIMATEUR_ATTENTE){
    			$em->flush();
    			$event = $this->get('orange_main.action_event')->createForAction($action);
    			$dispatcher->dispatch(OrangeMainEvents::ACTION_PROPOSITION_ANIMATEUR, $event);
    		}
    		if($action->getEtatReel() === Statut::ACTION_NOUVELLE){
    			$em->flush();
    			$event = $this->get('orange_main.action_event')->createForAction($action);
    			$dispatcher->dispatch(OrangeMainEvents::ACTION_PROPOSITION_PORTEUR, $event);
    		}
    		if($action->getEtatReel() === Statut::ACTION_DEMANDE_REPORT){
    			$em->flush();
    			$event = $this->get('orange_main.action_event')->createForAction($action);
    			$dispatcher->dispatch(OrangeMainEvents::ACTION_DEMANDE_REPORT_REFUSEE, $event);
    		}
    		if($action->getEtatReel() === Statut::ACTION_FAIT_DELAI || $action->getEtatReel() === Statut::ACTION_FAIT_HORS_DELAI){
    			$em->flush();
    			$event = $this->get('orange_main.action_event')->createForAction($action);
    			$dispatcher->dispatch(OrangeMainEvents::ACTION_PAS_SOLDER, $event);
    		}
    		$this->get('session')->getFlashBag()->add('success', array (
								'title' => 'Notification', 'body' => 'Enregistrement effectué avec succès'
    		));
    		return new JsonResponse(array('url' => $this->generateUrl('details_action', array('id' => $action_id))));
    	}
    	
    	return $this->render('OrangeMainBundle:ActionStatut:new.html.twig',
    			array('entity' => $entity,
    					'action' => $action,
    					'form' => $form->createView()
    			), new \Symfony\Component\HttpFoundation\Response(null,303));
    }
    
    /**
     * Displays a form to create a new ActionStatut entity.
     * @Route("/action_statut_nouveau/{action_id}", name="actionstatut_new")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function newAction($action_id) {
    	$em = $this->getDoctrine()->getManager();
    	$action = $em->getRepository("OrangeMainBundle:Action")->find($action_id);
    	$entity = new ActionStatut();
    	$form = $this->createCreateForm($entity,'ActionStatut');
    	return array( 'entity' => $entity, 'form' => $form->createView(), 'action' => $action);
    }

    /**
     * Finds and displays a ActionStatut entity.
     * @Route("/{id}", name="actionstatut_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('OrangeMainBundle:ActionStatut')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ActionStatut entity.');
        }
        $deleteForm = $this->createDeleteForm($id);
        return array( 'entity' => $entity, 'delete_form' => $deleteForm->createView());
    }

    /**
     * Displays a form to edit an existing ActionStatut entity.
     * @Route("/{id}/edit", name="actionstatut_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('OrangeMainBundle:ActionStatut')->find($id);
        if(!$entity) {
            throw $this->createNotFoundException('Unable to find ActionStatut entity.');
        }
        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);
        return array('entity' => $entity, 'edit_form' => $editForm->createView(), 'delete_form' => $deleteForm->createView());
    }

    /**
    * Creates a form to edit a ActionStatut entity.
    * @param ActionStatut $entity The entity
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(ActionStatut $entity) {
        $form = $this->createForm(new ActionStatutType(), $entity, array(
            	'action' => $this->generateUrl('actionstatut_update', array('id' => $entity->getId())), 'method' => 'PUT'
        	));
        $form->add('submit', 'submit', array('label' => 'Update'));
        return $form;
    }
    
    /**
     * Edits an existing ActionStatut entity.
     * @Route("/{id}", name="actionstatut_update")
     * @Method("PUT")
     * @Template("OrangeMainBundle:ActionStatut:edit.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('OrangeMainBundle:ActionStatut')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ActionStatut entity.');
        }
        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        if($editForm->isValid()) {
            $em->flush();
            return $this->redirect($this->generateUrl('actionstatut_edit', array('id' => $id)));
        }
        return array('entity' => $entity, 'edit_form' => $editForm->createView(), 'delete_form' => $deleteForm->createView());
    }
    /**
     * Deletes a ActionStatut entity.
     * @Route("/{id}", name="actionstatut_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('OrangeMainBundle:ActionStatut')->find($id);
            if (!$entity) {
                throw $this->createNotFoundException('Unable to find ActionStatut entity.');
            }
            $em->remove($entity);
            $em->flush();
        }
        return $this->redirect($this->generateUrl('actionstatut'));
    }

    /**
     * Creates a form to delete a ActionStatut entity by id.
     * @param mixed $id The entity id
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('actionstatut_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();
    }
    
    /**
     * @Route("/historique/{action_id}", name="historique_action")
     * @Template()
     */
    public function historiqueAction($action_id) {
    	$em = $this->getDoctrine()->getManager();
    	$historiqueStatutAction = $em->getRepository("OrangeMainBundle:ActionStatut")->findByAction($action_id);
    	return array('liste_historique' => $historiqueStatutAction);	
    }
    
    /**
     * Formulaire de confirmation pour abandonner une action
     *
     * @Route("/demande_abandon_nouveau/{action_id}", name="abandon_new")
     * @Method({"GET", "POST"})
     * @Template("OrangeMainBundle:ActionStatut:abandon.html.twig")
     */
    public function abandonAction($action_id) {
    	$em = $this->getDoctrine()->getManager();
    	$action = $em->getRepository("OrangeMainBundle:Action")->find($action_id);
    	$entity = new ActionStatut();
    	$form = $this->createCreateForm($entity,'ActionStatut');
    	return array('entity' => $entity, 'form' => $form->createView(), 'action' => $action);
    }
    
 	/**
     * Creates a new Domaine entity.
     *
     * @Route("/creer_abandon/{action_id}", name="abandon_create")
     * @Method({"GET", "POST"})
     * @Template("OrangeMainBundle:ActionStatut:abandon.html.twig")
     */
    public function createAbandonAction(Request $request, $action_id) {
    	$dispatcher = $this->container->get('event_dispatcher');
    	$entity = new ActionStatut();
    	$em = $this->getDoctrine()->getManager();
    	$form = $this->createCreateForm($entity,'ActionStatut');
    	$form->handleRequest($request);
    	$action = $em->getRepository('OrangeMainBundle:Action')->find($action_id);
    	if ($form->isValid()) {
    		$em->persist($entity);
    		$statut = $em->getRepository('OrangeMainBundle:Statut')->findOneByCode(Statut::ACTION_DEMANDE_ABANDON);
    		$entity->setUtilisateur($this->getUser());
    		$entity->setAction($action);
    		$entity->setStatut($statut);
    		$em->flush();
    		$event = $this->get('orange_main.action_event')->createForAction($entity->getAction());
    		$dispatcher->dispatch(OrangeMainEvents::ACTION_DEMANDE_ABANDON, $event);
    		$this->get('session')->getFlashBag()->add('success', array (
							'title' => 'Notification', 'body' => 'Enrégistrement effectué avec succès'
    			));
    		return new JsonResponse(array('url' => $this->generateUrl('details_action', array('id' => $action_id))));
    	}
    	return new Response($this->renderView('OrangeMainBundle:ActionStatut:abandon.html.twig', array('action'	=> $action, 'entity' => $entity, 'form' => $form->createView())), 303);
    }
    
    /**
	 * Validation
	 *
	 * @Route("/cloturer_action/{action_id}", name="cloturer_action")
	 * @Method({"GET", "POST"})
	 * @Template()
	 */
	public function cloturerAction(Request $request, $action_id) {
		$dispatcher = $this->container->get('event_dispatcher');
		$date = date('Y-m-d');
    	$entity = new ActionStatut();
    	$em = $this->getDoctrine()->getManager();
    	$form = $this->createCreateForm($entity,'ActionStatut');
		$action = $em->getRepository('OrangeMainBundle:Action')->find($action_id);
		if($request->getMethod() == 'POST') {
			$form->handleRequest($request);
			if($form->isValid()) {
				if ($entity->getDateFinExecut()){
					if($action->getDateInitial()->format('Y-m-d') >= $entity->getDateFinExecut()->format('Y-m-d')){
						$statut = $em->getRepository('OrangeMainBundle:Statut')->findOneByCode(Statut::ACTION_FAIT_DELAI);
						$action->setDateFinExecut($entity->getDateFinExecut());
					}
					else{
						$statut = $em->getRepository('OrangeMainBundle:Statut')->findOneByCode(Statut::ACTION_FAIT_HORS_DELAI);
						$action->setDateFinExecut($entity->getDateFinExecut());
					}
				}else{
					if($action->getDateInitial()->format('Y-m-d') >= $date){
						$statut = $em->getRepository('OrangeMainBundle:Statut')->findOneByCode(Statut::ACTION_FAIT_DELAI);
						$action->setDateFinExecut($entity->getDateFinExecut());
					}
					else{
						$statut = $em->getRepository('OrangeMainBundle:Statut')->findOneByCode(Statut::ACTION_FAIT_HORS_DELAI);
						$action->setDateFinExecut($entity->getDateFinExecut());
					}
				}
				if($entity->getErq() && $entity->getErq()->getFile()) {
					$entity->getErq()->setType($this->container->getParameter('types')['demande_solde']);
					$entity->getErq()->setAction($action);
					$entity->getErq()->setNomFichier($entity->getErq()->getFile()->getClientOriginalName());
					$entity->getErq()->setUtilisateur($this->getUser());
	    			$em->persist($entity->getErq());
				}
	    		$entity->setUtilisateur($this->getUser());
	    		$entity->setAction($action);
	    		$entity->setStatut($statut);
	    		$em->persist($entity);
	    		$em->flush();
	    		$event = $this->get('orange_main.action_event')->createForAction($action);
	    		$dispatcher->dispatch(OrangeMainEvents::ACTION_FAITE, $event);
				$this->get('session')->getFlashBag()->add('success', array (
							'title' => 'Notification', 'body' => 'Enregistrement effectué avec succès'
					));
				return new JsonResponse(array('url' => $this->generateUrl('details_action', array('id' => $action_id))));
			} else {
				return $this->render('OrangeMainBundle:ActionStatut:cloturer.html.twig', array('action' => $action, 'form' => $form->createView()), new Response(null, 303));
			}
		}
		return array('action' => $action, 'form' => $form->createView());
	}
     
}
