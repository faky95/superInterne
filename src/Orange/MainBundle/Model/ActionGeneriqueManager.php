<?php

namespace Orange\MainBundle\Model;

use Orange\MainBundle\Entity\Action;
use Orange\MainBundle\Entity\Statut;
use Orange\MainBundle\Utils\ActionUtils;
use Orange\MainBundle\Entity\ActionGenerique;
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
		$cc = null;
		$object     = 'Nouvelle Action Générique';
		$commentaire = "L\'action générique : {$action->getReference()}
		                a été créé par {$action->getAnimateur()->getNomComplet()} .";
		Notification::notifActionGenerique($helper, $object, $to, $cc , $commentaire, $action);
	}
	
	//modification d'action
	
	public function modifyAction($action , $helper) {
	
	}
	

	//solder une action
	public function clotureAction($action, $helper){
	}
	
	
	
	
}
