<?php

namespace Orange\MainBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Orange\MainBundle\Entity\ActionGenerique;
use Orange\QuickMakingBundle\Controller\BaseController;
use Orange\MainBundle\Entity\Statut;
use Orange\MainBundle\Utils\ActionUtils;
use Orange\MainBundle\OrangeMainEvents;
use Orange\MainBundle\Criteria\ActionGeneriqueCriteria;
use Orange\MainBundle\Entity\Action;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Orange\MainBundle\Criteria\ActionCriteria;
use Orange\MainBundle\Form\OrientationActionType;
use Orange\MainBundle\Entity\ActionGeneriqueHasAction;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * ActionGenerique controller
 */
class ActionGeneriqueController extends BaseController
{
	/**
	 * @Route("/les_actiongeneriques", name="les_actiongeneriques")
	 * @Method({"GET","POST"})
	 * @Template()
	 */
	public function indexAction(Request $request)
	{
		$em = $this->getDoctrine()->getManager();
		$this->get('session')->set('actiongenerique_criteria', array());
		$form = $this->createForm(new ActionGeneriqueCriteria());
		if($request->getMethod()=="POST"){
		       $this->modifyRequestForForm($request, array(), $form);
		       $this->get('session')->set('actiongenerique_criteria', $request->request->get($form->getName()));
		}
		return array('form'=>$form->createView());
	}
	
	/**
	 * @Route("/liste_des_actiongeneriques", name="liste_des_actiongeneriques")
	 */
	public function listeAction(Request $request){
		$em = $this->getDoctrine()->getManager();
		$criteria = null;
		$form = $this->createForm(new ActionGeneriqueCriteria());
		$this->modifyRequestForForm($request, $this->get('session')->get('actiongenerique_criteria'), $form);
		$criteria=$form->getData();
		$queryBuilder = $em->getRepository('OrangeMainBundle:ActionGenerique')->listAllElements($criteria);
		return $this->paginate($request, $queryBuilder);
	}
	
	
	/**
	 * @Route("/nouvelle_actiongenerique", name="nouvelle_actiongenerique")
	 * @Method("GET")
	 * @Template()
	 */
	public function newAction()
	{
		$entity = new ActionGenerique();
		$form   = $this->createCreateForm($entity,'ActionGenerique');
	
		return array(
				'entity' => $entity,
				'form'   => $form->createView(),
		);
	}
	
	/**
	 * @Route("/creer_actiongenerique", name="creer_actiongenerique")
	 * @Method("POST")
	 * @Template("OrangeMainBundle:ActionGenerique:new.html.twig")
	 */
	public function createAction(Request $request)
	{
		$em = $this->getDoctrine()->getManager();
		$entity = new ActionGenerique();
		$dispatcher = $this->get('event_dispatcher');
		
		$form = $this->createCreateForm($entity,'ActionGenerique');
		$form->handleRequest($request);
	
		if ($form->isValid()) {
			$entity->setAnimateur($this->getUser());
			$entity->setStatut(Statut::ACTION_NON_ECHUE);
			$em->persist($entity);
			$em->flush();
			ActionUtils::setReferenceActionGenerique($em, $entity);
			$event = $this->get('orange_main.actiongenerique_event')->createForActionGenerique($entity);
			$dispatcher->dispatch(OrangeMainEvents::ACTIONGENERIQUE_NOUVEAU, $event);
			$this->get('session')->getFlashBag()->add('success', array('title' => 'Notification', 'body' =>  'Action générique ajoutée avec succés!'));
			return $this->redirect($this->generateUrl('details_actiongenerique', array('id' => $entity->getId())));
		}
	
		return array(
				'entity' => $entity,
				'form'   => $form->createView(),
		);
	}
   


    /**
     * @Route("/details_actiongenerique/{id}", name="details_actiongenerique")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $repo= $em->getRepository('OrangeMainBundle:ActionGenerique');
        $entity = $repo->find($id);
        $stats = $repo->getStatsSimpleActionByActionGenerique($id)->getQuery()->getArrayResult();
        $map= $this->container->get('orange.main.dataStats')->mapToHaveLibelle($stats);

        if (!$entity) 
            throw $this->createNotFoundException('L\'action générique n\'existe pas.');
        
        return array(
            'action'      => $entity,
        		'stats'   => $map
        );
    }

    /**
     * Displays a form to edit an existing ActionGenerique entity.
     *
     * @Route("/{id}/edition_actiongenerique", name="edition_actiongenerique")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('OrangeMainBundle:ActionGenerique')->find($id);
        if (!$entity) {
        	throw $this->createNotFoundException('L\'action générique n\'existe pas.');
        }
        $editForm = $this->createCreateForm($entity,'ActionGenerique');
        return array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        );
    }

   
    /**
     * @Route("/{id}/modifier_actiongenerique", name="modifier_actiongenerique")
     * @Method("POST")
     * @Template("OrangeMainBundle:ActionGenerique:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('OrangeMainBundle:ActionGenerique')->find($id);
        if (!$entity) 
        	throw $this->createNotFoundException('L\'action générique n\'existe pas.');

        $editForm = $this->createCreateForm($entity,'ActionGenerique');
        $editForm->handleRequest($request);
        if ($editForm->isValid()) {
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', array('title' => 'Notification', 'body' =>  'Action générique modifiée avec succés!'));
            return $this->redirect($this->generateUrl('edition_actiongenerique', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'form'        => $editForm->createView()
        );
    }
   
    /**
     * @Route("/{id}/supprimer_actiongenerique", name="supprimer_actiongenerique")
     * @Method({"POST","GET"})
     * @Template()
     */
    public function deleteAction(Request $request, $id)
    {
    	$em = $this->getDoctrine()->getManager();
    	/** @var ActionGenerique $entity */
    	$entity = $em->getRepository('OrangeMainBundle:ActionGenerique')->find($id);
    	
    	if (!$entity) 
    		throw $this->createNotFoundException('L\'action générique n\'existe pas.');
    	
        if($request->getMethod()=="POST"){
        		$conn = $this->get('database_connection');
        		$query  = sprintf("delete  from action_generique_has_action where action_id=%s;",$id);
        		$query .= sprintf("delete  from action_generique_has_statut where action_id=%s;",$id);
        		$exec = $conn->prepare($query)->execute();
	            $em->remove($entity);
	            $em->flush();
	            $this->get('session')->getFlashBag()->add('success', array('title' => 'Notification', 'body' =>  'Action générique supprimée avec succés!'));
	            return new JsonResponse(array('url' => $this->generateUrl('les_actiongeneriques')));
        }
        return array('id'=>$id);
    }
    
