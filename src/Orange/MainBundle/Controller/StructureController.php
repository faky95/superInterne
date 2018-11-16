<?php

namespace Orange\MainBundle\Controller;

use Doctrine\ORM\QueryBuilder;
use Orange\MainBundle\Criteria\StructureCriteria;
use Orange\MainBundle\Entity\Structure;
use Orange\MainBundle\Form\LoadingType;
use Orange\MainBundle\Form\StructureType;
use Orange\QuickMakingBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Orange\QuickMakingBundle\Annotation\QMLogger;

/**
 * Structure controller.
 */
class StructureController extends BaseController
{

    /**
     * Lists all Structure entities.
     * @QMLogger(message="Liste des structures")
     * @Route("/les_structures", name="les_structures")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function indexAction()
    {
    	$this->get('session')->set('structure_criteria', new Request());
        return array();
    }
    
    
    /**
     * Lists  entities.
     *
     *@Route("/liste_des_structures", name="liste_des_structures")
     * @Method("GET")
     * @Template()
     */
		public function listeAction(Request $request) {
             $em = $this->getDoctrine()->getManager();
             $form = $this->createForm(new StructureCriteria());
             $this->modifyRequestForForm($request, $this->get('session')->get('structure_criteria'), $form);
             $criteria = $form->getData();
             $queryBuilder = $em->getRepository('OrangeMainBundle:Structure')->listAllElements($criteria);
             return $this->paginate($request, $queryBuilder);
       }
       
       /**
        * @Route("/filtrer_structures", name="filtrer_structures")
        * @Template()
        */
       
       public function filtreAction(Request $request) {
       	$form = $this->createForm(new StructureCriteria());
       	if($request->getMethod()=='POST') {
       		$this->get('session')->set('structure_criteria', $request->request->get($form->getName()));
       		return new JsonResponse();
       	} else {
       		$this->modifyRequestForForm($request, $this->get('session')->get('structure_criteria'), $form);
       		return array('form' => $form->createView());
       
       	}
       
       }

    /**
     * Displays a form to create a new Structure entity.
     * @QMLogger(message="Nouvelle action")
     * @Route("/nouvelle_structure/{bu_id}", name="nouvelle_structure", requirements={"bu_id" = "\d+"}, defaults={"bu_id" = null})
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function newAction($bu_id)
    {
        $entity = new Structure();
        if($this->getUser()->hasRole('ROLE_SUPER_ADMIN')==false) {
        	$entity->setBuPrincipal($this->getUser()->getStructure()->getBuPrincipal());
        }
        $form   = $this->createCreateForm( $entity, 'Structure');
        return array('entity' => $entity, 'form'   => $form->createView(), 'bu_id'	 => $bu_id);
    }
    
    /**
     * Creates a new Structure entity.
     * @QMLogger(message="Création de structure")
     * @Route("/creer_structure/{bu_id}", name="creer_structure", requirements={"bu_id" = "\d+"}, defaults={"bu_id" = null})
     * @Method("POST")
     * @Template("OrangeMainBundle:Structure:new.html.twig")
     */
    public function createAction(Request $request, $bu_id)
    {
    	$em = $this->getDoctrine()->getManager();
    	$entity = new Structure();
    	$form = $this->createCreateForm($entity, 'Structure');
    	$form->handleRequest($request);
    	if($this->getRequest()->isMethod("POST")) {
    		if ($form->isValid()) {
    			$em = $this->getDoctrine()->getManager();
    			$entity->updateLibelle($entity);
    			$em->persist($entity);
    			$em->flush();
				$route =  $this->generateUrl('les_structures');
				if($form->get('save_and_add')->isClicked()) {
					$route =  $this->generateUrl('nouvelle_structure', array('bu_id' => $bu_id));
				}
    			return $this->redirect($route);
    		}
    	}
    	return array('entity' => $entity, 'form' => $form->createView(), 'bu_id' => $bu_id);
    }

