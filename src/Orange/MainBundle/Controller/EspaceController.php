<?php

namespace Orange\MainBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Orange\MainBundle\Entity\Statut;
use Orange\MainBundle\Form\EspaceType;
use Orange\MainBundle\Criteria\EspaceCriteria;
use Symfony\Component\HttpFoundation\JsonResponse;
use Orange\QuickMakingBundle\Controller\BaseController;
use Doctrine\ORM\QueryBuilder;
use Orange\MainBundle\Entity\Instance;
use Orange\MainBundle\Entity\Espace;
use Orange\MainBundle\Entity\Animateur;
use Orange\QuickMakingBundle\Annotation\QMLogger;
/**
 * Espace controller.
 *
 * 
 */
class EspaceController extends BaseController
{

	/**
	 * Lists all Espace entities.
	 * @Route("/{espace_id}/dashboard_espace", name="dashboard_espace")
	 * @Method("GET")
	 * @Template("OrangeMainBundle:Espace:dashboard.html.twig")
	 */
    public function accesEspaceAction($espace_id) {
    	$espace=$this->getDoctrine()->getRepository('OrangeMainBundle:Espace')->find($espace_id);
    	$stats=$this->getDoctrine()->getRepository('OrangeMainBundle:Action')->getStatsByEspace($espace_id)->getQuery()->getArrayResult();
    	$actions = $this->getDoctrine()->getRepository('OrangeMainBundle:Action')->allActionEspace($espace_id);
    	$req=array('faite délai'=>0, 'faite hors délai'=>0, 'soldée delai'=>0,'soldée hors delai'=>0, 'Echue non soldée'=>0, 'Demande Abandon'=>0, 'Abandonnée'=>0, 'Non échue'=>0 );
    	
    	foreach($stats as $stat) {
    		if($stat['etatCourant']== Statut::ACTION_SOLDEE_DELAI) {
    			$req['soldée delai']=$stat['total'];
    		}elseif($stat['etatCourant']== Statut::ACTION_SOLDEE_HORS_DELAI) {
    			$req['soldée hors delai']=$stat['total'];
    		}elseif($stat['etatCourant']== Statut::ACTION_FAIT_DELAI) {
    			$req['faite délai']=$stat['total'];
    		}elseif($stat['etatCourant']== Statut::ACTION_FAIT_HORS_DELAI) {
    			$req['faite hors délai']=$stat['total'];
    		}elseif ($stat['etatCourant']== Statut::ACTION_ECHUE_NON_SOLDEE) {
    			$req['Echue non soldée']=$stat['total'];
    		}elseif ($stat['etatCourant']== Statut::ACTION_DEMANDE_ABANDON) {
    			$req['Demande Abandon']=$stat['total'];
    		}elseif ($stat['etatCourant']== Statut::ACTION_ABANDONNEE) {
    			$req['Abandonnée']=$stat['total'];
    		}elseif ($stat['etatCourant']== Statut::ACTION_NON_ECHUE) {
    			$req['Non échue']=$stat['total'];
    		}
    	}
    	$req['total']=count($this->getDoctrine()->getRepository('OrangeMainBundle:Action')->findBy(array('instance'=>$espace->getInstance())));
    	return array('espace_id'=>$espace_id, 'entity'=>$espace, 'req'=>$req, 'nbrTotal'=>count($actions));
    }
	
