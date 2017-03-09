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
        $stats = $repo->getSimpleActionByActionGenerique($id)->getQuery()->getArrayResult();
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
     */
    public function deleteAction(Request $request, $id)
    {
    	$em = $this->getDoctrine()->getManager();
    	/** @var ActionGenerique $entity */
    	$entity = $em->getRepository('OrangeMainBundle:ActionGenerique')->find($id);
    	
    	if (!$entity) 
    		throw $this->createNotFoundException('L\'action générique n\'existe pas.');
    	
    	
        if($request->getMethod()=="POST"){
        	if($entity->getActionGeneriqueHasAction()->count()>0)
        		$this->get('session')->getFlashBag()->add('error', array('title' => 'Notification', 'body' =>  'Impossible de supprimer cette action générique!'));
        	else {	   
	            $em->remove($entity);
	            $em->flush();
	            $this->get('session')->getFlashBag()->add('success', array('title' => 'Notification', 'body' =>  'Action générique supprimée avec succés!'));
        	}
        	return $this->redirect($this->generateUrl('les_actiongeneriques'));
        }
        return array('id'=>$id);
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
}
