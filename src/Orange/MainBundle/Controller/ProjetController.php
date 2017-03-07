<?php
namespace Orange\MainBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Orange\MainBundle\Entity\Projet;
use Orange\MainBundle\Form\ProjetType;
use Orange\QuickMakingBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Orange\QuickMakingBundle\Annotation\QMLogger;
use Orange\MainBundle\Criteria\ProjetCriteria;
use Doctrine\ORM\QueryBuilder;
use Orange\MainBundle\Entity\Statut;

/**
 * Projet controller.
 */
class ProjetController extends BaseController
{

    /**
	 * @QMLogger(message="Liste des projets")
     * @Route("/les_projets", name="les_projets")
     * @Method("GET")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Template()
     */
    public function indexAction()
    {
    	$this->get('session')->set('projet_criteria', new Request());
    	return array();
    }

    /**
     * Lists  entities.
     * @Route("/liste_des_projets", name="liste_des_projets")
     * @Method("GET")
     * @Template()
     */
    public function listAction(Request $request) {
    	$em = $this->getDoctrine()->getManager();
    	$form = $this->createForm(new ProjetCriteria());
    	$this->modifyRequestForForm($request, $this->get('session')->get('projet_criteria'), $form);
    	$queryBuilder = $em->getRepository('OrangeMainBundle:Projet')->listQueryBuilder();
    	return $this->paginate($request, $queryBuilder);
    }
    
    /**
	 * Lists all Projet entities.
	 * @Route("/{id}/dashboard_projet", name="dashboard_projet")
	 * @Method("GET")
	 * @Template("OrangeMainBundle:Projet:dashboard.html.twig")
	 */
    public function dashboardAction($id) {
    	$projet = $this->getDoctrine()->getRepository('OrangeMainBundle:Projet')->find($id);
    	$stats = $this->getDoctrine()->getRepository('OrangeMainBundle:Action')->getStatsByProjet($id)->getQuery()->getArrayResult();
    	$req = array(
    			'faite délai' => 0, 'faite hors délai' => 0, 'soldée delai' => 0,'soldée hors delai' => 0, 
    			'Echue non soldée' => 0, 'Demande Abandon' => 0, 'Abandonnée' => 0, 'Non échue' => 0
    		);
    	foreach($stats as $stat) {
    		if($stat['etatCourant']== Statut::ACTION_SOLDEE_DELAI) {
    			$req['soldée delai']=$stat['total'];
    		} elseif($stat['etatCourant']== Statut::ACTION_SOLDEE_HORS_DELAI) {
    			$req['soldée hors delai']=$stat['total'];
    		} elseif($stat['etatCourant']== Statut::ACTION_FAIT_DELAI) {
    			$req['faite délai']=$stat['total'];
    		} elseif($stat['etatCourant']== Statut::ACTION_FAIT_HORS_DELAI) {
    			$req['faite hors délai']=$stat['total'];
    		} elseif ($stat['etatCourant']== Statut::ACTION_ECHUE_NON_SOLDEE) {
    			$req['Echue non soldée']=$stat['total'];
    		} elseif ($stat['etatCourant']== Statut::ACTION_DEMANDE_ABANDON) {
    			$req['Demande Abandon']=$stat['total'];
    		} elseif ($stat['etatCourant']== Statut::ACTION_ABANDONNEE) {
    			$req['Abandonnée']=$stat['total'];
    		} elseif ($stat['etatCourant']== Statut::ACTION_NON_ECHUE) {
    			$req['Non échue']=$stat['total'];
    		}
    	}
    	return array('entity' => $projet, 'req'=>$req);
    }
    
    /**
     * Creates a new Projet entity.
     * @Route("/creer_projet", name="creer_projet")
     * @Method("POST")
     * @Template("OrangeMainBundle:Projet:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Projet();
        $form = $this->createCreateForm($entity, 'Projet');
        $form->handleRequest($request);
        if ($request->getMethod() == 'POST' ) {
        	if($form->isValid()){
	            $em = $this->getDoctrine()->getManager();
	            $em->persist($entity);
	            $em->flush();
	            return $this->redirect($this->generateUrl('les_projets'));
        	}
        }
        return array('entity' => $entity, 'form'   => $form->createView());
    }

    /**
     * Displays a form to create a new Projet entity.
     * @Route("/nouveau_projet", name="nouveau_projet")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Projet();
        $form   = $this->createCreateForm($entity,'Projet');
        return array('entity' => $entity, 'form' => $form->createView());
    }

     /**
       * Finds and displays a Projet entity.
       * @Route("/{id}/details_projet", name="details_projet")
       * @Method("GET")
       * @Template()
       */
      public function showAction($id) {
          $em = $this->getDoctrine()->getManager();
          $entity = $em->getRepository('OrangeMainBundle:Projet')->find($id);
          if (!$entity) {
              throw $this->createNotFoundException('Unable to find Projet entity.');
          }
          return array('entity' => $entity);
      }

