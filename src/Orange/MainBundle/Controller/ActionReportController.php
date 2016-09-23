<?php

namespace Orange\MainBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Orange\MainBundle\Entity\ActionReport;
use Orange\MainBundle\Entity\ActionStatut;
use Orange\MainBundle\Form\ActionReportType;
use Orange\MainBundle\Utils\ActionUtils;
use Orange\MainBundle\Entity\Statut;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Orange\QuickMakingBundle\Annotation\QMLogger;
use Orange\MainBundle\OrangeMainEvents;
/**
 * ActionReport controller.
 *
 * @Route("/actionreport")
 */
class ActionReportController extends Controller
{

    /**
     * Lists all ActionReport entities.
     *
     * @Route("/", name="actionreport")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('OrangeMainBundle:ActionReport')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    
    /**
     * Creates a form to create a ActionReport entity.
     * @param ActionReport $entity The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ActionReport $entity, $action_id) {
    	$form = $this->createForm(new ActionReportType(), $entity, array(
    			'action' => $this->generateUrl('actionreport_create', array('action_id' => $action_id)),
    			'method' => 'POST',
    		));
    	$form->add('submit', 'submit', array('label' => 'Create'));
    	return $form;
    }
    
    /**
     * Displays a form to create a new ActionStatut entity.
     * @QMLogger(message="Demande de report")
     * @Route("/action_report_nouveau/{action_id}", name="actionreport_new")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function newAction($action_id) {
    	$em = $this->getDoctrine()->getManager();
    	$action = $em->getRepository("OrangeMainBundle:Action")->find($action_id);
    	$entity = new ActionReport();
    	$entity->setAction($action);
    	$form = $this->createCreateForm($entity,'ActionReport');
    	return array('entity' => $entity, 'action_id' => $action_id, 'form' => $form->createView(), 'action' => $action);
    }
    
    /**
     * Creates a new Action report entity.
     * @Route("/creer_action_report/{action_id}", name="actionreport_create")
     * @Method({"POST","GET"})
     * @Template("OrangeMainBundle:ActionReport:new.html.twig")
     */
    public function createActionReportAction(Request $request, $action_id) {
    	$dispatcher = $this->container->get('event_dispatcher');
    	$em = $this->getDoctrine()->getManager();
    	$action = $em->getRepository('OrangeMainBundle:Action')->find($action_id);
    	$entity = new ActionReport();
    	$statut = new ActionStatut();
    	$form = $this->createCreateForm($entity,'ActionReport');
    	$form->handleRequest($request);
    	if ($form->isValid()) {
    		$code = $em->getRepository('OrangeMainBundle:Statut')->findOneByCode(Statut::ACTION_DEMANDE_REPORT);
    		$statut->setUtilisateur($this->getUser());
    		$statut->setAction($action);
    		$statut->setStatut($code);
    		$statut->setCommentaire($entity->getDescription().'| Date du report: '.$entity->getDate()->format('d-m-Y'));
    		$entity->setAction($action);
    		$em->persist($entity);
    		$em->persist($statut);
    		$em->flush();
    		ActionUtils::updateEtatReport($em, $action, Statut::ACTION_DEMANDE_REPORT);
    		$event = $this->get('orange_main.action_event')->createForAction($action);
    		$dispatcher->dispatch(OrangeMainEvents::ACTION_DEMANDE_REPORT, $event);
    		return new JsonResponse(array('url' => $this->generateUrl('details_action', array('id' => $action_id))));
    	}
    	return new Response($this->renderView('OrangeMainBundle:ActionReport:new.html.twig', array(
    						'entity' => $entity, 'action' => $action, 'action_id'	=> $action_id, 'form' => $form->createView())), 303
    			);
    }
    