    /**
     * @Route("/{espace_id}/membre_espace", name="membre_espace")
	 * @Method("GET")
	 * @Template("OrangeMainBundle:Espace:membresEspace.html.twig")
     */
    public function membresEspaceAction($espace_id){
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
    	$espace=$this->getDoctrine()->getRepository('OrangeMainBundle:Espace')->find($espace_id);
    	return array('espace_id'=>$espace_id, 'espace'=>$espace, 'gest' => $gestionnaire, 'nbrTotal' => count($actions), 'nbr' => count($act));
    }
    /**
     * Lists all Espace entities.
     * @QMLogger(message="Liste des espaces")
     * @Route("/les_espaces", name="les_espaces")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
    	$this->get('session')->set('espace_criteria', new Request());
    	return array();
    }
    /**
     * Creates a new Espace entity.
     *
     * @Route("/creer_espace", name="creer_espace")
     * @Method("POST")
     * @Template("OrangeMainBundle:Espace:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Espace();
        $instance=new Instance();
        $form = $this->createCreateForm($entity,'Espace');
        $form->handleRequest($request);

        if ($request->getMethod() == 'POST' ){
        	if($form->isValid()){
				$instance->setLibelle($entity->getLibelle());
				$instance->setDescription($entity->getDescription());
	        	$em = $this->getDoctrine()->getManager();
	        	$em->persist($instance);
	        	$entity->setInstance($instance);
	        	$em->persist($entity);
	        	$em->flush();
	        	return $this->redirect($this->generateUrl('les_espaces'));
        	}
        }

       return array(
           'entity' => $entity,
           'form'   => $form->createView(),
       );
    }


    /**
     * Displays a form to create a new Espace entity.
     * @QMLogger(message="Nouvelle espace")
     * @Route("/nouvel_espace", name="nouvel_espace")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Espace();
        $form   = $this->createCreateForm($entity,'Espace');
       
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Espace entity.
     * @QMLogger(message="Visualisation d'espace")
     * @Route("/{id}/details_espace", name="details_espace")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OrangeMainBundle:Espace')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Espace entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Espace entity.
     * @QMLogger(message="Modification espace")
     * @Route("/{id}/edition_espace", name="edition_espace", requirements={ "id"=  "\d+"})
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('OrangeMainBundle:Espace')->find($id);
        $editForm = $this->createEditForm($entity);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Espace entity.
    *
    * @param Espace $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Espace $entity)
    {
        $form = $this->createForm(new EspaceType(), $entity, array(
            'action' => $this->generateUrl('modifier_espace', array('id' => $entity->getId())),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Espace entity.
     *
     * @Route("/{id}/modifier_espace", name="modifier_espace", requirements={ "id"=  "\d+"})
     * @Method("POST")
     * @Template("OrangeMainBundle:Espace:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
    	$em = $this->getDoctrine()->getManager();
    	$entity = $em->getRepository('OrangeMainBundle:Espace')->find($id);
    	$form = $this->createCreateForm($entity,'Espace');
    	$request = $this->get('request');
    	if ($request->getMethod() == 'POST') {
    		$form->handleRequest($request);
    		if ($form->isValid()) {
    			$em->persist($entity);
    			$em->flush();
    			return $this->redirect($this->generateUrl('dashboard_espace',array('espace_id'=>$id)));
    		}
    	}
    	return array('entity' => $entity, 'edit_form' => $form->createView());
      
    }
    
    
    /**
     * Deletes a Espace entity.
     * @QMLogger(message="suppression d'espace")
     * @Route("/{id}", name="les_espaces_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('OrangeMainBundle:Espace')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Espace entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('les_espaces'));
    }

    /**
     * Creates a form to delete a Espace entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('les_espaces_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
   
    /**
     * Ajout ou Suppresion des gestionnaires
     * @QMLogger(message="Ajout gestionnaire")
     * @Route("/{id}_{user_id}/ajout_gestionnaire", name="ajout_gestionnaire")
     * @Method({"GET","POST"})
     * @Template("OrangeMainBundle:Espace:ajout_gestionnaire.html.twig")
     */
    public function ajoutGestionnaireAction(Request $request,$id,$user_id)
    {
    	$em = $this->getDoctrine()->getManager();
    	$entity = $em->getRepository('OrangeMainBundle:Espace')->find($id);
    	$animateur = new Animateur();
    	$user = $em->getRepository('OrangeMainBundle:Utilisateur')->find($user_id);
    	$membre=$em->getRepository('OrangeMainBundle:MembreEspace')->findOneBy(
    			array('utilisateur' => $user, 'espace' => $entity));
    	if(!$entity) 
    		throw $this->createNotFoundException('Unable to find Espace entity.');
    	if($request->getMethod() === 'POST'){
            if ($membre->getIsGestionnaire()==true){
            	$animateur=$em->getRepository('OrangeMainBundle:Animateur')->findOneBy(
            			array('utilisateur' => $user, 'instance' => $entity->getInstance()));
            	$membre->setIsGestionnaire(false);
            	if($animateur){
            		$em->remove($animateur);
            	}
            	
            } else {
        	   $membre->setIsGestionnaire(true);
        	   $animateur->setInstance($entity->getInstance());
        	   $animateur->setUtilisateur($user);
        	   $animateur->setDateAffectation(new \DateTime());
        	   $em->persist($animateur);
            	
            }
        	$em->flush();
        	return new JsonResponse(array('url' => $this->generateUrl('membre_espace', array('espace_id'=>$id))));
    	}
    	return array('entity'=>$entity, 'id'=>$id, 'user'=>$membre);
    }
    /**
     * Supprimer structure
     * @QMLogger(message="Suppression d'espace")
     * @Route("/{id}/supprimer_espace", name="supprimer_espace")
     */
    public function supprimerAction($id){
    
    	$em = $this->getDoctrine()->getManager();
    	$entity = $em->getRepository('OrangeMainBundle:Espace')->find($id);
    	if($entity) {
    		if ($entity->getIsDeleted()==false)
    		{
    			$entity->setIsDeleted(true);
    			$em->flush();
    			$this->get('session')->getFlashBag()->add('success', array (
    					'title' =>'Notification',
    					'body' => 'L\' espace a été activé avec succes ! '
    			));
    				
    		}else {
    			$entity->setIsDeleted(false);
    			$em->flush();
    			$this->get('session')->getFlashBag()->add('success', array (
    					'title' =>'Notification',
    					'body' => 'La suppression de l\'espace a été annule avec succes ! '
    			));
    		}
    		return $this->redirect($this->generateUrl('les_espaces'));
    
    	}else {
    		throw $this->createNotFoundException('Unable to find Espace entity.');
    	}
    	 
    	//return $this->redirect($this->generateUrl('structure'));
    }
    
