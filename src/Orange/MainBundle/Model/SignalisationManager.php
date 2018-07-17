<?php
namespace Orange\MainBundle\Model;

use Orange\MainBundle\Utils\Notification;
use Orange\MainBundle\Entity\Statut;
use Orange\MainBundle\Entity\Signalisation;
use Orange\MainBundle\Utils\InstanceUtils;
use Orange\MainBundle\Entity\SignalisationStatut;
use Orange\MainBundle\Utils\SignalisationUtils;

class SignalisationManager
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
		$this->repository = $em->getRepository('OrangeMainBundle:SignalisationStatut');
		$this->helper = $this->container->get('orange.main.mailer');
	}
	
	//création signalisation
	public function createNewSignalisation($entity , $helper) {
		$instance = $entity->getInstance();
		$source = $entity->getSource();
		$destinataire = InstanceUtils::animateursEmail($this->em, $instance);
		$animateur = $instance->getAnimateur()->count()==0
		? $instance->getParent()->getAnimateur()->get(0)->getUtilisateur()->getNomComplet()
		: $instance->getAnimateur()->get(0)->getUtilisateur()->getNomComplet();
		$subject = 	   'Nouvelle Signalisation';
		$commentaire = 'Une nouvelle signalisation a été postée par '.$this->user->getCompletNom().' au périmétre: '.$instance->getLibelle().'. '
				.$animateur.' est prié de prendre en charge cette signalisation. ';
		Notification::notificationSignWithCopy($helper, $subject, $destinataire, array($source->getUtilisateur()->getEmail()), $commentaire, $entity);
	}
	
	/**
	 * fait traitement signalisation
	 * @param \Orange\MainBundle\Entity\Signalisation $entity
	 * @param unknown $helper
	 */
	public function faitTraitement($entity, $helper){
		$instance = $entity->getInstance();
		$source = $entity->getSource();
		$destinataire = InstanceUtils::animateursEmail($this->em, $instance);
		SignalisationUtils::addAnimateur ($this->em, $this->user, $entity);
		$subject = 'Fin de traitement de la signalisation';
		$commentaire = "La signalisation intitulée : <<" . $entity->getLibelle() . ">> vient d'être traitée. ".
				'Toutes les actions la concernant sont soldées, '.$source->getUtilisateur()->getCompletNom().' est invité à qualifier la signalisation.';
		Notification::notificationSignWithCopy($helper, $subject, $destinataire, $source->getUtilisateur()->getEmail(), $commentaire, $entity);
		$this->updateEtatSignalisation($this->em, Statut::FIN_TRAITEMENT_SIGNALISATION, $entity);
	}
	
	//prise en charge signalisation
	public function priseEnChargeSignalisation($entity, $helper){
		$source = $entity->getSignalisation()->getSource();
		$destinataire = InstanceUtils::animateursEmail($this->em, $entity->getInstance());
		SignalisationUtils::addAnimateur ($this->em, $this->user, $entity);
		$subject = 'Prise en charge de la signalisation';
		$commentaire = 'La signalisation intitulée : <<' . $entity->getLibelle() . '>> 
								a été prise en charge par '.$this->user->getCompletNom().' .';
		Notification::notificationSignWithCopy($helper, $subject, $destinataire, $source->getUtilisateur()->getEmail(), $commentaire, $entity->getSignalisation());
		$this->updateEtatSignalisation($this->em, Statut::SIGNALISATION_PRISE_CHARGE, $entity);
	}
	
	//non prise en charge signalisation
	public function signalisationNonPriseEnCharge($entity, $helper){
		$signalisationActeursEmail = SignalisationUtils::getSignalisationMembresEmail($this->em, $entity);
		SignalisationUtils::changeStatutSignalisation ( $this->em, $this->user, Statut::SIGNALISATION_CLOTURE, $entity->getSignalisation (), "Cette signalisation a été clôturé suite au refus de prise en charge." );
		$subject = 'Prise en charge de la signalisation';
		$commentaire = 'La prise en charge de la signalisation intitulée : << ' . $entity->getLibelle() . ' >>
								a été rejetée par '.$this->user->getCompletNom().' .';
		Notification::notificationSignWithCopy($helper, $subject, $signalisationActeursEmail, array(), $commentaire, $entity );
		$this->updateEtatSignalisation($this->em, Statut::SIGNALISATION_INVALIDER, $entity);
	}
	
	//signalisation efficace
	/**
	 * @param \Orange\MainBundle\Entity\Signalisation $entity
	 * @param unknown $helper
	 */
	public function signalisationEfficace($entity, $helper){
		$signalisationActeursEmail = SignalisationUtils::getSignalisationMembresEmail($this->em, $entity);
		$commentaire = $entity->getSignStatut()->last() ? $entity->getSignStatut()->last()->getCommentaire() : null;
		$subject = 'Evaluation de la signalisation ';
		$commentaire = 'La signalisation << '.$entity->getLibelle().'>> a été qualifiée efficace par '.$this->user->getCompletNom();
		Notification::notificationSignWithCopy($helper, $subject, $signalisationActeursEmail, array(), $commentaire, $entity );
		$this->updateEtatSignalisation($this->em, Statut::SIGNALISATION_TRAITE_EFFICACEMENT, $entity);
	}
	
	//signalisation non efficace
	public function signalisationNonEfficace($entity, $helper) {
		$signalisationActeursEmail = SignalisationUtils::getSignalisationRejeteMembresEmail($this->em, $entity);
		$commentaire = $entity->getSignStatut()->last() ? $entity->getSignStatut()->last()->getCommentaire() : null;
		$subject = 'Evaluation de la signalisation ';
		$commentaire = 'La signalisation <<'.$entity->getLibelle().'>> a été qualifiée non efficace par '.$this->user->getCompletNom().
				' . Il est demandé à  l\'animateur en charge de cette signalisation de revoir les actions mal traitées .  ';
		Notification::notificationSignWithCopy($helper, $subject, $signalisationActeursEmail, array(), $commentaire, $entity );
		$this->updateEtatSignalisation($this->em, Statut::SIGNALISATION_TRAITE_NON_EFFICACEMENT, $entity);
	}
	
	// reformulation signalisation
	public function reformulationSignalisation($entity, $helper){
		$instance = $entity->getInstance();
		//$source = $entity->getSource();
		$destinataire = InstanceUtils::animateursEmail($this->em, $instance);
// 		$animateur = $instance->getAnimateur()->count()==0
// 			? $instance->getParent()->getAnimateur()->get(0)->getUtilisateur()->getNomComplet()
// 			: $instance->getAnimateur()->get(0)->getUtilisateur()->getNomComplet();
		$subject     = 'Reformulation signalisation';
		$commentaire = 'La signalisation a été reformulée par '.$this->user->getCompletNom().'. En dessous les informations sur la reformulation. ';
		$this->updateEtatSignalisation($this->em, Statut::NOUVELLE_SIGNALISATION, $entity);
		Notification::notificationSignWithCopy($helper, $subject, $destinataire, array(), $commentaire, $entity );
	}
	
	
	public function updateEtatSignalisation($entityManager, $currentStatus, $entity) {
		$entity->setEtatCourant($currentStatus);
		$entityManager->persist($entity);
		$entityManager->flush();
	}
	
}
