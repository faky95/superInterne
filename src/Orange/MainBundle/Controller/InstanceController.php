<?php
namespace Orange\MainBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Orange\MainBundle\Entity\Instance;
use Orange\MainBundle\Form\InstanceType;
use Orange\QuickMakingBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Orange\MainBundle\Criteria\InstanceCriteria;
use Doctrine\ORM\QueryBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Orange\QuickMakingBundle\Annotation\QMLogger;

/**
 * Instance controller.
 */
class InstanceController extends BaseController
{

    /**
     * Lists all Instance entities.
     * @QMLogger(message="Liste des instances")
     * @Route("/les_instance", name="les_instance")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function indexAction()
    {
    	$this->get('session')->set('instance_criteria', new Request());
    	return array();
    }
    
    /**
     * Creates a new Instance entity.
     * @QMLogger(message="Création d'une instance")
     * @Route("/creer_instance", name="creer_instance")
     * @Method("POST")
     * @Template("OrangeMainBundle:Instance:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Instance();
        $form = $this->createCreateForm($entity,'Instance', array('attr' => array('security_context' => $this->get('security.context'))));
        if(!$this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
        	$form->remove('bu');
        }
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            if(!$this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
            	$bu = $this->getUser()->getStructure()->getBuPrincipal();
            	$bu->addInstance($entity);
            	if($entity->getStructure()->count()==0){
            		$str=$em->find('OrangeMainBundle:Structure', $this->getUser()->getStructure()->getRoot());
            		$entity->addStructure($str);
            	}
            }
            $em->flush();
            return $this->redirect($this->generateUrl('les_instance'));
        }
        return array('entity' => $entity, 'form'   => $form->createView());
    }

    /**
     * Displays a form to create a new Instance entity.
     * @QMLogger(message="Nouvelle instance")
     * @Route("/nouvelle_instance", name="nouvelle_instance")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function newAction()
    {
        $entity = new Instance();
        $form   = $this->createCreateForm($entity, 'Instance', array('attr' => array('security_context' => $this->get('security.context'))));
        return array('entity' => $entity, 'form'   => $form->createView());
    }

    /**
     * Finds and displays a Instance entity.
     * @QMLogger(message="Visualisation d'une instance")
     * @Route("/{id}/details_instance", name="details_instance")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('OrangeMainBundle:Instance')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Instance entity.');
        }
        return array('entity' => $entity);
    }

    /**
     * Displays a form to edit an existing Instance entity.
     * @QMLogger(message="Modification d'une instance")
     * @Route("/{id}/edition_instance", name="edition_instance")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('OrangeMainBundle:Instance')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Instance entity.');
        }
        $editForm = $this->createEditForm($entity);
        return array('entity' => $entity, 'edit_form' => $editForm->createView());
    }

    /**
    * Creates a form to edit a Instance entity.
    * @param Instance $entity The entity
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Instance $entity)
    {
        $form = $this->createForm(new InstanceType(), $entity, array(
	            'action' => $this->generateUrl('modifier_instance', array('id' => $entity->getId())),
	            'method' => 'PUT',
	        ));
        $form->add('submit', 'submit', array('label' => 'Update'));
        return $form;
    }
    
    
    /**
     * Edits an existing Instance entity.
     * @Route("/{id}/modifier_instance", name="modifier_instance")
     * @Method("POST")
     * @Template("OrangeMainBundle:Instance:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('OrangeMainBundle:Instance')->find($id);
        $form = $this->createCreateForm($entity,'Instance');
        $request = $this->get('request');
        if ($request->getMethod() == 'POST') {
        	$form->handleRequest($request);
        	if ($form->isValid()) {
        		if($entity->getStructure()->count()==0){
        			$str=$em->find('OrangeMainBundle:Structure', $this->getUser()->getStructure()->getRoot());
        			$entity->addStructure($str);
        		}
        		$em->persist($entity);
        		$em->flush();
        		$this->get('session')->getFlashBag()->add('success', array (
        				'title' =>'Notification',
        				'body' => 'La modification s\'est déroulée avec succés.'
        		));
        		return $this->redirect($this->generateUrl('details_instance', array('id'=>$id)));
        	}
        }
        return array('entity' => $entity, 'edit_form' => $form->createView());
    }

    /**
     * Creates a form to delete a Instance entity by id.
     * @param mixed $id The entity id
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('supprimer_instance', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();
    }
    
    /**
     * Deletes a Instance entity.
     * @QMLogger(message="Suppression d'une instance")
     * @Route("/{id}/supprimer_instance", name="supprimer_instance")
     * @Method("GET")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function deleteAction(Request $request, $id)
    {
    	$em = $this->getDoctrine()->getManager();
    	$entity = $em->getRepository('OrangeMainBundle:Instance')->find($id);
    	if ($entity==null) {
    		$this->addFlash('success', array('title' =>'Notification', 'body' => 'Instance inexistante ! '));
    	}
    	$les_fils=$em->getRepository('OrangeMainBundle:Instance')->findBy(array('parent'=>$entity));
    	if($entity->getAction()->count()>0) {
    		$this->addFlash('failed', array('title' =>'Notification', 'body' => 'Cette instance a des actions ! '));
    	}
    	if($les_fils) {
    		$this->addFlash('failed', array('title' =>'Notification', 'body' => 'Cette instance a des sous instances ! '));
    	}
    	if($les_fils==null && $entity->getAction()->count()==0) {
    		$em->remove($entity);
    		$em->flush();
    		$this->addFlash('success', array('title' =>'Notification', 'body' => 'Cette instance a ete supprime avec succes ! '));
    	}
    	return $this->redirect($this->generateUrl('les_instance'));
    }
    
    /**
     * Lists  entities.
     * @Route("/liste_des_instances", name="liste_des_instances")
     * @Method("GET")
     * @Template()
     */
    public function listAction(Request $request) {
    	$em = $this->getDoctrine()->getManager();
    	$form = $this->createForm(new InstanceCriteria());
    	$this->modifyRequestForForm($request, $this->get('session')->get('instance_criteria'), $form);
    	$queryBuilder = $em->getRepository('OrangeMainBundle:Instance')->listAllElements();
    	$this->get('session')->set('data', array('query' => $queryBuilder->getDql(), 'param' =>$queryBuilder->getParameters()));
    	return $this->paginate($request, $queryBuilder);
    }
    
