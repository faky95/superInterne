<?php

namespace Orange\MainBundle\Service;

use Orange\MainBundle\Entity\Statut;
use Orange\MainBundle\Entity\Action;
use Orange\MainBundle\Utils\ActionUtils;
use Orange\MainBundle\Utils\SignalisationUtils;
use Symfony\Component\Validator\Constraints\DateTime;
use Orange\MainBundle\Utils\Notification;
use Orange\MainBundle\Query\ActionQuery;
class ActionCorrect {
	/**
	 * @var \Orange\MainBundle\Entity\Utilisateur
	 */
	private $user;
        
        /**
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $em;
	
	/**
	 * @param \Symfony\Component\Security\Core\SecurityContext $security_context        	
	 */
	public function __construct($security_context, $em, $helper) {
		$this->user = $security_context->getToken()->getUser();
        $this->em = $em;
        $this->helper = $helper;
	}
	
	/**
	 * @param \Orange\MainBundle\Entity\Action $entity        	
	 * @return string
	 */
	public function createActionCorrective($entity) {
		$query = new ActionQuery($this->em->getConnection());
		$table = 'action_has_signalisation';
		$query->updateId($table);
		$action = new Action();
		$date = new \DateTime();
		$action->setDateAction($date);
		$action->setDateDebut($date);
		$action->setLibelle($entity->getLibelle());
		$action->setDescription($entity->getDescription());
		$action->setInstance($entity->getInstance()->getParent());
		$action->setDomaine($entity->getDomaine());
		$action->setTypeAction($entity->getTypeSignalisation());
		$action->setPorteur($this->user);
		$action->setAnimateur($entity->getInstance()->getParent()->getAnimateur()->get(0)->getUtilisateur());
		$interval = new \DateInterval('P2W');
		$date->add($interval);
		$action->setDateInitial($date);
		$this->em->persist($action);
		$this->em->flush();
		$query->insertActionSign($action->getId(), $entity->getId());
		ActionUtils::setReferenceActionSignalisation($this->em, $action, $entity);
		SignalisationUtils::changeStatutSignalisation($this->em, $this->user, Statut::TRAITEMENT_SIGNALISATION, $entity, 'Une action corrective a été ajoutée pour traiter cette signalisation');
		ActionUtils::changeStatutAction($this->em, $action, Statut::ACTION_NON_ECHUE, $this->user, "Nouvelle action corrective créée par ".$this->user->getNomComplet());
		$subject = 'Nouvelle Action';
		$commentaire = 'L\'action: ' . $action->getReference() . ' vous a été affectée par '. $action->getAnimateur()->getNomComplet() . '. Vous êtes invité à prendre en charge.';
		
		Notification::notificationActionSignalisation($this->helper, $subject, $this->user->getEmail(),$entity->getSource()->getUtilisateur()->getEmail(),$commentaire, $action);
	}
	
}
