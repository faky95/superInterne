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
        $libelleStatut = $em->getRepository('OrangeMainBundle:Statut')->findOneByCode($action->getEtatCourant())->getLibelle();
		return array('entity' => $action, 'libelle_statut' => $libelleStatut);
	}
	
	/**
	 * @Route("/liste_invalidation/{action_id}", name="liste_invalidation")
	 * @Template()
	 */
	public function listeInvalidationAction($action_id){
		$em  = $this->getDoctrine()->getManager();
		$statutInvalidation = $em->getRepository('OrangeMainBundle:Statut')->findOneByCode(Statut::INVALIDER);
		$listeInvalidations = $em->getRepository('OrangeMainBundle:ActionStatut')->findBy(array('action' => $action_id, 'statut' => $statutInvalidation->getId()));
		return $this->render("OrangeMainBundle:ActionStatut:validationManager.html.twig", array('liste_invalidations' => $listeInvalidations, 'action_id' => $action_id));
	}
	
	/**
	 * @Route("/choix_proposition/{invalidation_id}", name="choix_proposition")
	 * @Template("OrangeMainBundle:ActionStatut:choixProposition.html.twig")
	 * @Method({"GET", "POST"})
	 */
	public function choixPropositionAction(Request $request, $invalidation_id){
		if($request->getMethod() === 'POST'){
			$em = $this->getDoctrine()->getManager();
			$invalidation = $em->getRepository('OrangeMainBundle:ActionStatut')->find($invalidation_id);
			ActionUtils::changeStatutAction($em, $invalidation->getAction(), Statut::VALIDER, $this->getUser(), $invalidation->getCommentaire());
			$this->get('session')->getFlashBag()->add('success', array (
								'title' => 'Notification', 'body' => 'Enregistrement effectué avec succès'
			));
			return new JsonResponse(array('url' => $this->generateUrl('details_action', array('id' => $invalidation->getAction()->getId()))));
		}
		return array('invalidation_id' => $invalidation_id);
	}
	
	/**
	 * Validation
	 * @Route("/validation_action/{action_id}", name="validation")
	 * @Method({"GET", "POST"})
	 * @Template()
	 */
	public function validationAction(Request $request, $action_id) {
		$date = date('Y-m-d'); 
		$em   = $this->getDoctrine()->getManager();
    	$entity = new ActionStatut();
    	$form = $this->createCreateForm($entity, 'ActionStatut');
		$action = $em->getRepository('OrangeMainBundle:Action')->find($action_id);
		if($request->getMethod() === 'POST') {
			$statut = $action->getActionStatut()->last();
	    	$form->handleRequest($request);
	    	if($form->isValid()) {
				if($statut->getDateStatut()->format('Y-m-d') > $action->getDateInitial()->format('Y-m-d') ){
					$statut = $em->getRepository('OrangeMainBundle:Statut')->findOneByCode(Statut::ACTION_SOLDEE_HORS_DELAI);
				}
				else
					$statut = $em->getRepository('OrangeMainBundle:Statut')->findOneByCode(Statut::ACTION_SOLDEE_DELAI);
				
	    		$entity->setUtilisateur($this->getUser());
	    		$entity->setAction($action);
	    		$entity->setStatut($statut);
	    		$em->persist($entity);
	    		$em->flush();
				return new JsonResponse(array('url' => $this->generateUrl('details_action', array('id' => $action_id))));
			} else {
				return $this->render('OrangeMainBundle:ActionStatut:validation.html.twig', array(
						'entity' => $action, 'form' => $form->createView()), new Response(null, 303)
					);
			}
		}
		return array('entity' => $action, 'form' => $form->createView());
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
     *
     * @Route("/creer_action_statut/{action_id}", name="actionstatut_create")
     * @Method({"POST","GET"})
     * @Template("OrangeMainBundle:ActionStatut:new.html.twig")
     */
    public function createAction(Request $request, $action_id) {
    	$em = $this->getDoctrine()->getManager();
    	$entity = new ActionStatut();
    	$form = $this->createCreateForm($entity,'ActionStatut');
    	$form->handleRequest($request);
   		$action = $em->getRepository('OrangeMainBundle:Action')->find($action_id);
    	if ($form->isValid()) {
    		$em->persist($entity);
    		$statut = $em->getRepository('OrangeMainBundle:Statut')->findOneByCode(Statut::INVALIDER);
    		$entity->setUtilisateur($this->getUser());
    		$entity->setAction($action);
    		$entity->setStatut($statut);
    		$em->flush();
    		$this->get('session')->getFlashBag()->add('success', array (
								'title' => 'Notification', 'body' => 'Enregistrement effectué avec succès'
    		));
    		return new JsonResponse(array('url' => $this->generateUrl('details_action', array('id' => 27766))));
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
	 * @Route("/solder_action/{action_id}", name="solder")
	 * @Method({"GET", "POST"})
	 * @Template()
	 */
	public function solderAction(Request $request, $action_id) {
		$date = date('Y-m-d');
    	$entity = new ActionStatut();
    	$em = $this->getDoctrine()->getManager();
    	$form = $this->createCreateForm($entity,'ActionStatut');
		$action = $em->getRepository('OrangeMainBundle:Action')->find($action_id);
		if($request->getMethod() == 'POST') {
			$form->handleRequest($request);
			if($form->isValid()) {
				if($action->getDateInitial()->format('Y-m-d') >= $date){
					$statut = $em->getRepository('OrangeMainBundle:Statut')->findOneByCode(Statut::ACTION_FAIT_DELAI);
				}
				else{
					$statut = $em->getRepository('OrangeMainBundle:Statut')->findOneByCode(Statut::ACTION_FAIT_HORS_DELAI);
				}
	    		$entity->setUtilisateur($this->getUser());
	    		$entity->setAction($action);
	    		$entity->setStatut($statut);
	    		$em->persist($entity);
	    		$em->flush();
				$this->get('session')->getFlashBag()->add('success', array (
							'title' => 'Notification', 'body' => 'Enregistrement effectué avec succès'
					));
				return new JsonResponse(array('url' => $this->generateUrl('details_action', array('id' => $action_id))));
			} else {
				return $this->render('OrangeMainBundle:ActionStatut:solder.html.twig', array('action' => $action, 'form' => $form->createView()), new Response(null, 303));
			}
		}
		return array('action' => $action, 'form' => $form->createView());
	}
     
}