    /**
     * @todo retourne le nombre d'enregistrements renvoyer par le résultat de la requête
     * @param QueryBuilder $queryBuilder
     * @return integer
     */
    protected function getLengthResults(QueryBuilder $queryBuilder, $rootColumnName) {
    	$data = $queryBuilder->select(sprintf('COUNT(DISTINCT %s.%s) as number', $queryBuilder->getRootAlias(), $rootColumnName))
    		->resetDQLPart('groupBy')
	    	->getQuery()->execute();
    	return $data[0]['number'];
    }

    /**
     * @Route("/filtrer_les_instances", name="filtrer_les_instances")
     * @Template()
     */
    public function filterAction(Request $request) {
    	$form = $this->createForm(new InstanceCriteria());
    	if($request->getMethod()=='POST') {
    		$this->get('session')->set('instance_criteria', $request->request->get($form->getName()));
    		return new JsonResponse();
    	} else {
    		$this->modifyRequestForForm($request, $this->get('session')->get('instance_criteria'), $form);
    		return array('form' => $form->createView());
    	}
    }
    
    /**
     * @QMLogger(message="Extraction instance")
     * @Route("/export_instance", name="export_instance")
     * @Template()
     */
    public function exportAction() {
    	$queryBuilder = $this->getDoctrine()->getRepository('OrangeMainBundle:Instance')->listAllElements(true);
    	$query = $queryBuilder->getQuery();
    	$query->setHint(\Doctrine\ORM\Query::HINT_FORCE_PARTIAL_LOAD, 1);
    	$data = $this->getMapping()->getExtraction()->exportInstance($query->execute());
	  	$objWriter = $this->get('orange.main.extraction')->exportInstance($data);
    	$filename = sprintf("Extraction_des_instances_du_%s.xlsx", date('d-m-Y'));
    	$objWriter->save($this->get('kernel')->getWebDir()."/upload/reporting/$filename");
    	return $this->redirect($this->getUploadDir().$filename);
    }
    
    /*
     * reperetoire de sauvegarde des reporting
     */
    private function getUploadDir() {
    	return $this->getRequest()->getBaseUrl().($this->get('kernel')
    			->getEnvironment()=='prod' ? '' : '/..')."/upload/reporting/";
    }
    
    /**
     * @todo retourne le nombre d'enregistrements renvoyer par le rÃ©sultat de la requete
     * @param \Orange\MainBundle\Entity\Instance $entity
     * @return array
     */
    protected function addRowInTable($entity) {
    	return array(
    			$entity[0]->getLibelle(),
    			$entity[0]->getDescription(),
    			$this->get('orange_main.actions')->generateActionsForInstance($entity[0], $entity['number'])
    		);
    }
    
    /**
     * @todo ajoute un filtre
     * @param sfWebRequest $request
     */
    protected function setFilter(QueryBuilder $queryBuilder, $aColumns, Request $request) {
    	parent::setFilter($queryBuilder, array('i.libelle'), $request);
    }
    
}
