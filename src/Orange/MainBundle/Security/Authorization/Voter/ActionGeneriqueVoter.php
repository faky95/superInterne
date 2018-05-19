<?php 
namespace Orange\MainBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\User\UserInterface;
use Orange\MainBundle\Entity\Action;
use Orange\MainBundle\Entity\Utilisateur;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Orange\MainBundle\Entity\Config;


class ActionGeneriqueVoter extends AbstractVoter {
	
	const CREATE 	 = 'create';
	const READ 	 	 = 'read';
	const LISTE 	 = 'liste';
	const UPDATE 	 = 'update';
	const DELETE	 = 'delete';
	const ORIENTER	 = 'orienter';
	const ABANDONNE  = 'abandonne';
	const FAITE      = 'faite'; 
	
	private $em;
	
	protected $container;
	
	public function __construct(EntityManager $em, ContainerInterface $container) {
		$this->em = $em;
		$this->container = $container;
	}
	
	protected function getSupportedAttributes() {
		return array(self::CREATE, self::READ, self::UPDATE, self::DELETE,self::LISTE,self::ORIENTER, self::ABANDONNE,self::FAITE);
	}
	
	protected function getSupportedClasses() {
		return array('Orange\MainBundle\Entity\ActionGenerique');
	}
	
	protected function isGranted($attribute, $action, $user = null) {
		$user = $this->container->get('security.context')->getToken()->getUser();
		// make sure there is a user object (i.e. that the user is logged in)
		if(!$user instanceof UserInterface) {
			return false;
		} elseif($user->hasRole('ROLE_SUPER_ADMIN')) {
			return true;
		}
		// double-check that the User object is the expected entity (this
		// only happens when you did not configure the security system properly)
		if (!$user instanceof Utilisateur) {
			throw new \LogicException('The user is somehow not our User class!');
		}
		
		switch($attribute) {
			case self::CREATE:
				if ($user->hasRole('ROLE_ADMIN') || $user->hasRole('ROLE_ANIMATEUR_ACTIONGENERIQUE')) {
					return true;
				}
			break;
			case self::READ:
				if ($user->getId() == $action->getPorteur()->getId() || $this->isAnimateur($action, $user) || $this->isBuAdministrateur($action, $user)) {
					return true;
				}
			break;
			case self::UPDATE:
				if ($user->hasRole('ROLE_ADMIN') || $user->getId()==$action->getAnimateur()->getId()) {
					return true;
				}
			break;
			case self::DELETE:
				if ($user->hasRole('ROLE_ADMIN') || $user->getId()==$action->getAnimateur()->getId()) {
					return true;
				}
			break;
			case self::LISTE:
				if($user->getStructure()->getBuPrincipal()->hasConfig(Config::BU_ACTION_GENERIQUE)==true){
					return true;
				}
			break;
			case self::ORIENTER:
				if ($user->getId() == $action->getPorteur()->getId()) {
					return true;
				}
			break;
			case self::ABANDONNE:
				if ($user->getId() == $action->getPorteur()->getId()) {
					return true;
				}
			break;
		    case self::FAITE:
					if ($user->getId() == $action->getPorteur()->getId()) {
						return true;
					}
			break;
		}
		return false;
	}
	
	public function isBuAdministrateur($action, $user) {
		return $user->getIsAdmin() && $user->getStructure()->getBuPrincipal()->hasConfig(Config::BU_ACTION_GENERIQUE);
	}
	
	public function isAnimateur($action, $user) {
		$result = false;
		if($user->hasRole(Utilisateur::ROLE_ANIMATEUR_ACTIONGENERIQUE) && $action->getAnimateur()->getId()==$user->getId()) {
			return true;
		}
		return $result;
	}
	
}