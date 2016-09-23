<?php

namespace Orange\MainBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Orange\MainBundle\Entity\Chantier;
use Orange\MainBundle\Form\ChantierType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Orange\QuickMakingBundle\Controller\BaseController;
use Orange\MainBundle\Criteria\ChantierCriteria;
use Doctrine\ORM\QueryBuilder;
use Orange\QuickMakingBundle\Annotation\QMLogger;
/**
 * Chantier controller.
 *
 */
class ChantierController extends BaseController
{

    /**
     * Lists all Chantier entities.
     *
     * @Route("/les_chantiers", name="les_chantiers")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
    	$this->get('session')->set('chantier_criteria', new Request());
    	return array();
    }
    

    
    
    /**
     * Creates a new Chantier entity.
     *
     * @Route("/creer_chantier", name="creer_chantier")
     * @Method("POST")
     * @Template("OrangeMainBundle:Chantier:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Chantier();
        $form = $this->createCreateForm($entity,'Chantier');
        $form->handleRequest($request);

        if ($request->getMethod() == 'POST') {
        	if ($form->isValid()) {
        	foreach ($entity->getTmpMembre() as $tp)
        		$entity->addTmpMembre($tp);
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            return $this->redirect($this->generateUrl('les_chantiers'));
        	}
        }
      
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Chantier entity.
     *
     * @param Chantier $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
    
    private function createCreateForm(Chantier $entity)
    {
        $form = $this->createForm(new ChantierType(), $entity, array(
            'action' => $this->generateUrl('creer_chantier'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    } */

    /**
     * Displays a form to create a new Chantier entity.
     *
     * @Route("/nouveau_chantier", name="nouveau_chantier")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Chantier();
        $form   = $this->createCreateForm($entity,'Chantier');

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Chantier entity.
     *
     * @Route("/{id}/details_chantier", name="details_chantier")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OrangeMainBundle:Chantier')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Chantier entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Chantier entity.
     *
     * @Route("/{id}/edition_chantier", name="edition_chantier", requirements={ "id"=  "\d+"})
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OrangeMainBundle:Chantier')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Chantier entity.');
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
    * Creates a form to edit a Chantier entity.
    *
    * @param Chantier $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Chantier $entity)
    {
        $form = $this->createForm(new ChantierType(), $entity, array(
            'action' => $this->generateUrl('modifier_chantier', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Chantier entity.
     *
     * @Route("/{id}/modifier_chantier", name="modifier_chantier", requirements={ "id"=  "\d+"})
     * @Method("POST")
     * @Template("OrangeMainBundle:Chantier:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('OrangeMainBundle:Chantier')->find($id);
        $form = $this->createCreateForm($entity,'Chantier');
        $request = $this->get('request');
        if ($request->getMethod() == 'POST') {
        	$form->handleRequest($request);
        	if ($form->isValid()) {
        		$em->persist($entity);
        		$em->flush();
        		return $this->redirect($this->generateUrl('details_chantier', array('id'=>$id)));
        	}
        }
        return array('entity' => $entity, 'edit_form' => $form->createView());
    }
    /**
     * Deletes a Chantier entity.
     *
     * @Route("/{id}", name="les_chantiers_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('OrangeMainBundle:Chantier')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Chantier entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('les_chantiers'));
    }

    /**
     * Creates a form to delete a Chantier entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('les_chantiers_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
    
    /**
     * Ajout ou Suppresion de chef de chantier
     *
     * @Route("/{id}_{user_id}/ajout_chef", name="ajout_chef")
     * @Method("GET")
     */
    public function ajoutChefAction($id,$user_id)
    {
    	$em = $this->getDoctrine()->getManager();
    	$entity = $em->getRepository('OrangeMainBundle:Chantier')->find($id);
    	if($entity) {
    		$user = $em->getRepository('OrangeMainBundle:Utilisateur')->find($user_id);
    		$membre=$em->getRepository('OrangeMainBundle:MembreChantier')->findOneBy(
    				array('utilisateur' => $user, 'chantier' => $entity));
    		if ($membre->getIsChef()==1)
    			$membre->setIsChef(0);
    		else
    			$membre->setIsChef(1);
    		$em->flush();
    
    		return $this->redirect($this->generateUrl('details_chantier', array('id' => $id)));
    	}else {
    		throw $this->createNotFoundException('Unable to find Chantier entity.');
    	}
    
    }
    /**
     * Supprimer chantier
     *
     * @Route("/{id}/supprimer_chantier", name="supprimer_chantier")
     */
    public function supprimerAction($id){
    
    	$em = $this->getDoctrine()->getManager();
    	$entity = $em->getRepository('OrangeMainBundle:Chantier')->find($id);
    	if($entity) {
    		if ($entity->getIsDeleted()==false)
    		{
    			$entity->setIsDeleted(true);
    			$em->flush();
    			$this->get('session')->getFlashBag()->add('success', array (
    					'title' =>'Notification',
    					'body' => 'Le chantier à été activé avec succes ! '
    			));
    				
    		}else {
    			$entity->setIsDeleted(false);
    			$em->flush();
    			$this->get('session')->getFlashBag()->add('success', array (
    					'title' =>'Notification',
    					'body' => 'Le chantier de la structure à été annule avec succes ! '
    			));
    		}
    		return $this->redirect($this->generateUrl('les_chantiers'));
    
    	}else {
    		throw $this->createNotFoundException('Unable to find Structure entity.');
    	}
    	 
    	//return $this->redirect($this->generateUrl('structure'));
    }
    
    /**
     * Lists  entities.
     *
     *@Route("/liste_des_chantiers", name="liste_des_chantiers")
     * @Method("GET")
     * @Template()
     */
    public function listAction(Request $request) {
    	$em = $this->getDoctrine()->getManager();
    	$form = $this->createForm(new ChantierCriteria());
    	$this->modifyRequestForForm($request, $this->get('session')->get('chantier_criteria'), $form);
    	$queryBuilder = $em->getRepository('OrangeMainBundle:Chantier')->listQueryBuilder();
    	return $this->paginate($request, $queryBuilder);
    }
    

    /**
     * @Route("/filtrer_les_chantiers", name="filtrer_les_chantiers")
     * @Template()
     */
    
    public function filterAction(Request $request) {
    	$form = $this->createForm(new ChantierCriteria());
    	if($request->getMethod()=='POST') {
    		$this->get('session')->set('chantier_criteria', $request->request->get($form->getName()));
    		return new JsonResponse();
    	} else {
    		$this->modifyRequestForForm($request, $this->get('session')->get('chantier_criteria'), $form);
    		return array('form' => $form->createView());
    
    	}
    
    }
    
    /**
     * @todo retourne le nombre d'enregistrements renvoyer par le résultat de la requête
     * @param \Orange\MainBundle\Entity\Chantier $entity
     * @return array
     */
    
    protected function addRowInTable($entity) {
    	return array(
    			$entity->getLibelle(),
    			$entity->getProjet()->__toString(),
    			$entity->getDateCreation()->format("d-m-Y"),
    			null
    	);
    }
    
    /**
     * @todo ajoute un filtre
     * @param sfWebRequest $request
     */
    protected function setFilter(QueryBuilder $queryBuilder, $aColumns, Request $request) {
    	parent::setFilter($queryBuilder, array('c.libelle'), $request);
    }
    
}