    /**
     * Finds and displays a Structure entity.
     * @QMLogger(message="Visualisation de structure")
     * @Route("/details_structure/{id}", name="details_structure")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('OrangeMainBundle:Structure')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Structure entity.');
        }
        $deleteForm = $this->createDeleteForm($id);
        return array('entity'      => $entity, 'delete_form' => $deleteForm->createView());
    }

    /**
     * Displays a form to edit an existing Structure entity.
     * @QMLogger(message="Modification de structure")
     * @Route("/{id}/edition_structure", name="edition_structure", requirements={ "id"=  "\d+"})
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('OrangeMainBundle:Structure')->find($id);
        $form = $this->createCreateForm($entity, 'Structure');
        $request = $this->get('request');
        if ($request->getMethod() == 'POST') {
        	$form->handleRequest($request);
        	if ($form->isValid()) {
        		$em->persist($entity);
        		$em->flush();
        		$this->get('orange.main.updateStructure')->setStructureForAction();
        		return $this->redirect($this->generateUrl('details_structure', array('id'=>$id)));
        	}
        }
        return array('entity' => $entity, 'edit_form' => $form->createView());
    }

    /**
    * Creates a form to edit a Structure entity.
    *
    * @param Structure $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Structure $entity)
    {
        $form = $this->createForm(new StructureType(), $entity, array(
            'action' => $this->generateUrl('modifier_structure', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Structure entity.
     * @Route("/{id}/modifier_structure", name="modifier_structure", requirements={ "id"=  "\d+"})
     * @Method("POST")
     * @Template("OrangeMainBundle:Structure:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
    	$bu = $this->getUser()->getStructure()->getBuPrincipal()->getId();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('OrangeMainBundle:Structure')->find($id);
        $form = $this->createCreateForm($entity,'Structure', array('attr'=>array( 'bu_id'=>$bu)));
        $request = $this->get('request');
        if ($request->getMethod() == 'POST') {
        	$form->handleRequest($request);
        	if ($form->isValid()) {
        		foreach($em->getRepository('OrangeMainBundle:Structure')->findChildren($entity) as $structure) {
        			$structure->updateLibelle($structure);
        			$entitym->persist($structure);
        		}
        		$em->persist($entity);
        		$em->flush();
        		return $this->redirect($this->generateUrl('les_structures'));
        	}
        }
        return array('entity' => $entity, 'edit_form' => $form->createView());
      
    }
    
    /**
     *  Deletes a Structure entity.
     *  @QMLogger(message="Suppression de structure")
     *  @Route("/{id}/supprimer_structure", name="supprimer_structure", requirements={ "id"=  "\d+"})
     *  @Security("has_role('ROLE_ADMIN')")
     */
    
    public function deleteAction($id) {
    	$em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('OrangeMainBundle:Structure')->find($id);
    	//if($this->getRequest()->getMethod() =='POST') {
    		if($entity->isDeletable()) {
    			$em->remove($entity);
    			$em->flush();
    			$this->container->get('session')->getFlashBag()->add('success', array (
	          		    		'title' =>'Notification',
	          		    		'body' => 'La structure a été supprimée avec succes !'
	          		    ));
    		} else {
    			$this->container->get('session')->getFlashBag()->add('error', array (
	    					'title' =>'Notification',
	    					'body' => 'Cette structure ne peut pas être supprimée!'
	    					));
    		}
//     	}
    	return $this->redirect($this->generateUrl('les_structures'));
    }

    /**
     * Creates a form to delete a Structure entity by id.
     * @param mixed $id The entity id
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('supprimer_structure', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
    
    /**
    * @Route("/filtrer_les_structures", name="filtrer_les_structures")
    * @Template()
    */
    
    public function filterAction(Request $request) {
    	$form = $this->createForm(new StructureCriteria());
    	if($request->getMethod()=='POST') {
    		$this->get('session')->set('structure_criteria', $request->request->get($form->getName()));
    		return new JsonResponse();
    	} else {
    		$this->modifyRequestForForm($request, $this->get('session')->get('structure_criteria'), $form);
    		return array('form' => $form->createView());
    
    	}
    
    }
    
    /**
    * @todo retourne le nombre d'enregistrements renvoyer par le résultat de la requête
    * @param \Orange\MainBundle\Entity\Structure $entity
    * @return array
    */
    
    protected function addRowInTable($entity) {
    	return array(
    			$entity->getLibelle(),
    			$entity->getParent()?$entity->getParent()->__toString():null,
    			$entity->getTypeStructure()?$entity->getTypeStructure()->__toString():null,
    			$this->get('orange_main.actions')->generateActionsForStructure($entity)
    	);
    }
	
    /**
     * @todo ajoute un filtre
     * @param sfWebRequest $request
     */
    protected function setFilter(QueryBuilder $queryBuilder, $aColumns, Request $request) {
    	parent::setFilter($queryBuilder, array('s.libelle'), $request);
    }
    
    
    
    /**
     * @Route("/chargement_structure", name="chargement_structure")
     * @Template()
     */
    public function loadingAction() {
    	$form = $this->createForm(new LoadingType());
    	return array('form' => $form->createView());
    }
    
    /**
     * @Route("/importer_structure", name="importer_structure")
     * @Method("POST")
     * @Template()
     */
    public function importAction(Request $request) {
    	$buP=$this->getUser()->getStructure()->getBuPrincipal()->getId();
    	$form = $this->createForm(new LoadingType());
    	$form->handleRequest($request);
    	if($form->isValid()) {
    		$data = $form->getData();
    		try {
    			$number = $this->get('orange.main.loader')->loadStructure($data['file'], $buP);
    			$this->get('session')->getFlashBag()->add('success', "Le chargement s'est effectué avec succès! Nombre de structures chargées: $number");
    			return $this->redirect($this->generateUrl('les_structures'));
    		} catch(\Exception $e) {
    		$this->get('session')->getFlashBag()->add('error', $e->getMessage());
    		}
    	}
    	return $this->render('OrangeMainBundle:Structure:loading.html.twig', array('form' => $form->createView()));
    }
}
