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

/**
 * Instance controller.
 *
 */
class InstanceController extends BaseController
{

    /**
     * Lists all Instance entities.
     *
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
     *
     * @Route("/creer_instance", name="creer_instance")
     * @Method("POST")
     * @Template("OrangeMainBundle:Instance:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Instance();
        $form = $this->createCreateForm($entity,'Instance', array(
        										'attr' => array('security_context' => $this->get('security.context'))
        ));
        if(!$this->get('security.context')->isGranted('ROLE_SUPER_ADMIN'))
        {
        	$form->remove('bu');
        }
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            if(!$this->get('security.context')->isGranted('ROLE_SUPER_ADMIN'))
            {
            	$bu = $this->getUser()->getStructure()->getBuPrincipal();
            	$bu->addInstance($entity);
            }
            $em->flush();

            return $this->redirect($this->generateUrl('les_instance'));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Instance entity.
     *
     * @Route("/nouvelle_instance", name="nouvelle_instance")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function newAction()
    {
        $entity = new Instance();
        $form   = $this->createCreateForm($entity,'Instance', array(
        										'attr' => array('security_context' => $this->get('security.context'))
        ));

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Instance entity.
     *
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

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Instance entity.
     *
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
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Instance entity.
    *
    * @param Instance $entity The entity
    *
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
     *
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
        		$em->persist($entity);
        		$em->flush();
        		return $this->redirect($this->generateUrl('details_instance', array('id'=>$id)));
        	}
        }
        
        return array('entity' => $entity, 'edit_form' => $form->createView());
    }
    
    
  /**
     * Deletes a Instance entity.
     *
     * @Route("/{id}/supprimer_instance", name="supprimer_instance")
     * @Method("GET")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function deleteAction(Request $request, $id)
    {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('OrangeMainBundle:Instance')->find($id);

            if ($entity) {
            	$les_fils=$em->getRepository('OrangeMainBundle:Instance')->findBy(array('parent'=>$entity));
            	if($entity->getAction()->count()>0){
            		$this->container->get('session')->getFlashBag()->add('failed', array (
            				'title' =>'Notification',
            				'body' => 'Cette instance a des actions ! '
            		));
            	}
            	if($les_fils){
            		$this->container->get('session')->getFlashBag()->add('failed', array (
            				'title' =>'Notification',
            				'body' => 'Cette instance a des sous instances ! '
            		));
            	}
            	if($les_fils==null && $entity->getAction()->count()==0){
            		$em->remove($entity);
            		$em->flush();
            		$this->container->get('session')->getFlashBag()->add('success', array (
            				'title' =>'Notification',
            				'body' => 'Cette instance a ete supprime avec succes ! '
            		));
            	}
            	
            }else
            	$this->container->get('session')->getFlashBag()->add('success', array (
            			'title' =>'Notification',
            			'body' => 'Instance inexistante ! '
            	));
		return $this->redirect($this->generateUrl('les_instance'));
    }
    

    /**
     * Creates a form to delete a Instance entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('supprimer_instance', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
    /**
     * Supprimer instance
     *
     * @Route("/{id}/supprimer_instance", name="supprimer_instance")
     */
    public function supprimerAction($id){
    
    	$em = $this->getDoctrine()->getManager();
    	$entity = $em->getRepository('OrangeMainBundle:Instance')->find($id);
    	if($entity) {
    		if ($entity->getIsDeleted()==false)
    		{
    			$entity->setIsDeleted(true);
    			$em->flush();
    			$this->get('session')->getFlashBag()->add('success', array (
    					'title' =>'Notification',
    					'body' => 'Instance a été supprime avec succes ! '
    			));
    
    		}else {
    			$entity->setIsDeleted(false);
    			$em->flush();
    			$this->get('session')->getFlashBag()->add('success', array (
    					'title' =>'Notification',
    					'body' => 'La suppression de cette instance à été annule avec succes ! '
    			));
    		}
    		return $this->redirect($this->generateUrl('les_instance'));
    
    	}else {
    		throw $this->createNotFoundException('Unable to find Bu entity.');
    	}
    
    }
    
    /**
     * Lists  entities.
     *
     *@Route("/liste_des_instances", name="liste_des_instances")
     * @Method("GET")
     * @Template()
     */
    public function listAction(Request $request) {
    	$em = $this->getDoctrine()->getManager();
    	$form = $this->createForm(new InstanceCriteria());
    	$this->modifyRequestForForm($request, $this->get('session')->get('instance_criteria'), $form);
    	$queryBuilder = $em->getRepository('OrangeMainBundle:Instance')->listQueryBuilder();
    	return $this->paginate($request, $queryBuilder);
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
     * @todo retourne le nombre d'enregistrements renvoyer par le résultat de la requête
     * @param \Orange\MainBundle\Entity\Instance $entity
     * @return array
     */
    
    protected function addRowInTable($entity) {
    	return array(
    			$entity->getLibelle(),
    			$entity->getDescription(),
    			$this->get('orange_main.actions')->generateActionsForInstance($entity)
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