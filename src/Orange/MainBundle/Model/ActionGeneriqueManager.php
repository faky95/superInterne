<?php

namespace Orange\MainBundle\Model;

use Orange\MainBundle\Entity\Action;
use Orange\MainBundle\Entity\ActionGenerique;
use Orange\MainBundle\Entity\ActionGeneriqueHasAction;
use Orange\MainBundle\Entity\Statut;
use Orange\MainBundle\Utils\ActionUtils;
use Orange\MainBundle\Utils\Notification;

class ActionGeneriqueManager
{
	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $em;

	/**
	 *
	 * @var \Orange\MainBundle\Entity\Utilisateur
	 */
	private $user;
	
	protected $container;
	
	/**
	 * @var \Orange\MainBundle\Repository\ActionRepository
	 */
	protected $repository;
	
	protected $helper;

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 */
	public function __construct($em, $container, $security_context) {
		$this->em = $em;
		$this->container = $container;
		$this->user = $security_context->getToken()->getUser();
		$this->repository = $em->getRepository('OrangeMainBundle:Action');
		$this->helper = $this->container->get('orange.main.mailer');
		$this->types = $container->getParameter('types');
	}
	
	
	/**
	 * Création d'action
	 * 
	 * @param ActionGenerique $action
	 * @param unknown $helper
	 */
	public function newAction($action , $helper) {
		$commentaire = "Nouvelle action générique créée par {$this->user}";
		ActionUtils::changeStatutActionGenerique($this->em, $action, Statut::ACTION_NON_ECHUE, $this->user, $commentaire);
		$to = array($action->getPorteur()->getEmail());
		$cc = array($action->getAnimateur()->getEmail());
		$object     = 'Nouvelle Action Générique';
		$commentaire = "L\'action générique : {$action->getReference()}
		                a été créé par {$action->getAnimateur()->getNomComplet()} .";
		Notification::notifActionGenerique($helper, $object, $to, $cc , $commentaire, $action);
	}
	

	/**
	 * @param ActionGenerique $action
	 * @param unknown $helper
	 */
	public function faiteAction($action, $helper){
		$connection  =  $this->container->get('database_connection');
		$sql         =  "UPDATE action a 
		                 inner join action_generique_has_action agh on agh.action_id=a.id and agh.actionGenerique_id = {$action->getId()}
				         set a.etat_reel =
		                 CASE
						 WHEN  a.date_initial <  NOW() THEN '" . Statut::ACTION_FAIT_DELAI . "'
						 WHEN  a.date_initial >= NOW() THEN '" . Statut::ACTION_FAIT_HORS_DELAI . "'
						 END, 
						 a.etat_courant   =
						 CASE
						 WHEN  a.date_initial <  NOW() THEN '" . Statut::ACTION_FAIT_DELAI . "'
						 WHEN  a.date_initial >= NOW() THEN '" . Statut::ACTION_FAIT_HORS_DELAI . "'
						 END
		                 where a.etat_courant !='".Statut::ACTION_ABANDONNEE."';";
		
		$sql        .=  "update action_generique 
				         set statut =  
				         CASE
						 WHEN  date_initial <  NOW() THEN '" . Statut::ACTION_FAIT_DELAI . "'
						 WHEN  date_initial >= NOW() THEN '" . Statut::ACTION_FAIT_HORS_DELAI . "'
						 END
						 where id={$action->getId()} ;";
		
		$connection->prepare($sql)->execute();
		/** @var ActionGeneriqueHasAction $agha */
		foreach ($action->getActionGeneriqueHasAction() as $agha){
			$act               = $agha->getAction();
			$emailPorteur      = ActionUtils::getEmailPorteur($this->em, $act);
			$emailManager      = ActionUtils::getEmailManager($this->em, $act);
			$emailContributeur = ActionUtils::getEmailContributeur($this->em, $act);
			$allEmailAnimateur = ActionUtils::getAllEmailAnimateur($this->em, $act);
			$subject           = 'Fin de traitement de l\'action';
			$commentaire       = 'L\'action: ' . $act->getReference() . ' a été traitée par ' . $this->user->getCompletNom() . '.
									   ' . $act->getAnimateur()->getNomComplet() . ' est invité à prendre en charge.';
			$emailPorteur = array($emailPorteur);
			if ($act->getInstance() && $act->getInstance()->getEspace()){
				$cc = array_merge($emailPorteur, $emailContributeur);
			}else{
				$cc = array_merge($emailPorteur, $emailManager, $emailContributeur, $allEmailAnimateur);
			}
			Notification::notificationWithCopy($helper, $subject, $allEmailAnimateur, $cc, $commentaire, $act->getActionStatut()->last());
		}
		$to = array($action->getPorteur()->getEmail());
		$cc = array($action->getAnimateur()->getEmail());
		$object      = 'Action générique :: Fin de traitement';
		$commentaire = "L'action générique {$action->getReference()} a été traité avec succés . Sa prise en charge sera effective lorsque toutes ses actions seront cloturées .";
		Notification::notifActionGenerique($helper, $object, $to, $cc , $commentaire, $action);
	}
	
	/**
	 * 
	 * @param ActionGenerique $action
	 * @param unknown $helper
	 */
	public function solderAction($action , $helper){
		$connection  =  $this->container->get('database_connection');
		$sql         =  "update action_generique
				         set statut =
				         CASE
						 WHEN  date_initial <  NOW() THEN '" . Statut::ACTION_SOLDEE_DELAI . "'
						 WHEN  date_initial >= NOW() THEN '" . Statut::ACTION_SOLDEE_HORS_DELAI . "'
						 END
						 where id={$action->getId()} ;";
		
		$connection->prepare($sql)->execute();
		$to = array($action->getPorteur()->getEmail());
		$cc = array($action->getAnimateur()->getEmail());
		$object      = 'Cloture Action Générique';
		$commentaire = "L'action générique {$action->getReference()} a été cloturée suite à la cloture des toutes les actions simples .";
		Notification::notifActionGenerique($helper, $object, $to, $cc , $commentaire, $action);
	}
	
	/**
	 * 
	 * @param ActionGenerique $action
	 * @param unknown $helper
	 */
	public function abandonAction($action , $helper){
		$action->getActionGeneriqueHasAction()->clear();
		$action->setStatut(Statut::ACTION_ABANDONNEE);
		$this->em->persist($action);
		$this->em->flush();
		$to = array($action->getPorteur()->getEmail());
		$cc = array($action->getAnimateur()->getEmail());
		$object      = 'Action générique abandonnée';
		$commentaire = "L'action générique {$action->getReference()} a été abandonnée et les actions qui y sont rattachées sont maintenant détachées.";
		Notification::notifActionGenerique($helper, $object, $to, $cc , $commentaire, $action);
	}
	
	
	
}
