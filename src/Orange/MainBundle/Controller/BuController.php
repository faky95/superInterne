<?php

namespace Orange\MainBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Orange\MainBundle\Entity\Bu;
use Orange\MainBundle\Form\BuType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Orange\MainBundle\Criteria\BuCriteria;
use Orange\QuickMakingBundle\Controller\BaseController;
use Doctrine\ORM\QueryBuilder;
use Orange\MainBundle\Entity\Utilisateur;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Orange\QuickMakingBundle\Annotation\QMLogger;
/**
 * Bu controller.
 *
 */
class BuController extends BaseController
{

    /**
     * Lists all Bu entities.
     * @QMLogger(message="Liste des BU")
     * @Route("/les_bu", name="les_bu")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function indexAction()
    {
    	$this->get('session')->set('bu_criteria', new Request());
    	return array();
    }
    
   
    /**
     * Lists all Bu entities.
     *
     * @Method("GET")
     * @Template()
     */
    public function listeAction()
    {
    	$em = $this->getDoctrine()->getManager();
    	$entities = $em->getRepository('OrangeMainBundle:Bu')->findAll();
    	return array(
    			'entities' => $entities,
    	);
    }
    
    /**
     * Creates a new Bu entity.
     * @QMLogger(message="Création Bu")
     * @Route("/creer_bu", name="creer_bu")
     * @Template("OrangeMainBundle:Bu:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Bu();
        $form = $this->createCreateForm($entity,'Bu');
        $form->handleRequest($request);
        if($form->isValid()) {
	    	$em = $this->getDoctrine()->getManager();
	        $em->persist($entity);
	        $em->flush();
	        return new JsonResponse(array('url' => $this->generateUrl('nouvelle_structure', array('bu_id' => $entity->getId()))));
       	}
       	return $this->render('OrangeMainBundle:Bu:new.html.twig', array(
        					'entity' => $entity,
        					'form'   => $form->createView(),
        			), new \Symfony\Component\HttpFoundation\Response(null,303));
    }

    /**
     * Displays a form to create a new Bu entity.
     * @QMLogger(message="Nouvelle BU")
     * @Route("/nouveau_bu", name="nouveau_bu")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function newAction()
    {
        $entity = new Bu();
        $form   = $this->createCreateForm($entity,'Bu');
        return array('entity' => $entity, 'form'   => $form->createView());
    }

    /**
     * Finds and displays a Bu entity.
     * @QMLogger(message="Visuslaisation de BU")
     * @Route("/{id}/details_bu", name="details_bu")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('OrangeMainBundle:Bu')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Bu entity.');
        }
        $deleteForm = $this->createDeleteForm($id);
        return array('entity'      => $entity, 'delete_form' => $deleteForm->createView());
    }

    /**
     * Displays a form to edit an existing Bu entity.
     * @QMLogger(message="Modification de BU")
     * @Route("/{id}/edition_bu", name="edition_bu")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('OrangeMainBundle:Bu')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Bu entity.');
        }
        $editForm = $this->createEditForm($entity);
        return array('entity'      => $entity, 'edit_form'   => $editForm->createView());
    }

    /**
    * Creates a form to edit a Bu entity.
    * @param Bu $entity The entity
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Bu $entity)
    {
        $form = $this->createForm(new BuType(), $entity, array(
            'action' => $this->generateUrl('modifier_bu', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));
        $form->add('submit', 'submit', array('label' => 'Update'));
        return $form;
    }
    /**
     * Edits an existing Bu entity.
     *
     * @Route("/{id}/modifier_bu", name="modifier_bu")
     * @Method("POST")
     * @Template("OrangeMainBundle:Bu:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('OrangeMainBundle:Bu')->find($id);
        $form = $this->createCreateForm($entity,'Bu');
        $request = $this->get('request');
        if ($request->getMethod() == 'POST') {
        	$form->handleRequest($request);
        	if ($form->isValid()) {
        		$em->persist($entity);
        		$em->flush();
        		return $this->redirect($this->generateUrl('details_bu', array('id'=>$id)));
        	}
        }
        return array('entity' => $entity, 'edit_form' => $form->createView());
    }
    /**
     * Deletes a Bu entity.
     * @QMLogger(message="Suppression de BU")
     * @Route("/{id}/supprimer_bu", name="supprimer_bu")
     * @Method("GET")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function deleteAction(Request $request, $id)
    {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('OrangeMainBundle:Bu')->find($id);
            if ($entity) {
            	if($entity->getStructureBuPrincipal()->count()>0 ){
            		$this->container->get('session')->getFlashBag()->add('failed', array (
            				'title' =>'Notification', 'body' => 'Cette bu a des structures ! '
            			 ));
            	} else {
            		$this->container->get('session')->getFlashBag()->add('sucess', array (
            				'title' =>'Notification', 'body' => 'Cette bu a ete supprime avec succes ! '
            			));
            		$em->remove($entity);
            		$em->flush();
            	}            		
            } else {
                throw $this->createNotFoundException('Unable to find Bu entity.');
            }
        return $this->redirect($this->generateUrl('les_bu'));
    }

    /**
     * Creates a form to delete a Bu entity by id.
     * @param mixed $id The entity id
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('supprimer_bu', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();
    }
    
    /**
     * Lists  entities.
     *
     *@Route("/liste_des_bus", name="liste_des_bus")
     * @Method("GET")
     * @Template()
     */
    public function listAction(Request $request) {
    	$em = $this->getDoctrine()->getManager();
    	$form = $this->createForm(new BuCriteria());
    	$this->modifyRequestForForm($request, $this->get('session')->get('bu_criteria'), $form);
    	$queryBuilder = $em->getRepository('OrangeMainBundle:Bu')->listQueryBuilder();
    	return $this->paginate($request, $queryBuilder);
    }
    
    
    /**
     * @Route("/filtrer_les_domaines", name="filtrer_les_domaines")
     * @Template()
     */
    
    public function filterAction(Request $request) {
    	$form = $this->createForm(new BuCriteria());
    	if($request->getMethod()=='POST') {
    		$this->get('session')->set('bu_criteria', $request->request->get($form->getName()));
    		return new JsonResponse();
    	} else {
    		$this->modifyRequestForForm($request, $this->get('session')->get('bu_criteria'), $form);
    		return array('form' => $form->createView());
    
    	}
    
    }
    
    /**
     * @todo retourne le nombre d'enregistrements renvoyer par le résultat de la requête
     * @param \Orange\MainBundle\Entity\Bu $entity
     * @return array
     */
    protected function addRowInTable($entity) {
    	return array(
    			$entity->getLibelle(),
    			$entity->getValidationAutomatique()?'OUI':'NON',
    			$entity->getDemandeReport()?'OUI':'NON',
    			$this->get('orange_main.actions')->generateActionsForBu($entity)
    	);
    }
    
    /**
     * @todo ajoute un filtre
     * @param sfWebRequest $request
     */
    protected function setFilter(QueryBuilder $queryBuilder, $aColumns, Request $request) {
    	parent::setFilter($queryBuilder, array('b.libelle'), $request);
    }
}
