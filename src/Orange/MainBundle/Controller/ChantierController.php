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
use Orange\MainBundle\Entity\Statut;

/**
 * Chantier controller.
 */
class ChantierController extends BaseController
{

	/**
	 * @Route("/{id}/dashboard_chantier", name="dashboard_chantier")
	 * @Method("GET")
	 * @Template("OrangeMainBundle:Chantier:dashboard.html.twig")
	 */
	public function dashboardAction($id) {
		$chantier = $this->getDoctrine()->getRepository('OrangeMainBundle:Chantier')->find($id);
		$stats = $this->getDoctrine()->getRepository('OrangeMainBundle:Action')->getStatsByChantier($id)->getQuery()->getArrayResult();
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
		return array('entity' => $chantier, 'req'=>$req);
	}
	
    /**
	 * @QMLogger(message="Liste des chantiers d'un projet")
     * @Route("/{projet_id}/les_chantiers_by_projet", name="les_chantiers_by_projet")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request, $projet_id)
    {
    	$em = $this->getDoctrine()->getManager();
    	$form = $this->createForm(new ChantierCriteria(), null, array('attr'=>array( 'projet_id'=> $projet_id)));
    	$data = $request->get($form->getName());
    	if($request->getMethod()=='POST') {
    		if(isset($data['effacer'])) {
    			$this->get('session')->set('chantier_criteria', new Request());
    		} else {
    			$this->get('session')->set('chantier_criteria', $request->request->get($form->getName()));
    			$form->handleRequest($request);
    		}
    	} else {
    		$this->get('session')->set('chantier_criteria', new Request());
    	}
    	$projet		= $em->getRepository('OrangeMainBundle:Projet')->find($projet_id);
    	return array('form' => $form->createView(), 'projet'=> $projet);
    	$this->get('session')->set('chantier_criteria', new Request());
    	return array();
    }
    
    /**
     * @QMLogger(message="Ajouter un chantier au projet")
     * @Route("/{projet_id}/creer_chantier", name="creer_chantier")
     * @Method("POST")
     * @Template("OrangeMainBundle:Chantier:new.html.twig")
     */
    public function createAction(Request $request, $projet_id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new Chantier();
        $entity->setProjet($em->getReference('OrangeMainBundle:Projet', $projet_id));
        $form = $this->createCreateForm($entity, 'Chantier');
        $form->handleRequest($request);
       	if($form->isValid()) {
            $em->persist($entity);
            $em->flush();
            return $this->redirect($this->generateUrl('les_chantiers_by_projet', array('projet_id' => $projet_id)));
       	}
        return array('entity' => $entity, 'form'   => $form->createView());
    }

    /**
     * @QMLogger(message="Ajout d'un chantier au projet")
     * @Route("/{projet_id}/nouveau_chantier", name="nouveau_chantier")
     * @Method("GET")
     * @Template()
     */
    public function newAction($projet_id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new Chantier();
    	$projet	= $em->getRepository('OrangeMainBundle:Projet')->find($projet_id);
        if(!$projet) {
            $this->addFlash('error', "Projet non reconnu");
            return $this->redirect($this->generateUrl('dashboard'));
        }
    	$entity->setProjet($projet);
        $form   = $this->createCreateForm($entity, 'Chantier');
        return array('entity' => $entity, 'form'   => $form->createView());
    }

    /**
     * @QMLogger(message="Visualisation d'un chantier")
     * @Route("/{id}/details_chantier", name="details_chantier")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('OrangeMainBundle:Chantier')->find($id);
        if (!$entity) {
            $this->addFlash('error', "Chantier non reconnu");
            return $this->redirect($this->generateUrl('dashboard'));
        }
        return array('entity' => $entity);
    }

    /**
     * @QMLogger(message="Edition d'un chantier")
     * @Route("/{id}/edition_chantier", name="edition_chantier", requirements={ "id"=  "\d+"})
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('OrangeMainBundle:Chantier')->find($id);
        if(!$entity) {
            $this->addFlash('error', "Chantier non reconnu");
            return $this->redirect($this->generateUrl('dashboard'));
        }
        $editForm = $this->createEditForm($entity);
        return array('entity' => $entity, 'edit_form' => $editForm->createView());
    }

    /**
    * Creates a form to edit a Chantier entity.
    * @param Chantier $entity The entity
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
     * @QMLogger(message="Modifier un chantier")
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
	        if(!$entity) {
	            $this->addFlash('error', "Chantier non reconnu");
	            return $this->redirect($this->generateUrl('dashboard'));
	        }
	        if($em->getRepository('OrangeMainBundle:Action')->getNumberByChantier($id) > 0) {
	        	$this->addFlash('warning', "Impossible de supprimer le chantier. Il comporte déjà des actions");
	        	return $this->redirect($this->generateUrl('dashboard'));
	        }
            $em->remove($entity);
            $em->flush();
        }
        return $this->redirect($this->generateUrl('les_chantiers'));
    }

    /**
     * Supprimer chantier
     * @Route("/{id}/supprimer_chantier", name="supprimer_chantier")
     */
    public function supprimerAction($id) {
    	$em = $this->getDoctrine()->getManager();
    	$entity = $em->getRepository('OrangeMainBundle:Chantier')->find($id);
    	if($entity) {
    		if ($entity->getIsDeleted()==false) {
    			$entity->setIsDeleted(true);
    			$em->flush();
    			$this->get('session')->getFlashBag()->add('success', array (
    					'title' =>'Notification', 'body' => 'Le chantier à été activé avec succés ! '
    				));
    		} else {
    			$entity->setIsDeleted(false);
    			$em->flush();
    			$this->get('session')->getFlashBag()->add('success', array (
    					'title' =>'Notification', 'body' => 'Le chantier à été annulé avec succés ! '
    				));
    		}
    		return $this->redirect($this->generateUrl('les_chantiers'));
    
    	} else {
    		throw $this->createNotFoundException('Unable to find Structure entity.');
    	}
    }
    
    /**
     * Lists  entities.
     * @Route("/{projet_id}/liste_des_chantiers", name="liste_des_chantiers")
     * @Method("GET")
     * @Template()
     */
    public function listAction(Request $request, $projet_id) {
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
    			$entity->listChefChantier(),
    			$entity->getDateCreation()->format("d-m-Y"),
      			$this->get('orange_main.actions')->generateActionsForChantier($entity)
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
