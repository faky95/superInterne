<?php
namespace Orange\MainBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Orange\MainBundle\Entity\TypeAction;
use Orange\MainBundle\Form\TypeActionType;
use Orange\QuickMakingBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Orange\MainBundle\Criteria\TypeActionCriteria;
use Doctrine\ORM\QueryBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Orange\QuickMakingBundle\Annotation\QMLogger;

/**
 * TypeAction controller.
 * @Route("/")
 */
class TypeActionController extends BaseController {

    /**
     * Lists all TypeAction entities.
     * @QMLogger(message="Liste des types")
     * @Route("/les_types_action", name="les_types_action")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function indexAction($espace_id=null) {
    	$this->get('session')->set('type_action_criteria', new Request());
    	
    	return array('espace_id'=>$espace_id);
    }
    
    /**
     * @Route("/{espace_id}/les_types_action_by_espace", name="les_types_action_by_espace")
     * @Method("GET")
     * @Template("OrangeMainBundle:TypeAction:index.html.twig")
     */
    public function typeEspaceAction($espace_id) {
    	$em = $this->getDoctrine()->getManager();
    	if ($espace_id){
    		$entity = $em->getRepository('OrangeMainBundle:Espace')->find($espace_id);
    		$user = $em->getRepository('OrangeMainBundle:Utilisateur')->find($this->getUser()->getId());
    		$membre=$em->getRepository('OrangeMainBundle:MembreEspace')->findOneBy(
    				array('utilisateur' => $user, 'espace' => $entity));
    		$actions = $this->getDoctrine()->getRepository('OrangeMainBundle:Action')->allActionEspace($espace_id);
    		$act = $this->getDoctrine()->getRepository('OrangeMainBundle:Action')->listActionsUserByEspace($this->getUser()->getId(), $espace_id);
    		$gestionnaire = $membre->getIsGestionnaire();
    	}
    	$this->get('session')->set('type_action_criteria', new Request());
    	$espace=$this->getDoctrine()->getRepository('OrangeMainBundle:Espace')->find($espace_id);
    	return array('espace_id'=>$espace_id, 'espace'=>$espace, 'gest' => $gestionnaire, 'nbrTotal' => count($actions), 'nbr' => count($act));
    }
    
   
    /**
     * Creates a new TypeAction entity.
     * @QMLogger(message="Création des types")
     * @Route("/creer_type_action", name="creer_type_action")
     * @Route("/{espace_id}/creer_type_action_to_espace", name="creer_type_action_to_espace")
     * @Method({"GET", "POST"})
     * @Template("OrangeMainBundle:TypeAction:new.html.twig")
     */
    public function createAction(Request $request, $espace_id=null) {
        $entity = new TypeAction();
        if($espace_id!=null) {
        	$espace = $this->getDoctrine()->getRepository('OrangeMainBundle:Espace')->find($espace_id);
        }
        $form = $this->createCreateForm($entity,'TypeAction');
        if(!$this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
        	$form->remove('bu');
        }
        $form->handleRequest($request);
        if($request->getMethod() === 'POST') {
	        if($form->isValid()) {
	            $em = $this->getDoctrine()->getManager();
	            $em->persist($entity);
	            if($espace_id===null) {
		            if(!$this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
		            	$bu = $this->getUser()->getStructure()->getBuPrincipal();
		            	$bu->addTypeAction($entity);
		            }
		            
	            } else {
	            	$espace->getInstance()->addTypeAction($entity);
	            	$em->persist($espace->getInstance());
	            }
	            $em->flush();
	            $this->get('session')->getFlashBag()->add('success', array (
	            		'title' =>'Notification',
	            		'body' => 'Le type a été créé avec succés.'
	            ));
	            if($espace_id!=null) {
	            	return $this->redirect($this->generateUrl('les_types_action_by_espace', array('espace_id' => $espace_id)));
	            } else {
	            	return $this->redirect($this->generateUrl('les_types_action'));
	            }
	        }
        }
        return array(
        		'entity' => $entity,
        		'form'   => $form->createView(),
        		'espace_id' =>$espace_id,
        );
    }

    /**
     * Displays a form to create a new TypeAction entity.
     * @QMLogger(message="Nouveau type")
     * @Route("/nouveau_type_action", name="nouveau_type_action")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function newAction($espace_id=null) {
        $entity = new TypeAction();
        $form   = $this->createCreateForm($entity,'TypeAction', array('attr' => array('security_context' => $this->get('security.context'))));
        return array('entity' => $entity, 'form'   => $form->createView(),'espace_id'=>$espace_id);
    }

    /**
     * Displays a form to create a new Domaine entity.
     *
     * @Route("/{espace_id}/nouveau_type_to_espace", name="nouveau_type_to_espace")
     * @Method("GET")
     * @Template("OrangeMainBundle:TypeAction:new.html.twig")
     */
    public function newTypeEspaceAction($espace_id) {
    	$entity = new TypeAction();
    	$form   = $this->createCreateForm($entity,'TypeAction');
    	return array('entity' => $entity, 'form'   => $form->createView(), 'espace_id' =>$espace_id);
    }
    
