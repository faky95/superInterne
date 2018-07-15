<?php
namespace Orange\MainBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Orange\MainBundle\Entity\TacheStatut;
use Orange\MainBundle\Form\TacheStatutType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Orange\QuickMakingBundle\Controller\BaseController;
use Orange\MainBundle\Entity\Statut;
use Symfony\Component\HttpFoundation\Response;
use Orange\QuickMakingBundle\Annotation\QMLogger;
use Orange\MainBundle\Utils\Notification;
use Orange\MainBundle\OrangeMainForms;
use Orange\MainBundle\Entity\TypeStatut;

/**
 * TacheStatut controller.
 * @Route("/tachestatut")
 */
class TacheStatutController extends BaseController
{

    /**
     * Lists all TacheStatut entities.
     * @Route("/", name="tachestatut")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('OrangeMainBundle:TacheStatut')->findAll();
        return array('entities' => $entities);
    }
    
    /**
     * @QMLogger(message="Historique des traitements d'une occurence")
     * @Route("/historique/{tache_id}", name="historique_tache")
     * @Template()
     */
    public function historiqueAction($tache_id) {
    	$em = $this->getDoctrine()->getManager();
    	$entities = $em->getRepository("OrangeMainBundle:TacheStatut")->findByTache($tache_id);
    	return array('entities' => $entities);
    }
    
    /**
     * Creates a new Domaine entity.
     * @Route("/creer_tache_statutttttt/{tache_id}/{etat}", name="tachestatut_createeee")
     * @Method("POST")
     * @Template("OrangeMainBundle:TacheStatut:new.html.twig")
     */
    public function createeeAction(Request $request, $tache_id, $etat)
    {
    	$entity = new TacheStatut();
    	$form = $this->createCreateForm($entity,'TacheStatut');
    	$form->handleRequest($request);
    
    	if ($form->isValid())  {
    		$em = $this->getDoctrine()->getManager();
    		$em->persist($entity);
    		$tache = $em->getRepository('OrangeMainBundle:Tache')->find($tache_id);
    		$actionCyclique = $tache->getActionCyclique();
    		$today = new \DateTime('NOW');
    		$dateInitial = $tache->getDateInitial();
    		switch ($etat) {
				case 'FAITE':
					$tache->setDateCloture($today);
					$statut = ($today > $dateInitial) ? Statut::ACTION_FAIT_HORS_DELAI : Statut::ACTION_FAIT_DELAI;
					break;
				case 'SOLDER':
					$statut = ($today > $dateInitial) ? Statut::ACTION_SOLDEE_HORS_DELAI : Statut::ACTION_SOLDEE_DELAI;
					break;
				case 'NON_SOLDER':
					$statut = ($today > $dateInitial) ? Statut::ACTION_NON_ECHUE : Statut::ACTION_ECHUE_NON_SOLDEE;
					break;
			}
			$statutEntity = $em->getRepository('OrangeMainBundle:Statut')->findOneByCode($statut);
			
			$entity->setUtilisateur($this->getUser());
			$entity->setStatut($statutEntity);
			$entity->setTache($tache);
			$tache->setEtatCourant($statut);
			$em->flush();
			return new JsonResponse(array('url' => $this->generateUrl('actioncyclique_show', array('id' => $actionCyclique->getId()))));
    	}
    	return new Response($this->renderView('OrangeMainBundle:TacheStatut:new.html.twig', array('entity' => $entity, 'etat' => $etat, 'form' => $form->createView())), 303);
    }
    
