<?php
namespace Orange\MainBundle\Controller;

use Doctrine\DBAL\DBALException;
use Orange\MainBundle\Criteria\ActionCriteria;
use Orange\MainBundle\Criteria\ActionGeneriqueCriteria;
use Orange\MainBundle\Entity\Action;
use Orange\MainBundle\Entity\ActionGenerique;
use Orange\MainBundle\Entity\Statut;
use Orange\MainBundle\OrangeMainEvents;
use Orange\MainBundle\Utils\ActionUtils;
use Orange\QuickMakingBundle\Annotation\QMLogger;
use Orange\QuickMakingBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * ActionGenerique controller
 */
class ActionGeneriqueController extends BaseController
{
	/**
	 * @QMLogger(message="Liste des actions génériques")
	 * @Route("/les_actiongeneriques", name="les_actiongeneriques")
	 * @Method({"GET","POST"})
	 * @Template()
	 */
	public function indexAction(Request $request)
	{
		$this->get('session')->set('actiongenerique_criteria', array());
		$this->denyAccessUnlessGranted('liste', new ActionGenerique(), 'accés non autorisé!');
		$form = $this->createForm(new ActionGeneriqueCriteria());
		if($request->getMethod()=="POST"){
		       $this->modifyRequestForForm($request, array(), $form);
		       $this->get('session')->set('actiongenerique_criteria', $request->request->get($form->getName()));
		}
		return array('form'=>$form->createView());
	}
	
	/**
	 * @QMLogger(message="Chargement Ajax des actions génériques")
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
	 * @QMLogger(message="Afficher formulaire de creation d'une action générique")
	 * @Route("/nouvelle_actiongenerique", name="nouvelle_actiongenerique")
	 * @Method("GET")
	 * @Template()
	 */
	public function newAction()
	{
		$entity = new ActionGenerique();
		$form   = $this->createCreateForm($entity,'ActionGenerique');
		$this->denyAccessUnlessGranted('create', $entity, 'accés non autorisé!');
		if(!$this->getUser()->hasRole("ROLE_ADMIN")){
			$form->remove('porteur');
			$entity->setPorteur($this->getUser());
		}
		return array('entity' => $entity, 'form'   => $form->createView());
	}
	
	/**
	 * @QMLogger(message="Envoi des données du formulaire de creation d'une action générique")
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
		if(!$this->getUser()->hasRole("ROLE_ADMIN")){
			$form->remove('porteur');
			$entity->setPorteur($this->getUser());
		}
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
		return array('entity' => $entity, 'form' => $form->createView());
	}
   


    /**
     * @QMLogger(message="Afficher détails d'une action générique")
     * @Route("/details_actiongenerique/{id}", name="details_actiongenerique")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $repo= $em->getRepository('OrangeMainBundle:ActionGenerique');
        $entity = $repo->find($id);
        $this->denyAccessUnlessGranted('read', $entity, 'accés non autorisé!');
        $stats = $repo->getStatsSimpleActionByActionGenerique($id)->getQuery()->getArrayResult();
        $map = $this->getMapping()->getReporting()->mapToHaveLibelle($stats);
        if (!$entity) {
            throw $this->createNotFoundException('L\'action générique n\'existe pas.');
        }
        return array('action' => $entity, 'stats' => $map);
    }

    /**
     * Displays a form to edit an existing ActionGenerique entity.
     * @QMLogger(message="Affichage du formulaire de modification d'une action générique")
     * @Route("/{id}/edition_actiongenerique", name="edition_actiongenerique")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('OrangeMainBundle:ActionGenerique')->find($id);
        $this->denyAccessUnlessGranted('update', $entity, 'accés non autorisé!');
        if (!$entity) {
        	throw $this->createNotFoundException('L\'action générique n\'existe pas.');
        }
        $editForm = $this->createCreateForm($entity,'ActionGenerique');
        if(!$this->getUser()->hasRole("ROLE_ADMIN")) {
        	$editForm->remove('porteur');
        }
        return array('entity' => $entity, 'form' => $editForm->createView());
    }
   
    /**
     * @QMLogger(message="Envoi des données du formulaire de modification d'une action générique")
     * @Route("/{id}/modifier_actiongenerique", name="modifier_actiongenerique")
     * @Method("POST")
     * @Template("OrangeMainBundle:ActionGenerique:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('OrangeMainBundle:ActionGenerique')->find($id);
        if (!$entity) {
        	throw $this->createNotFoundException('L\'action générique n\'existe pas.');
        }
        $editForm = $this->createCreateForm($entity,'ActionGenerique');
        $this->removeFormFields($editForm, array('statut'));
        if(!$this->getUser()->hasRole("ROLE_ADMIN")) {
        	$this->removeFormFields($editForm, array('porteur'));
        }
        $editForm->handleRequest($request);
        if($editForm->isValid()) {
        	$em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', array('title' => 'Notification', 'body' =>  'Action générique modifiée avec succés!'));
            return $this->redirect($this->generateUrl('les_actiongeneriques'));
        }
        return array('entity' => $entity, 'form' => $editForm->createView());
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
    	$this->denyAccessUnlessGranted('delete', $entity, 'accés non autorisé!');
    	
    	if(!$entity) {
    		throw $this->createNotFoundException('L\'action générique n\'existe pas.');
    	}
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
    	$this->denyAccessUnlessGranted('liste', $entity, 'accés non autorisé!');
    	$entityStatut = null;
    	$form = $this->createForm($criteria);
    	if($statut!=null) {
    		$entityStatut = $em->getRepository('OrangeMainBundle:Statut')->findOneByCode($statut);
    	}
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
    	
    	if($statut!=null){
    		$criteria = new Action();
    		$entityStatut = $em->getRepository('OrangeMainBundle:Statut')->findOneBy(array('code'=>$statut));
    		$criteria->statut = $entityStatut;
    	}
    	$this->modifyRequestForForm($request, $this->get('session')->get('actiongenerique_criteria'), $form);
    	$criteria=$form->getData();
    	
    	$queryBuilder = $em->getRepository('OrangeMainBundle:ActionGenerique')->getActionByActionGenerique($criteria,$id);
    	return $this->paginate($request, $queryBuilder, 'addRowInTableForSimpleAction');
    }
    
    /**
     * @Route("/{data}/orienter_action", name="orienter_action")
     * @Method({"GET","POST"})
     * @Template()
     */
    public function orientationAction(Request $request, $data){
    	$em     = $this->getDoctrine()->getEntityManager();
    	$ids    = (strpos($data, ',')!=false) ? explode(',', $data) : $data;
    	$action = new Action();
    	$form   = $this->createCreateForm($action , 'OrientationAction', array('attr'=>array('user'=>$this->getUser(),'ids'=>$ids)));
    	if($request->getMethod() == "POST"){
    		$form->handleRequest($request);
    		try {
    			$datas = array("ids"=>$ids, "user"=>$this->getUser(),"actiongenerique"=>$action->getActionGenerique());
    			$this->get('orange.main.query')->orienterManyActions($datas);
    			$this->get('session')->getFlashBag()->add('success', array('title' => 'Notification', 'body' =>  'Orientation vers l\'action générique effectuée  avec succés!'));
    			return new JsonResponse(array('url' => $this->generateUrl('les_actiongeneriques')));
    		} catch (DBALException $e) { 
    			$this->get('session')->getFlashBag()->add('error', array ('title' => 'Message d\'erreur', 'body' => nl2br($e->getMessage())));
    			return new JsonResponse(array('url' => $this->generateUrl('mes_actions')));
    		}
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
