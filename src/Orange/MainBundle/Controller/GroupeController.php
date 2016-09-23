<?php

namespace Orange\MainBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Orange\MainBundle\Entity\Groupe;
use Orange\MainBundle\Form\GroupeType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Orange\MainBundle\Criteria\GroupeCriteria;
use Orange\QuickMakingBundle\Controller\BaseController;
use Doctrine\ORM\QueryBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Orange\QuickMakingBundle\Annotation\QMLogger;
/**
 * Groupe controller.
 *
 */
class GroupeController extends BaseController
{

    /**
     * Lists all Groupe entities.
     * @QMLogger(message="Liste des groupes")
     * @Route("/les_groupe", name="les_groupe")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function indexAction()
    {
    	$this->get('session')->set('groupe_criteria', new Request());
        return array();
    }
    /**
     * Creates a new Groupe entity.
     *
     * @Route("/creer_groupe", name="creer_groupe")
     * @Method("POST")
     * @Template("OrangeMainBundle:Groupe:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Groupe();
        $em = $this->getDoctrine()->getManager();
        $struct = $em->getRepository('OrangeMainBundle:Structure')->find(array('id' =>$this->getUser()->getStructure()->getId()));
        $entity->setStructure($struct);
        $form = $this->createCreateForm($entity,'Groupe');
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('details_groupe', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Groupe entity.
     *
     * @param Groupe $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     
    private function createCreateForm($structure_id, Groupe $entity)
    {
        $form = $this->createForm(new GroupeType($structure_id), $entity, array(
            'action' => $this->generateUrl('creer_groupe'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }
*/
    /**
     * Displays a form to create a new Groupe entity.
     * @QMLogger(message="Nouveau groupe")
     * @Route("/nouveau_groupe", name="nouveau_groupe")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function newAction()
    {
        $entity = new Groupe();
        $em = $this->getDoctrine()->getManager();
        $struct = $em->getRepository('OrangeMainBundle:Structure')->find(array('id' =>$this->getUser()->getStructure()->getId()));
        $entity->setStructure($struct);
        $form   = $this->createCreateForm($entity,'Groupe');

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Groupe entity.
     * @QMLogger(message="Visualisation d'un groupe")
     * @Route("/{id}/details_groupe", name="details_groupe")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OrangeMainBundle:Groupe')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Groupe entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Groupe entity.
     * @QMLogger(message="Modification d'un groupe")
     * @Route("/{id}/edition_groupe", name="edition_groupe")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OrangeMainBundle:Groupe')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Groupe entity.');
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
    * Creates a form to edit a Groupe entity.
    *
    * @param Groupe $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Groupe $entity)
    {
        $form = $this->createForm(new GroupeType(), $entity, array(
            'action' => $this->generateUrl('les_groupe_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Groupe entity.
     *
     * @Route("/{id}/modifier_groupe", name="modifier_groupe")
     * @Method("PUT")
     * @Template("OrangeMainBundle:Groupe:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OrangeMainBundle:Groupe')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Groupe entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('les_groupe'));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
/**
     * Deletes a Groupe entity.
     * @QMLogger(message="Suppression d'un groupe")
     * @Route("/{id}/supprimer_groupe", name="supprimer_groupe")
     * @Method("GET")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function deleteAction(Request $request, $id)
    {
    	$em = $this->getDoctrine()->getManager();
    	$entity = $em->getRepository('OrangeMainBundle:Groupe')->find($id);
    	if($entity) {
    		if ($entity->getMembreGroupe()->count()>0 || $entity->getAction()->count()>0)
    		{
    			$this->get('session')->getFlashBag()->add('success', array (
    					'title' =>'Notification',
    					'body' => 'Suppression impossible ! '
    			));
    
    		}else {
    			$em->remove($entity);
    			$em->flush();
    			$this->get('session')->getFlashBag()->add('success', array (
    					'title' =>'Notification',
    					'body' => 'La suppresion du groupe a reussi ! '
    			));
    		}
    		
    
    	}else {
    		throw $this->createNotFoundException('Unable to find Groupe entity.');
    	}
    return $this->redirect($this->generateUrl('les_groupes'));
    }
    

    /**
     * Creates a form to delete a Groupe entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('supprimer_groupe', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
    /**
     * Supprimer chantier
     * @QMLogger(message="Suppression d'un groupe")
     * @Route("/{id}/supprimer_groupe", name="supprimer_groupe")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function supprimerAction($id){
    
    	$em = $this->getDoctrine()->getManager();
    	$entity = $em->getRepository('OrangeMainBundle:Groupe')->find($id);
    	if($entity) {
    		if ($entity->getIsDeleted()==false)
    		{
    			$entity->setIsDeleted(true);
    			$em->flush();
    			$this->get('session')->getFlashBag()->add('success', array (
    					'title' =>'Notification',
    					'body' => 'Le groupe à été activé avec succes ! '
    			));
    
    		}else {
    			$entity->setIsDeleted(false);
    			$em->flush();
    			$this->get('session')->getFlashBag()->add('success', array (
    					'title' =>'Notification',
    					'body' => 'La suppresion du groupe à été annule avec succes ! '
    			));
    		}
    		return $this->redirect($this->generateUrl('les_groupes'));
    
    	}else {
    		throw $this->createNotFoundException('Unable to find Groupe entity.');
    	}
    
    }
    
    
    /**
     * Lists  entities.
     *
     *@Route("/liste_des_groupes", name="liste_des_groupes")
     * @Method("GET")
     * @Template()
     */
    public function listAction(Request $request) {
    	$em = $this->getDoctrine()->getManager();
    	$form = $this->createForm(new GroupeCriteria());
    	$this->modifyRequestForForm($request, $this->get('session')->get('groupe_criteria'), $form);
    	$queryBuilder = $em->getRepository('OrangeMainBundle:Groupe')->listQueryBuilder();
    	return $this->paginate($request, $queryBuilder);
    }
    
    
    /**
     * @Route("/filtrer_les_chantiers", name="filtrer_les_chantiers")
     * @Template()
     */
    
    public function filterAction(Request $request) {
    	$form = $this->createForm(new GroupeCriteria());
    	if($request->getMethod()=='POST') {
    		$this->get('session')->set('groupe_criteria', $request->request->get($form->getName()));
    		return new JsonResponse();
    	} else {
    		$this->modifyRequestForForm($request, $this->get('session')->get('groupe_criteria'), $form);
    		return array('form' => $form->createView());
    
    	}
    
    }
    
    /**
     * @todo retourne le nombre d'enregistrements renvoyer par le résultat de la requête
     * @param \Orange\MainBundle\Entity\Groupe $entity
     * @return array
     */
    
    protected function addRowInTable($entity) {
    	return array(
    			$entity->getName(),
    			$entity->getDescription(),
    			$entity->getEmail(),
    			$this->get('orange_main.actions')->generateActionsForGroupe($entity)
    	);
    }
    /**
     * @todo ajoute un filtre
     * @param sfWebRequest $request
     */
    protected function setFilter(QueryBuilder $queryBuilder, $aColumns, Request $request) {
    	parent::setFilter($queryBuilder, array('g.name'), $request);
    }
}
