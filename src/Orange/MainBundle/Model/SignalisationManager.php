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
		$signStatut = $this->repository->getStatut($entity->getId());
		Notification::notificationSignWithCopy($helper, $subject, $destinataire, array($source->getUtilisateur()->getEmail()), $commentaire, $signStatut[0]);
	}
	
	//prise en charge signalisation
	public function priseEnChargeSignalisation($entity, $helper){
		$instance = $entity->getSignalisation()->getInstance();
		$source = $entity->getSignalisation()->getSource();
		$destinataire = InstanceUtils::animateursEmail($this->em, $instance);
		SignalisationUtils::addAnimateur ($this->em, $this->user, $entity->getSignalisation());
		$subject = 'Prise en charge de la signalisation';
		$commentaire = 'La signalisation intitulée : <<' . $entity->getSignalisation()->getLibelle() . '>> 
								a été prise en charge par '.$this->user->getCompletNom().' .';
		Notification::notificationSignWithCopy($helper, $subject, $destinataire, $source->getUtilisateur()->getEmail(), $commentaire, $entity);
		$this->updateEtatSignalisation($this->em, Statut::SIGNALISATION_PRISE_CHARGE, $entity->getSignalisation());
	}
	
	//non prise en charge signalisation
	public function signalisationNonPriseEnCharge($entity, $helper){
		$signalisationActeursEmail = SignalisationUtils::getSignalisationMembresEmail($this->em, $entity->getSignalisation());
		SignalisationUtils::changeStatutSignalisation ( $this->em, $this->user, Statut::SIGNALISATION_CLOTURE, $entity->getSignalisation (), "Cette signalisation a été clôturé suite au refus de prise en charge." );
		$subject = 'Prise en charge de la signalisation';
		$commentaire = 'La prise en charge de la signalisation intitulée : << ' . $entity->getSignalisation()->getLibelle() . ' >>
								a été rejetée par '.$this->user->getCompletNom().' . Cette signalisation a été cloturée par le système. ';
		Notification::notificationWithCopy($helper, $subject, $signalisationActeursEmail, array(), $commentaire, $entity );
		$this->updateEtatSignalisation($this->em, Statut::SIGNALISATION_INVALIDER, $entity->getSignalisation());
	}
	
	//signalisation efficace
	public function signalisationEfficace($entity, $helper){
		$signalisationActeursEmail = SignalisationUtils::getSignalisationMembresEmail($this->em, $entity->getSignalisation());
		$subject = 'Evaluation de la signalisation ';
		$commentaire = 'La signalisation << '.$entity->getSignalisation()->getLibelle().'>> a été qualifiée efficace par sa source '.$this->user->getCompletNom().' .
								ce dernier a laissé le commentaire suivant : '.$entity->getCommentaire().'
								Cette signalisation est clôturée ';
		Notification::notificationWithCopy($helper, $subject, $signalisationActeursEmail, array(), $commentaire, $entity );
		$this->updateEtatSignalisation($this->em, Statut::SIGNALISATION_TRAITE_EFFICACEMENT, $entity->getSignalisation());
	}
	
	//signalisation non efficace
	public function signalisationNonEfficace($entity, $helper){
		$signalisationActeursEmail = SignalisationUtils::getSignalisationRejeteMembresEmail($this->em, $entity->getSignalisation());
		$subject = 'Evaluation de la signalisation ';
		$commentaire = 'La signalisation <<'.$entity->getSignalisation()->getLibelle().'>> a été qualifiée non efficace par sa source '.$this->user->getCompletNom().' .
								qui a laissé le commentaire suivant :'.$entity->getCommentaire().'. Il est demandé à  l\'animateur en charge de cette signalisation de se connecter pour recharger les actions mal traitées .  ';
		Notification::notificationWithCopy($helper, $subject, $signalisationActeursEmail, array(), $commentaire, $entity );
		$this->updateEtatSignalisation($this->em, Statut::SIGNALISATION_TRAITE_NON_EFFICACEMENT, $entity->getSignalisation());
	}
	
	
	public function updateEtatSignalisation($entityManager, $currentStatus, $entity) {
		$entity->setEtatCourant($currentStatus);
		$entityManager->persist($entity);
		$entityManager->flush();
	}
	
}