    /**
     * Displays a form to edit an existing Projet entity.
     * @Route("/{id}/edition_projet", name="edition_projet", requirements={ "id"=  "\d+"})
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('OrangeMainBundle:Projet')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Projet entity.');
        }
        $editForm = $this->createEditForm($entity);
        return array('entity' => $entity, 'edit_form' => $editForm->createView());
    }

    /**
    * Creates a form to edit a Projet entity.
    * @param Projet $entity The entity
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Projet $entity)
    {
        $form = $this->createForm(new ProjetType(), $entity, array(
            'action' => $this->generateUrl('modifier_projet', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));
        $form->add('submit', 'submit', array('label' => 'Update'));
        return $form;
    }
    
    /**
     * Edits an existing Projet entity.
     * @Route("/{id}/modifier_projet", name="modifier_projet", requirements={ "id"=  "\d+"})
     * @Method("POST")
     * @Template("OrangeMainBundle:Projet:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('OrangeMainBundle:Projet')->find($id);
        $form = $this->createCreateForm($entity,'Projet');
        $request = $this->get('request');
        if ($request->getMethod() == 'POST') {
        	$form->handleRequest($request);
        	if ($form->isValid()) {
        		$em->persist($entity);
        		$em->flush();
        		return $this->redirect($this->generateUrl('details_projet', array('id'=>$id)));
        	}
        }
        return array('entity' => $entity, 'edit_form' => $form->createView());
    }
    
    /**
     * Deletes a Projet entity.
     * @Route("/{id}", name="projet_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('OrangeMainBundle:Projet')->find($id);
            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Projet entity.');
            }
            $em->remove($entity);
            $em->flush();
        }
        return $this->redirect($this->generateUrl('projet'));
    }

    /**
     * Supprimer chantier
     * @Route("/{id}/supprimer_projet", name="supprimer_projet")
     */
    public function supprimerAction($id){
    
    	$em = $this->getDoctrine()->getManager();
    	$entity = $em->getRepository('OrangeMainBundle:Projet')->find($id);
    	if($entity) {
    		if($entity->getEtat()==false) {
    			$entity->setEtat(true);
    			$em->flush();
    			$this->get('session')->getFlashBag()->add('success', array(
    					'title' =>'Notification', 'body' => 'Le projet à été activé avec succes ! '
    				));
    		} else {
    			$entity->setEtat(false);
    			$em->flush();
    			$this->get('session')->getFlashBag()->add('success', array(
    					'title' =>'Notification', 'body' => 'Le suppression du projet à été annule avec succes ! '
    				));
    		}
    		return $this->redirect($this->generateUrl('les_projets'));
    	} else {
    		throw $this->createNotFoundException('Unable to find Projet entity.');
    	}
    
    }
    
    /**
     * @Route("/filtrer_les_instances", name="filtrer_les_instances")
     * @Template()
     */
    public function filterAction(Request $request) {
    	$form = $this->createForm(new ProjetCriteria());
    	if($request->getMethod()=='POST') {
    		$this->get('session')->set('projet_criteria', $request->request->get($form->getName()));
    		return new JsonResponse();
    	} else {
    		$this->modifyRequestForForm($request, $this->get('session')->get('projet_criteria'), $form);
    		return array('form' => $form->createView());
    	}
    }
    
    /**
     * @todo retourne le nombre d'enregistrements renvoyer par le résultat de la requête
     * @param \Orange\MainBundle\Entity\Projet $entity
     * @return array
     */
    protected function addRowInTable($entity) {
    	return array(
    			$entity->getLibelle(),
    			$entity->listChefProjet(),
    			$entity->getDateCreation() ? $entity->getDateCreation()->format("d-m-Y") : null,
      			$this->get('orange_main.actions')->generateActionsForProjet($entity)
    		);
    }
    
    /**
     * @todo ajoute un filtre
     * @param sfWebRequest $request
     */
    protected function setFilter(QueryBuilder $queryBuilder, $aColumns, Request $request) {
    	parent::setFilter($queryBuilder, array('p.libelle'), $request);
    }
    
}