    /**
     * Finds and displays a ActionReport entity.
     *
     * @Route("/{id}", name="actionreport_show")
     * @Method("GET")
     * @Template()  
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('OrangeMainBundle:ActionReport')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ActionReport entity.');
        }
        $deleteForm = $this->createDeleteForm($id);
        return array('entity' => $entity, 'delete_form' => $deleteForm->createView());
    }

    /**
     * Displays a form to edit an existing ActionReport entity.
     *
     * @Route("/{id}/edit", name="actionreport_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OrangeMainBundle:ActionReport')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ActionReport entity.');
        }

        $editForm = $this->createEditForm($entity);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    }
    
    /**
     * Displays a form to edit an existing ActionReport entity.
     *
     * @Route("/traitement_report/{id}", name="report_action")
     * @Template()
     */
    public function reportAction($id)
    {
    	$em = $this->getDoctrine()->getManager();
    	
    	$action = $em->getRepository('OrangeMainBundle:Action')->find($id);
    	
    	$entity = $em->getRepository('OrangeMainBundle:ActionReport')->findOneByAction($id);
    	
    	if (!$entity) {
    		throw $this->createNotFoundException('Unable to find ActionReport entity.');
    	}
    	 
    	$editForm = $this->createEditForm($entity);
    	
    	return array(
    			'entity' 	  => $entity,
    			'edit_form'	  => $editForm->createView(),			 	
    	);
    }
    
    
    /**
     * Edits an existing ActionReport entity.
     *
     * @Route("/reporter/{id}", name="reporter_update")
     * @Method({"GET", "POST"})
     * @Template("OrangeMainBundle:ActionReport:report.html.twig")
     */
    public function reporterAction(Request $request, $id)
    {
    	$em = $this->getDoctrine()->getManager();
    	
    	$entity = $em->getRepository('OrangeMainBundle:ActionReport')->find($id);
    	
    	$action = $entity->getAction();
    
    	if (!$entity) {
    		throw $this->createNotFoundException('Unable to find ActionReport entity.');
    	}
    
    	$deleteForm = $this->createDeleteForm($id);
    	$editForm = $this->createForm(new ActionReportType(), $entity);
    	$editForm->handleRequest($request);
    
    	if ($editForm->isValid()) {
//     		var_dump($entity->getDate()); exit;
    		if ($editForm->get('valider')->isClicked()) {
    			ActionUtils::reportDelai($em, $entity);
    			ActionUtils::changeStatutAction($em, $action, Statut::EVENEMENT_DEMANDE_DE_REPORT_ACCEPTE, $this->getUser());
    			ActionUtils::changeStatutAction($em, $action, Statut::ACTION_NON_ECHUE, $this->getUser());
    			ActionUtils::updateEtatCourantEntity($em, $action, Statut::ACTION_TRAITEMENT);
    			return $this->redirect($this->generateUrl('details_action', array('id' => $action->getId())));
    		}
    		elseif ($editForm->get('invalider')->isClicked()) {
    			ActionUtils::changeStatutAction($em, $action, Statut::EVENEMENT_DEMANDE_DE_REPORT_REFUS, $this->getUser());
    			ActionUtils::changeStatutAction($em, $action, Statut::ACTION_NON_ECHUE, $this->getUser());
    			ActionUtils::updateEtatCourantEntity($em, $action, Statut::ACTION_NON_ECHUE);
    			return $this->redirect($this->generateUrl('details_action', array('id' => $action->getId())));
    		}
    		elseif ($editForm->get('modifier')->isClicked()){
    			return $this->redirect($this->generateUrl('actionreport_edit', array('id' => $id)));
    		}
    	}
    	
    	return array(
    			'entity'      => $entity,
    			'edit_form'   => $editForm->createView(),
    			'delete_form' => $deleteForm->createView(),
    	);
    }

    /**
    * Creates a form to edit a ActionReport entity.
    *
    * @param ActionReport $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(ActionReport $entity)
    {
        $form = $this->createForm(new ActionReportType(), $entity, array(
            'action' => $this->generateUrl('actionreport_update', array('id' => $entity->getId())),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    
    /**
     * Edits an existing ActionReport entity.
     *
     * @Route("/{id}", name="actionreport_update")
     * @Method("POST")
     * @Template("OrangeMainBundle:ActionReport:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OrangeMainBundle:ActionReport')->find($id);
        
        $action = $entity->getAction();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ActionReport entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
        	$em->flush();
        	ActionUtils::reportDelai($em, $entity);
        	ActionUtils::changeStatutAction($em, $entity->getAction(), Statut::EVENEMENT_DEMANDE_DE_REPORT_ACCEPTE, $this->getUser());
        	ActionUtils::changeStatutAction($em, $entity->getAction(), Statut::ACTION_NON_ECHUE, $this->getUser());
        	ActionUtils::updateEtatCourantEntity($em, $action, Statut::ACTION_NON_ECHUE);
        	return $this->redirect($this->generateUrl('details_action', array('id' => $entity->getAction()->getId())));
        }
        
        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    
    
    /**
     * Deletes a ActionReport entity.
     *
     * @Route("/{id}", name="actionreport_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('OrangeMainBundle:ActionReport')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find ActionReport entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('actionreport'));
    }

    /**
     * Creates a form to delete a ActionReport entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('actionreport_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
    
    /**
     * For animateur or porteur validation the status change to traitement
     *
     * @Route("/valider_report/{action_id}", name="valider_report")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function validerAction(Request $request, $action_id){
    	$em   = $this->getDoctrine()->getManager();
    	$action = $em->getRepository('OrangeMainBundle:Action')->find($action_id);
    	if($request->getMethod() == 'POST') {
    		ActionUtils::changeStatutAction($em, $action, Statut::EVENEMENT_DEMANDE_DE_REPORT_ACCEPTE, $this->getUser(), "La demande de report d'échéance a été accepté");
    		ActionUtils::updateEtatCourantEntity($em, $action, Statut::ACTION_NON_ECHUE);
    		return new JsonResponse(array('url' => $this->generateUrl('details_action', array('id' => $action_id))));
    	}
    	return array('entity' => $action);
    }
    
    /**
     * For animateur or porteur validation the status change to traitement
     *
     * @Route("/valider_report/{action_id}", name="invalider_report")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function invaliderAction(Request $request, $action_id){
    	$em   = $this->getDoctrine()->getManager();
    	$action = $em->getRepository('OrangeMainBundle:Action')->find($action_id);
    	if($request->getMethod() == 'POST') {
    		ActionUtils::changeStatutAction($em, $action, Statut::EVENEMENT_DEMANDE_DE_REPORT_REFUS, $this->getUser(), "La demande de report d'échéance a été refusée");
    		ActionUtils::updateEtatCourantEntity($em, $action, Statut::ACTION_NON_ECHUE);
    		return new JsonResponse(array('url' => $this->generateUrl('details_action', array('id' => $action_id))));
    	}
    	return array('entity' => $action);
    }
    
    
}