    /**
     * Lists  entities.
     *
     * @Route("/liste_des_espaces", name="liste_des_espaces")
     * @Method("GET")
     * @Template()
     */
    public function listAction(Request $request) {
    	$em = $this->getDoctrine()->getManager();
    	$form = $this->createForm(new EspaceCriteria());
    	$this->modifyRequestForForm($request, $this->get('session')->get('espace_criteria'), $form);
    	$queryBuilder = $em->getRepository('OrangeMainBundle:Espace')->listQueryBuilder();
    	return $this->paginate($request, $queryBuilder);
    }
    
    
    /**
     * @Route("/filtrer_les_chantiers", name="filtrer_les_chantiers")
     * @Template()
     */
    
    public function filterAction(Request $request) {
    	$form = $this->createForm(new EspaceCriteria());
    	if($request->getMethod()=='POST') {
    		$this->get('session')->set('espace_criteria', $request->request->get($form->getName()));
    		return new JsonResponse();
    	} else {
    		$this->modifyRequestForForm($request, $this->get('session')->get('espace_criteria'), $form);
    		return array('form' => $form->createView());
    
    	}
    
    }
    
    /**
     * @todo retourne le nombre d'enregistrements renvoyer par le résultat de la requête
     * @param \Orange\MainBundle\Entity\Espace $entity
     * @return array
     */
    
    protected function addRowInTable($entity) {
    	return array(
    			$entity->getLibelle(),
    			$entity->getDateCreation()?$entity->getDateCreation()->format("d-m-Y"):null,
    			$entity->getMembreEspace()->count(),
    			$this->get('orange_main.actions')->generateActionsForEspace($entity)
    	);
    }
    /**
     * @todo ajoute un filtre
     * @param sfWebRequest $request
     */
    protected function setFilter(QueryBuilder $queryBuilder, $aColumns, Request $request) {
    	parent::setFilter($queryBuilder, array('e.libelle'), $request);
    }
}