    /**
     * Finds and displays a TypeAction entity.
     * @QMLogger(message="Visualisation de type")
     * @Route("/details_type_action/{id}", name="details_type_action")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('OrangeMainBundle:TypeAction')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TypeAction entity.');
        }
        $deleteForm = $this->createDeleteForm($id);
        return array('entity' => $entity, 'delete_form' => $deleteForm->createView());
    }

    /**
     * Displays a form to edit an existing TypeAction entity.
     * @QMLogger(message="Modification type")
     * @Route("/{id}/edition_type_action", name="edition_type_action")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('OrangeMainBundle:TypeAction')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TypeAction entity.');
        }
        $editForm = $this->createEditForm($entity);
        return array('entity' => $entity, 'form' => $editForm->createView());
    }

    /**
    * Creates a form to edit a TypeAction entity.
    *
    * @param TypeAction $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(TypeAction $entity) {
        $form = $this->createForm(new TypeActionType(), $entity, array(
	            'action' => $this->generateUrl('modifier_type_action', array('id' => $entity->getId())), 'method' => 'PUT'
        	));
        $form->add('submit', 'submit', array('label' => 'Update'));
        return $form;
    }
    /**
     * Edits an existing TypeAction entity.
     *
     * @Route("/{id}/modifier_type_action", name="modifier_type_action")
     * @Method("POST")
     * @Template("OrangeMainBundle:TypeAction:edit.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('OrangeMainBundle:TypeAction')->find($id);
        $form = $this->createCreateForm($entity,'TypeAction');
        $request = $this->get('request');
        if($request->getMethod() == 'POST') {
        	$form->handleRequest($request);
        	if($form->isValid()) {
        		$em->persist($entity);
        		$em->flush();
        		$this->get('session')->getFlashBag()->add('success', array (
        				'title' =>'Notification',
        				'body' => 'La modification s\'est déroulée avec succés.'
        		));
        		return $this->redirect($this->generateUrl('les_types_action'));
        	}
        }
        return array('entity' => $entity, 'edit_form' => $form->createView());
    }
	/**
     * Deletes a TypeAction entity.
     *
     * @Route("/{id}/supprimer_type_action", name="supprimer_type_action")
     * @Method("GET")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function deleteAction(Request $request, $id) {
    	$em = $this->getDoctrine()->getManager();
    	$entity = $em->getRepository('OrangeMainBundle:TypeAction')->find($id);
     	if($entity) {
     		if($entity->getAction()->count()>0) {
            	$this->container->get('session')->getFlashBag()->add('failed', array ('title' =>'Notification', 'body' => 'Cet type d\'action est rattache a des actions ! ')); 
            } else {
            	$em->remove($entity);
            	$em->flush();
            }
        } else {
            throw $this->createNotFoundException('Unable to find TypeAction entity.');
        }
        $em->remove($entity);
        $em->flush();
        $this->get('session')->getFlashBag()->add('success', array (
        		'title' =>'Notification',
        		'body' => 'La supression s\'est déroulée avec succés.'
        ));
        return $this->redirect($this->generateUrl('les_types_action'));
    }
    

    /**
     * Creates a form to delete a TypeAction entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('les_types_action_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();
    }
    /**
     * Lists  entities.
     *
     *@Route("/liste_des_types_action", name="liste_des_types_action")
     *@Route("/{espace_id}/liste_des_types_action_by_espace", name="liste_des_types_action_by_espace")
     * @Method("GET")
     * @Template()
     */
    public function listAction(Request $request,$espace_id=null) {
    	$em = $this->getDoctrine()->getManager();
    	$form = $this->createForm(new TypeActionCriteria());
    	$this->modifyRequestForForm($request, $this->get('session')->get('type_action_criteria'), $form);
    	if($espace_id!=null) {
    		$queryBuilder = $em->getRepository('OrangeMainBundle:TypeAction')->getTypesByEspace($espace_id);
    	} else {
    		$queryBuilder = $em->getRepository('OrangeMainBundle:TypeAction')->listQueryBuilder();
    	}
    	return $this->paginate($request, $queryBuilder);
    }
    
    
    /**
     * @Route("/filtrer_les_domaines", name="filtrer_les_domaines")
     * @Template()
     */
    
    public function filterAction(Request $request) {
    	$form = $this->createForm(new TypeActionCriteria());
    	if($request->getMethod()=='POST') {
    		$this->get('session')->set('type_action_criteria', $request->request->get($form->getName()));
    		return new JsonResponse();
    	} else {
    		$this->modifyRequestForForm($request, $this->get('session')->get('type_action_criteria'), $form);
    		return array('form' => $form->createView());
    	}
    }
    
     /**
     * @todo retourne le nombre d'enregistrements renvoyer par le résultat de la requête
     * @param \Orange\MainBundle\Entity\TypeAction $entity
     * @return array
     */
    
    protected function addRowInTable($entity) {
    	return array(
    			'<span align="center" style="margin-left: 15px; width:20px; height:20px; background:'.$entity->getCouleur().'">&nbsp;&nbsp;&nbsp;&nbsp;</span>',
    			$entity->getType(),
    			$this->get('orange_main.actions')->generateActionsForTypeAction($entity)
    		);
    }
    
    /**
     * @todo ajoute un filtre
     * @param sfWebRequest $request
     */
    protected function setFilter(QueryBuilder $queryBuilder, $aColumns, Request $request) {
    	parent::setFilter($queryBuilder, array('t.type'), $request);
    }
    
}