    /**
     * Displays a form to create a new TacheStatut entity.
     * @QMLogger(message="Ouverture du formulaire de traitement sur une tâche")
     * @Route("/tache_statut_nouveau/{tache_id}/{etat}", name="tachestatut_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction($tache_id, $etat) {
    	$em = $this->getDoctrine()->getManager();
    	$tache = $em->getRepository("OrangeMainBundle:Tache")->find($tache_id);
    	$entity = new TacheStatut();
    	$event = ($etat=='EVENEMENT_DEMANDE_SOLDE' || $etat=='EVENEMENT_DEMANDE_ABANDON') ? OrangeMainForms::TACHESTATUT_FAIT : null;
    	$form = $this->createForm(new TacheStatutType($event), $entity);
    	return array('entity' => $entity, 'form'   => $form->createView(), 'tache'	=> $tache, 'etat'	=> $etat);
    }

    /**
     * Finds and displays a TacheStatut entity.
     * @Route("/{id}", name="tachestatut_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('OrangeMainBundle:TacheStatut')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TacheStatut entity.');
        }
        return array('entity' => $entity);
    }

    /**
     * Displays a form to edit an existing TacheStatut entity.
     * @Route("/{id}/edit", name="tachestatut_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('OrangeMainBundle:TacheStatut')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TacheStatut entity.');
        }
        $editForm = $this->createEditForm($entity);
        return array('entity' => $entity, 'edit_form' => $editForm->createView());
    }

    /**
    * Creates a form to edit a TacheStatut entity.
    * @param TacheStatut $entity The entity
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(TacheStatut $entity)
    {
        $form = $this->createForm(new TacheStatutType(), $entity, array(
            'action' => $this->generateUrl('tachestatut_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));
        $form->add('submit', 'submit', array('label' => 'Update'));
        return $form;
    }
    
    /**
     * Edits an existing TacheStatut entity.
     * @Route("/{id}", name="tachestatut_update")
     * @Method("PUT")
     * @Template("OrangeMainBundle:TacheStatut:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('OrangeMainBundle:TacheStatut')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TacheStatut entity.');
        }
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        if ($editForm->isValid()) {
            $em->flush();
            return $this->redirect($this->generateUrl('tachestatut_edit', array('id' => $id)));
        }
        return array('entity' => $entity, 'edit_form' => $editForm->createView());
    }
    
    /**
     * Deletes a TacheStatut entity.
     * @Route("/{id}", name="tachestatut_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('OrangeMainBundle:TacheStatut')->find($id);
            if (!$entity) {
                throw $this->createNotFoundException('Unable to find TacheStatut entity.');
            }
            $em->remove($entity);
            $em->flush();
        }
        return $this->redirect($this->generateUrl('tachestatut'));
    }

    /**
     * @QMLogger(message="Traitement sur une tâche")
     * @Route("/creer_tache_statut/{tache_id}/{etat}", name="tachestatut_create")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function createAction(Request $request, $tache_id, $etat) {
    	$em   = $this->getDoctrine()->getManager();
    	$tache = $em->getRepository('OrangeMainBundle:Tache')->find($tache_id);
    	$actionCyclique = $em->getRepository('OrangeMainBundle:Tache')->find($tache_id)->getActionCyclique();
    	$action = $tache->getActionCyclique()->getAction();
    	$animateurs = array();
    	foreach($action->getInstance()->getAnimateur() as $animateur) {
    		$animateurs[] = $animateur->getUtilisateur()->getEmail();
    	}
    	$statut = null;
		$copy = array($this->getUser()->getEmail());
    	if($request->getMethod() == 'POST') {
    		$statutTache = new TacheStatut();
			switch ($etat) {
				case 'EVENEMENT_DEMANDE_SOLDE':
					$form = $this->createForm(new TacheStatutType(OrangeMainForms::TACHESTATUT_FAIT), $statutTache);
					$form->handleRequest($request);
					$tache->setDateFinExecut($statutTache->dateFinExecut);
					$target = $animateurs;
					$subject = "Fin de traitement d'une tâche";	
					$statut = ($tache->getDateInitial() > new \DateTime('NOW')) ? Statut::ACTION_FAIT_DELAI : Statut::ACTION_FAIT_HORS_DELAI;
					$infos = sprintf("La tâche %s a été traitée par %s . %s est invité à prendre en charge.", 
								$tache->getReference(), $this->getUser()->getCompletNom(), $action->getAnimateur()->getNomComplet()
							);
					break;
				case 'EVENEMENT_DEMANDE_ABANDON':
					$form = $this->createForm(new TacheStatutType(OrangeMainForms::TACHESTATUT_DEMANDE_ABANDON), $statutTache);
					$form->handleRequest($request);
					$target = $animateurs;
					$subject = "Demande d'abandon d'une tâche";
					$statut = Statut::ACTION_DEMANDE_ABANDON;
					$infos = sprintf("%s a demandé un abandon de la tâche %s. %s est invité à prendre en charge.", 
								$tache->getReference(), $this->getUser()->getCompletNom(), $action->getAnimateur()->getNomComplet()
							);
					break;
				case 'EVENEMENT_VALIDER':
					$form = $this->createForm(new TacheStatutType(), $statutTache);
					$form->handleRequest($request);
					$copy = array_merge($copy, $animateurs);
					$target = array($tache->getActionCyclique()->getAction()->getPorteur()->getEMail());
					$tache->getDateCloture(new \DateTime('NOW'));
					if($tache->getEtatCourant()==Statut::ACTION_DEMANDE_ABANDON) {
						$subject = "Abandon d'une tâche";
						$statut = Statut::ACTION_ABANDONNEE;
						$infos = sprintf("%s a validé l'abandon de la tâche .", $this->getUser()->getCompletNom());
					} elseif($tache->getEtatCourant()==Statut::ACTION_FAIT_DELAI || $tache->getEtatCourant()==Statut::ACTION_FAIT_HORS_DELAI) {
						$subject = "Clôture d'une tâche";
						$infos = sprintf("La clôture de la tâche %s traitée par %s a été soldée par %s.", 
									$tache->getReference(), $action->getPorteur()->getCompletNom(), $this->getUser()->getCompletNom()
								);
						if($tache->getDateInitial() >= $tache->getDateFinExecut()) {
							$statut = Statut::ACTION_SOLDEE_DELAI;
						} else {
							$statut = Statut::ACTION_SOLDEE_HORS_DELAI;
						}
					}
					break;
				case 'EVENEMENT_INVALIDER':
					$form = $this->createForm(new TacheStatutType(), $statutTache);
					$form->handleRequest($request);
					$copy = array_merge($copy, $animateurs);
					$target = array($tache->getActionCyclique()->getAction()->getPorteur()->getEMail());
					if($tache->getEtatCourant()==Statut::ACTION_DEMANDE_ABANDON) {
						$subject = "Rejet de l'abandon d'une tâche";
					} elseif($tache->getEtatCourant()==Statut::ACTION_FAIT_DELAI || $tache->getEtatCourant()==Statut::ACTION_FAIT_HORS_DELAI) {
						$subject = "Rejet du solde d'une tâche";
						$infos = sprintf("La clôture de la tâche %s traitée par %s a été rejeté par %s.", 
									$tache->getReference(), $action->getPorteur()->getCompletNom(), $this->getUser()->getCompletNom()
								);
					}
					if($tache->getDateInitial() > new \DateTime('NOW')) {
						$statut = Statut::ACTION_NON_ECHUE;
					} else {
						$statut = Statut::ACTION_ECHUE_NON_SOLDEE;
					}
					$tache->getDateCloture(null);
					break;
			}
			if($statut) {
				$statutTache->setTache($tache);				
				$typeStatut = $em->getRepository('OrangeMainBundle:TypeStatut')->findOneByLibelle(TypeStatut::TYPE_ACTION);
				$statutEntity = $em->getRepository('OrangeMainBundle:Statut')->findOneBy(array('code' => $statut, 'typeStatut' => $typeStatut->getId()));
				$statutTache->setStatut($statutEntity);
				$statutTache->setUtilisateur($this->getUser());
				$tache->setEtatCourant($statut);
				$em->persist($tache);
				foreach($statutTache->erq as $erq) {
					if($erq->getFile()) {
						$erq->setType($this->getParameter('types')['demande_solde']);
						$erq->setNomFichier($erq->getFile()->getClientOriginalName());
						$erq->setTache($tache);
						$erq->setUtilisateur($this->getUser());
						$em->persist($erq);
					}
				}
				$em->persist($statutTache);
				$em->flush();
    			$this->get('session')->getFlashBag()->add('success', array(
    					'title' => 'Notification', 'body' =>  "Le traitement sur la tâche s'est effectué avec succès"
    				));
    			foreach($action->getContributeur() as $contributeur) {
    				$copy[] = $contributeur->getUtilisateur()->getEmail();
    			}
    			if($this->getUser()->getSuperior()) {
    				$copy[] = $this->getUser()->getSuperior()->getEmail();
    			}
    			Notification::notificationWithCopy($this->get('orange.main.mailer'), $subject, array_unique($target), array_unique($copy), $infos, $tache);
			} else {
    			$this->get('session')->getFlashBag()->add('error', array(
    					'title' => 'Notification', 'body' =>  "Une erreur inattendue s'est produite lors du traitement sur la tâche"
    				));
			}
    		return new JsonResponse(array('url' => $this->generateUrl('actioncyclique_show', array('id' => $actionCyclique->getId()))));
    	}
    	return array('tache' => $tache);
    }
}