    /**
     * @Route("/{id}/actions_by_actiongenerique", name="actions_by_actiongenerique")
     * @Route("/{id}/{statut}/actions_by_actiongenerique_statut", name="actions_by_actiongenerique_statut")
     * @Method({"GET","POST"})
     * @Template()
     */
    public function actionByGeneriqueAction(Request $request, $id,$statut=null)
    {
    	$em = $this->getDoctrine()->getManager();
    	$criteria = new ActionCriteria();
    	$entity = $em->getRepository('OrangeMainBundle:ActionGenerique')->find($id);
    	$entityStatut = null;
    	$form = $this->createForm($criteria);
    	
    	if($statut!=null)
    		$entityStatut = $em->getRepository('OrangeMainBundle:Statut')->findOneByCode($statut);
    	
    	if($request->getMethod()=="POST"){
    		$this->modifyRequestForForm($request, array(), $form);
    		$this->get('session')->set('actiongenerique_criteria', $request->request->get($form->getName()));
    	}
    	return array('form'=>$form->createView(), 'entity'=>$entity, 'statut'=>$entityStatut);
    }
    
    /**
     * @Route("/{id}/listeaction_by_actiongenerique", name="listeaction_by_actiongenerique")
     * @Route("/{id}/{statut}/listeaction_by_actiongenerique_statut", name="listeaction_by_actiongenerique_statut") 
     */
    public function listeByGeneriqueAction(Request $request, $id,$statut=null){
    	
    	if($request->isXmlHttpRequest()==false)
    		throw new AccessDeniedException("Accés non autorisé");
    	
    	$em = $this->getDoctrine()->getManager();
    	$criteria = null;
    	$form = $this->createForm(new ActionCriteria());
    	$this->modifyRequestForForm($request, $this->get('session')->get('actiongenerique_criteria'), $form);
    	$criteria=$form->getData();
    	if($statut!=null){
    		$entityStatut = $em->getRepository('OrangeMainBundle:Statut')->findOneByCode($statut);
    		$criteria->statut = $entityStatut;
    	}
    	
    	$queryBuilder = $em->getRepository('OrangeMainBundle:ActionGenerique')->getActionByActionGenerique($criteria,$id);
    	return $this->paginate($request, $queryBuilder, 'addRowInTableForSimpleAction');
    }
    
    /**
     * @Route("/{data}/orienter_action", name="orienter_action")
     * @Method({"GET","POST"})
     * @Template()
     */
    public function orientationAction(Request $request, $data){
    	$em = $this->getDoctrine()->getEntityManager();
    	$ids = (strpos($data, ',')!=false) ? explode(',', $data) : $data;
    	$action = new Action();
    	$form   = $this->createCreateForm($action , 'OrientationAction', array('attr'=>array('user'=>$this->getUser(),'ids'=>$ids)));
    	if($request->getMethod()=="POST"){
   		        $form->handleRequest($request);
       		    $datas = array("ids"=>$ids, "user"=>$this->getUser(),"actiongenerique"=>$action->getActionGenerique());
   		        $this->get('orange.main.query')->orienterManyActions($datas);
   		        $this->get('session')->getFlashBag()->add('success', array('title' => 'Notification', 'body' =>  'Orientation vers l\'action générique effectuée  avec succés!'));
   		    	return new JsonResponse(array('url' => $this->generateUrl('les_actiongeneriques')));
    	}
    	return array('data'=> $data,'form'=>$form->createView());
    }

    
    /**
     * 
     * @param ActionGenerique $entity
     */
    public function addRowInTable($entity){
           return array(
           		$entity->getReference(),
           		$entity->getPorteur()->__toString(),
           		$entity->getLibelle(),
           		$entity->getActionGeneriqueHasAction()->count(),
           		$this->get('orange_main.actions')->generateActionsForActionGenerique($entity)
           		
           );	
    }
    
    /**
     *
     * @param Action $entity
     */
    public function addRowInTableForSimpleAction($entity){
    	return array(
    			'<span align="center" style="margin-left: 15px; width:20px; height:20px; background:'.($entity->getPriorite()?$entity->getPriorite()->getCouleur():'') .'">&nbsp;&nbsp;&nbsp;&nbsp;</span>',
    			$entity->getReference(),
    			$entity->getInstance()->__toString(),
    			$entity->getLibelle(),
    			$entity->getPorteur()->getPrenom().' '.$entity->getPorteur()->getNom(),
    			$this->showEntityStatus($entity, 'etat'),
    			$this->get('orange_main.actions')->generateActionsForAction($entity)
    	);
    }
}
