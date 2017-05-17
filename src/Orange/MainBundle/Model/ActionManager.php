<?php
namespace Orange\MainBundle\Model;

use Orange\MainBundle\Entity\Action;
use Orange\MainBundle\Utils\Notification;
use Orange\MainBundle\Entity\Statut;
use Orange\MainBundle\Utils\ActionUtils;
use Doctrine\ORM\Event\LifecycleEventArgs;

class ActionManager
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
	public function createNewActionEspace($action , $helper) {
		$espace = $this->user->getMembreEspace()->get(0)->getEspace();
		$emailGestionnaire = ActionUtils::getEmailGestionnaire($this->em, $espace);
		$emailPorteur = ActionUtils::getEmailPorteur($this->em, $action);
		$emailContributeur = ActionUtils::getEmailContributeur($this->em, $action);
		$subject = 'Nouvelle Action '.$espace->getLibelle();
		$commentaire = 'L\'action: ' . $action->getReference() . ' vous a été affectée par '. $action->getAnimateur()->getNomComplet() . '.';
		$emailPorteur = array($emailPorteur);
		$cc = array_merge($emailGestionnaire, $emailContributeur);
		Notification::notificationForActionEspace($helper, $subject, $emailPorteur, $cc, $commentaire, $action);
		
	}
	
	//Création d'action
	
	public function createNewAction($action , $helper) {
		$emailPorteur = ActionUtils::getEmailPorteur($this->em, $action);
		$emailManager = ActionUtils::getEmailManager($this->em, $action);
		$emailContributeur = ActionUtils::getEmailContributeur($this->em, $action);
		$subject = 'Nouvelle Action';
		$commentaire = 'L\'action: ' . $action->getReference() . ' vous a été affectée par '. $action->getAnimateur()->getNomComplet() . '. Vous êtes invité à confirmer sa prise en charge.';
		$emailPorteur = array($emailPorteur);
		$emailAnimateur = array($action->getAnimateur()->getEmail());
		$cc = array_merge($emailManager, $emailAnimateur, $emailContributeur);
		Notification::notificationAction($helper, $subject, $emailPorteur, $cc, $commentaire, $action);
		
	}
	
	//Validation de la prise en charge d'une action
	public function validerAction($action, $helper){
		$emailPorteur = ActionUtils::getEmailPorteur($this->em, $action);
		$emailManager = ActionUtils::getEmailManager($this->em, $action);
		$emailContributeur = ActionUtils::getEmailContributeur($this->em, $action);
		$subject = 'Prise en charge de l\'action';
		$commentaire = 'La prise en charge de l\'action '. $action->getReference().' a été confirmée par '.$this->user->getCompletNom().
		'.';
		$this->updateEntityEtat($this->em, Statut::ACTION_NON_ECHUE, Statut::ACTION_NON_ECHUE, $action);
 		$emailPorteur = array($emailPorteur);
 		$emailAnimateur = $action->getAnimateur()->getEmail();
 		$cc = array_merge($emailPorteur, $emailManager, $emailContributeur);
		Notification::notificationAction($helper, $subject, $emailAnimateur, $cc, $commentaire, $action);
	}
	
	//cloturer une action 
	public function faiteAction($action, $helper){
		$emailPorteur = ActionUtils::getEmailPorteur($this->em, $action);
		$emailManager = ActionUtils::getEmailManager($this->em, $action);
		$emailContributeur = ActionUtils::getEmailContributeur($this->em, $action);
		$allEmailAnimateur = ActionUtils::getAllEmailAnimateur($this->em, $action);
		$subject = 'Fin de traitement de l\'action';
		$commentaire = 'L\'action: ' . $action->getReference() . ' a été traitée par ' . $this->user->getCompletNom() . '.
									   ' . $action->getAnimateur()->getNomComplet() . ' est invité à prendre en charge.';
		$emailPorteur = array($emailPorteur);
		if ($action->getInstance() && $action->getInstance()->getEspace()){
			$cc = array_merge($emailPorteur, $emailContributeur);
		}else{
			$cc = array_merge($emailPorteur, $emailManager, $emailContributeur, $allEmailAnimateur);
		}
		$statut = $action->getActionStatut()->last()->getStatut()->getCode();
		$this->updateEntityEtat($this->em, $statut, $statut, $action);
		Notification::notificationWithCopy($helper, $subject, $allEmailAnimateur, $cc, $commentaire, $action->getActionStatut()->last());
	}
	
	//solder une action
	public function clotureAction($action, $helper){
		$emailPorteur = ActionUtils::getEmailPorteur($this->em, $action);
		$emailManager = ActionUtils::getEmailManager($this->em, $action);
		$emailContributeur = ActionUtils::getEmailContributeur($this->em, $action);
		$allEmailAnimateur = ActionUtils::getAllEmailAnimateur($this->em, $action);
		$subject = 'Cloture de l\'action';
		$commentaire = '. L\'action a été soldée par ' . $this->user->getCompletNom() . ' ';
		$emailPorteur = array($emailPorteur);
		if ($action->getInstance() && $action->getInstance()->getEspace()){
			$cc = array_merge($emailContributeur, $allEmailAnimateur);
		}else{
			$cc = array_merge($allEmailAnimateur, $emailManager, $emailContributeur );
		}
		$statut = $action->getActionStatut()->last()->getStatut()->getCode();
		$this->updateEntityEtat($this->em, $statut, $statut, $action);
		Notification::notificationWithCopy($helper, $subject, $emailPorteur, $cc, $commentaire, $action->getActionStatut()->last());
	}
	 //Refus de solder
	public function pasSolderAction($action, $helper){
		$today = date('Y-m-d');
		$emailPorteur = ActionUtils::getEmailPorteur($this->em, $action);
		$emailManager = ActionUtils::getEmailManager($this->em, $action);
		$emailContributeur = ActionUtils::getEmailContributeur($this->em, $action);
		$allEmailAnimateur = ActionUtils::getAllEmailAnimateur($this->em, $action);
		$subject = 'Action non soldee';
		$commentaire = '. L\'action n\'a pas été soldée par ' . $this->user->getCompletNom() . '. Merci de retraiter l\'action';
		$emailPorteur = array($emailPorteur);
		if ($action->getInstance() && $action->getInstance()->getEspace()){
			$cc = array_merge($emailContributeur, $allEmailAnimateur);
		}else{
			$cc = array_merge($allEmailAnimateur, $emailManager, $emailContributeur );
		}
		$statut = Statut::ACTION_NON_ECHUE;
		if($today > $action->getDateInitial()->format('Y-m-d')){
			$statut = Statut::ACTION_ECHUE_NON_SOLDEE;
		}
		$action->getActionStatut()->last()->setStatut($this->em->getRepository('OrangeMainBundle:Statut')->findOneByCode($statut));
		$this->em->persist($action->getActionStatut()->last());
		$this->em->flush();
		$this->updateEntityEtat($this->em, $statut, $statut, $action);
		Notification::notificationWithCopy($helper, $subject, $emailPorteur, $cc, $commentaire, $action->getActionStatut()->last());
	}
	
	//demande d'abandon
	public function abandonAction($action, $helper){
		$statut = Statut::ACTION_DEMANDE_ABANDON;
		$emailPorteur = ActionUtils::getEmailPorteur($this->em, $action);
		$emailManager = ActionUtils::getEmailManager($this->em, $action);
		$emailContributeur = ActionUtils::getEmailContributeur($this->em, $action);
		$allEmailAnimateur = ActionUtils::getAllEmailAnimateur($this->em, $action);
		$emailAnimateur = ActionUtils::getEmailAnimateur($this->em, $action);
		$subject = 'Demande d\'abandon d\'action .';
		$commentaire = $this->user->getCompletNom() . ' a effectué une demande d\'abandon. '.$action->getAnimateur()->getNomComplet().' est invité(e) à donner une suite à cette demande. ';
		$emailPorteur = array($emailPorteur);
		if ($action->getInstance() && $action->getInstance()->getEspace()){
			$cc = array_merge($emailContributeur, $allEmailAnimateur);
		}else{
			$cc = array_merge($allEmailAnimateur, $emailManager, $emailContributeur );
		}
		$action->getActionStatut()->last()->getErq()->setType($this->types['demande_abandon']);
		$this->updateEntityEtat($this->em, $statut, $statut, $action);
		Notification::notificationWithCopy($helper, $subject, $emailAnimateur, $cc, $commentaire, $action->getActionStatut()->last());
	}
	
	//abandon accepte
	public function AbandonAccepteAction($action, $helper){
		$statut = Statut::ACTION_ABANDONNEE;
		$emailPorteur = ActionUtils::getEmailPorteur($this->em, $action);
		$emailManager = ActionUtils::getEmailManager($this->em, $action);
		$emailContributeur = ActionUtils::getEmailContributeur($this->em, $action);
		$allEmailAnimateur = ActionUtils::getAllEmailAnimateur($this->em, $action);
		$subject = 'Demande d\'abandon d\'action';
		$comment = '. La demande d\'abandon de l\'action '.$action->getReference().' a été acceptée par
									   ' . $this->user->getNomComplet(). ' ';
		if ($action->getInstance() && $action->getInstance()->getEspace()){
			$cc = array_merge($emailContributeur, $allEmailAnimateur);
		}else{
			$cc = array_merge($emailContributeur, $emailManager, $allEmailAnimateur);
		}
		$action->setDateCloture(new \DateTime());
		$this->updateEntityEtat($this->em, $statut, $statut, $action);
		Notification::notificationWithCopy($helper, $subject, $emailPorteur, $cc, $comment, $action->getActionStatut()->last());
	}
	
	//abandon refuse
	public function abandonRefuseAction($action, $helper){
		$today = date('Y-m-d');
		$emailPorteur = ActionUtils::getEmailPorteur($this->em, $action);
		$emailManager = ActionUtils::getEmailManager($this->em, $action);
		$emailContributeur = ActionUtils::getEmailContributeur($this->em, $action);
		$allEmailAnimateur = ActionUtils::getAllEmailAnimateur($this->em, $action);
		$emailAnimateur = ActionUtils::getEmailAnimateur($this->em, $action);
		$subject = 'Demande d\'abandon d\'action .';
		$statut = Statut::ACTION_NON_ECHUE;
		if($today > $action->getDateInitial()->format('Y-m-d')){
			$statut = Statut::ACTION_ECHUE_NON_SOLDEE;
		}
		$comment = '. La demande d\'abandon de l\'action '.$action->getReference().' a été refusée par
						' . $this->user->getNomComplet(). ' pour les raisons suivantes: "'.$action->getActionStatut()->last()->getCommentaire ().'"';
		$emailAnimateur = array($emailAnimateur);
		if ($action->getInstance() && $action->getInstance()->getEspace()){
			$cc = array_merge($emailContributeur, $allEmailAnimateur);
		}else{
			$cc = array_merge($emailManager, $emailContributeur, $allEmailAnimateur);
		}
		$action->getActionStatut()->last()->setStatut($this->em->getRepository('OrangeMainBundle:Statut')->findOneByCode($statut));
		$this->em->persist($action->getActionStatut()->last());
		$this->em->flush();
		$this->updateEntityEtat($this->em, $statut, $statut, $action);
		Notification::notificationWithCopy($helper, $subject, $emailPorteur, $cc, $comment, $action->getActionStatut()->last());
	}
	
	//demande report
	public function reportAction($action, $helper){
		$statut = Statut::ACTION_DEMANDE_REPORT;
		$emailPorteur = ActionUtils::getEmailPorteur($this->em, $action);
		$emailManager = ActionUtils::getEmailManager($this->em, $action);
		$emailContributeur = ActionUtils::getEmailContributeur($this->em, $action);
		$allEmailAnimateur = ActionUtils::getAllEmailAnimateur($this->em, $action);
		$emailAnimateur = ActionUtils::getEmailAnimateur($this->em, $action);
		$subject = 'Demande de report d\'action .';
		$commentaire = $this->user->getCompletNom(). ' a effectué(e) une demande de report d\'échéance pour les raisons suivantes :"'.$action->getReport()->last()->getDescription().
		'". '.$action->getAnimateur()->getNomComplet().' est invité(e) à donner une suite à cette demande. ';
		$emailPorteur = array($emailPorteur);
		if ($action->getInstance() && $action->getInstance()->getEspace()){
			$cc = array_merge($emailContributeur, $allEmailAnimateur, $emailPorteur);
		}else{
			$cc = array_merge($emailContributeur, $emailManager, $allEmailAnimateur, $emailPorteur);
		}
		$action->setEtatReel($statut);
		$this->em->persist($action);
		$this->em->flush();
		Notification::notificationWithCopy($helper, $subject, $emailAnimateur, $cc, $commentaire,  $action->getActionStatut()->last());
	}
	
	//report accepte
	public function reportAccepteAction($action, $helper){
		$today = date('Y-m-d');
		$dateReport = $action->getReport()->last()->getDate();
		$statut = Statut::ACTION_NON_ECHUE;
		if ($today > $dateReport->format('Y-m-d')){
			$statut = Statut::ACTION_ECHUE_NON_SOLDEE;
		}
		$emailPorteur = ActionUtils::getEmailPorteur($this->em, $action);
		$emailManager = ActionUtils::getEmailManager($this->em, $action);
		$emailContributeur = ActionUtils::getEmailContributeur($this->em, $action);
		$allEmailAnimateur = ActionUtils::getAllEmailAnimateur($this->em, $action);
		$subject = 'Demande de report d\'action';
		$comment = 'La demande de report d\'échéance a été acceptée.
										par ' . $this->user->getNomComplet().' ';
		$emailPorteur = array($emailPorteur);
		if ($action->getInstance() && $action->getInstance()->getEspace()){
			$cc = array_merge($emailContributeur, $allEmailAnimateur);
		}else{
			$cc = array_merge($emailContributeur, $allEmailAnimateur, $emailManager);
		}
		$action->getActionStatut()->last()->setStatut($this->em->getRepository('OrangeMainBundle:Statut')->findOneByCode($statut));
		$this->em->persist($action->getActionStatut()->last());
		$this->em->flush();
		$action->setDateInitial($action->getReport()->last()->getDate());
		$this->updateEntityEtat($this->em, $statut, $statut, $action);
		Notification::notificationWithCopy($helper, $subject, $emailPorteur, $cc, $comment, $action->getActionStatut()->last());
		
	}
	
	//report refuse
	public function reportRefuseAction($action, $helper){
		$today = date('Y-m-d');
		$emailPorteur = ActionUtils::getEmailPorteur($this->em, $action);
		$emailManager = ActionUtils::getEmailManager($this->em, $action);
		$emailContributeur = ActionUtils::getEmailContributeur($this->em, $action);
		$allEmailAnimateur = ActionUtils::getAllEmailAnimateur($this->em, $action);
		$subject = 'Demande de report d\'action .';
		$comment = '. La demande de report d\'échéance a été refusée
											par ' . $this->user->getNomComplet() . ' !, reprise de traitement de l\'action .';
		$statut = Statut::ACTION_NON_ECHUE;
		if($today > $action->getDateInitial()->format('Y-m-d')){
			$statut = Statut::ACTION_ECHUE_NON_SOLDEE;
		}
		if ($action->getInstance() && $action->getInstance()->getEspace()){
			$cc = array_merge($emailContributeur, $allEmailAnimateur);
		}else{
			$cc = array_merge($emailManager, $emailContributeur, $allEmailAnimateur);
		}
		$action->getActionStatut()->last()->setStatut($this->em->getRepository('OrangeMainBundle:Statut')->findOneByCode($statut));
		$this->em->persist($action->getActionStatut()->last());
		$this->em->flush();
		$this->updateEntityEtat($this->em, $statut, $statut, $action);
		Notification::notificationWithCopy($helper, $subject, $emailPorteur, $cc, $comment, $action->getActionStatut()->last());
	}
	
	//proposition porteur 
	public function propositionPorteurAction($action, $helper){
		$emailPorteur = ActionUtils::getEmailPorteur($this->em, $action);
		$emailManager = ActionUtils::getEmailManager($this->em, $action);
		$emailContributeur = ActionUtils::getEmailContributeur($this->em, $action);
		$emailAnimateur = ActionUtils::getEmailAnimateur($this->em, $action);
		$allEmailAnimateur = ActionUtils::getAllEmailAnimateur($this->em, $action);
		$subject = 'Contre proposition sur l\'action';
		$comment = '' . $this->user->getCompletNom() . ', a fait L\'amendement: "'. $action->getActionStatut()->last()->getCommentaire () . '".'
				. $action->getAnimateur()->getNomComplet() . ' est invité(e) à prendre en charge.';
		$emailPorteur = array($emailPorteur);
		if ($action->getInstance() && $action->getInstance()->getEspace()){
			$cc = array_merge($emailContributeur, $allEmailAnimateur);
		}else{
			$cc = array_merge($emailContributeur, $emailPorteur, $emailManager, $allEmailAnimateur);
		}
		$statut = Statut::EVENEMENT_VALIDATION_ANIMATEUR_ATTENTE;
		$action->getActionStatut()->last()->setStatut($this->em->getRepository('OrangeMainBundle:Statut')->findOneByCode($statut));
		$this->em->persist($action->getActionStatut()->last());
		$action->setEtatReel($statut);
		$action->setEtatCourant(Statut::EVENEMENT_INVALIDER);
		$this->em->persist($action);
		$this->em->flush();
		Notification::notificationWithCopy($helper, $subject, $emailAnimateur, $cc, $comment, $action->getActionStatut()->last());
	}
	
	//proposition animateur
	public function propositionAnimateurAction($action, $helper){
		$emailPorteur = ActionUtils::getEmailPorteur($this->em, $action);
		$emailManager = ActionUtils::getEmailManager($this->em, $action);
		$emailContributeur = ActionUtils::getEmailContributeur($this->em, $action);
		$emailAnimateur = ActionUtils::getEmailAnimateur($this->em, $action);
		$allEmailAnimateur = ActionUtils::getAllEmailAnimateur($this->em, $action);
		$manager = $action->getPorteur()->getSuperior();
		$subject = 'Contre proposition sur l\'action: Retour de l\'animateur ';
		$statut = Statut::EVENEMENT_VALIDATION_MANAGER_ATTENTE;
		$comment = $this->user->getCompletNom(). ' a refusé l\'amendement de '.$action->getPorteur()->getCompletNom().'
										. '.$manager->getCompletNom().'est invité(e) à arbitrer.';
		$emailPorteur = array($emailPorteur);
		$emailAnimateur = array($emailAnimateur);
		if ($action->getInstance() && $action->getInstance()->getEspace()){
			$cc = array_merge($emailContributeur, $emailPorteur, $allEmailAnimateur);
		}else{
			$cc = array_merge($emailContributeur, $emailPorteur, $emailManager, $allEmailAnimateur);
		}
		$action->getActionStatut()->last()->setStatut($this->em->getRepository('OrangeMainBundle:Statut')->findOneByCode($statut));
		$this->em->persist($action->getActionStatut()->last());
		$action->setEtatReel($statut);
		$action->setEtatCourant(Statut::EVENEMENT_INVALIDER);
		$this->em->persist($action);
		$this->em->flush();
		Notification::notificationWithCopy($helper, $subject, $manager->getEmail(), $cc, $comment, $action->getActionStatut()->last());
	}
	
	//validation animateur
	public function validationAnimateurAction($action, $helper){
		$emailPorteur = ActionUtils::getEmailPorteur($this->em, $action);
		$emailManager = ActionUtils::getEmailManager($this->em, $action);
		$emailContributeur = ActionUtils::getEmailContributeur($this->em, $action);
		$emailAnimateur = ActionUtils::getEmailAnimateur($this->em, $action);
		$allEmailAnimateur = ActionUtils::getAllEmailAnimateur($this->em, $action);
		$subject = 'Contre proposition sur l\'action: Retour de l\'animateur ';
		$comment = $this->user->getCompletNom() .' a validé l\'amendement et est invité(e) à modifier l\'action en conséquence.';
		$statut = Statut::ACTION_NON_ECHUE;
		$emailAnimateur = array($emailAnimateur);
		if ($action->getInstance() && $action->getInstance()->getEspace()){
			$cc = array_merge($allEmailAnimateur, $emailContributeur);
		}else{
			$cc = array_merge($allEmailAnimateur, $emailContributeur, $emailManager);
		}
		$action->getActionStatut()->last()->setStatut($this->em->getRepository('OrangeMainBundle:Statut')->findOneByCode($statut));
		$this->em->persist($action->getActionStatut()->last());
		$this->em->persist($action);
		$this->em->flush();
		$this->updateEntityEtat($this->em, $statut, $statut, $action);
		Notification::notificationWithCopy($helper, $subject, $emailPorteur, $cc, $comment,$action->getActionStatut()->last());
		
	}
	
	//Réassignation action
	public function reassignationAction($action, $helper){
		$subject = "Re assignation d'une action";
		$cc = $this->user->getEmail();
		$commentaire =  $this->user->getCompletNom() ." vous a ré assigné l'action ".$action->getReference().".Merci de la traiter.";
		Notification::notificationWithCopy($helper, $subject, $action->getPorteur()->getEmail(), $cc, $commentaire, $action->getActionStatut()->last());
	}
	
	//validation manager
	public function validationManagerAction($action, $helper){
		
	}
	public function updateEntityEtat($entityManager, $etatCourant, $etatReel, $entity) {
		$entity->setEtatCourant($etatCourant);
		$entity->setEtatReel($etatReel);
		$entityManager->persist($entity);
		$entityManager->flush();
	}
	
	//modification porteur
	/**
	 * 
	 * @param Action $action
	 * @param unknown $helper
	 * @param LifecycleEventArgs $args
	 */
	public function updateAction($action, $helper,$args){
		var_dump($args->getEntity()->getId());
		var_dump($action->getId());
		exit;
		if($args->hasChangedField('porteur')){
			exit('yes');
		}
	}
}
