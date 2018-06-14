<?php 
namespace Orange\MainBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\User\UserInterface;
use Orange\MainBundle\Entity\Action;
use Orange\MainBundle\Entity\Utilisateur;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter;
use Symfony\Component\DependencyInjection\ContainerInterface;


class ActionVoter extends AbstractVoter {
	
	const CREATE 	 = 'create';
	const READ 	 	 = 'read';
	const UPDATE 	 = 'update';
	const DELETE	 = 'delete';
	const IMPORT	 = 'import';
	
	private $em;
	
	protected $container;
	
	public function __construct(EntityManager $em, ContainerInterface $container) {
		$this->em = $em;
		$this->container = $container;
	}
	
	protected function getSupportedAttributes() {
		return array(self::CREATE, self::READ, self::UPDATE, self::DELETE, self::IMPORT);
	}
	
	protected function getSupportedClasses() {
		return array('Orange\MainBundle\Entity\Action');
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
				// the data object could have for example a method isPrivate()
				// which checks the Boolean attribute $private
				if ($user->hasRole('ROLE_ANIMATEUR') || $user->hasRole('ROLE_ADMIN')) {
					return true;
				}
				break;
			case self::READ:
				// S'il est le porteur désigné de l'
				if(($user->getId() === $action->getPorteur()->getId()) || ($this->isInstanceAnimateur($action, $user)) || ($this->isBuAdministrateur($action, $user)) || ($this->isGestionnaireEspace($action, $user)) || ($this->isContributeurOfAction($action, $user))) {
					return true;
				}
				// S'il est un manager ou ++ de la structure
				if($user->hasRole('ROLE_MANAGER')) {
					$structure = $action->getPorteur()->getStructure();
					$manager = $this->em->getRepository('OrangeMainBundle:Utilisateur')->findBy(array('manager' => true, 'structure' => $structure->getId()));
					if($manager !== NULL) {
						$managerPlusOne = $this->em->getRepository('OrangeMainBundle:Utilisateur')->findBy(array('manager' => true, 'structure' => $structure->getParent()->getId()));
						if($managerPlusOne !== NULL) {
							$managerPlusTwo = $this->em->getRepository('OrangeMainBundle:Utilisateur')->findBy(array('manager' => true,
												'structure' => $structure->getParent()->getParent()?$structure->getParent()->getParent()->getId():$structure->getParent()->getId()));
						}
						if($manager || $managerPlusOne || $managerPlusTwo) {
							return true;
						}
					}
				}
				break;
			case self::UPDATE:
				if($this->isInstanceAnimateur($action, $user) || $this->isBuAdministrateur($action, $user)) {
					return true;
				}
				break;
			case self::DELETE:
				if($this->isInstanceAnimateur($action, $user) || $this->isBuAdministrateur($action, $user)) {
					return true;
				}
				break;
			case self::IMPORT:
				// the data object could have for example a method isPrivate()
				// which checks the Boolean attribute $private
				if ($user->hasRole('ROLE_ANIMATEUR') || $user->hasRole('ROLE_ADMIN')) {
					return true;
				}
				break;
		}
		return false;
	}
	
	public function isBuAdministrateur($action, $user) {
		$result = false;
		$buPrincipal = $user->getStructure()->getBuPrincipal();
		if($user->getIsAdmin() && (($buPrincipal->getId() === $action->getStructure()->getBuPrincipal()->getId())
				|| $action->getInstance()->in_bu($buPrincipal->getId()))) {
			$result = true;
		}
		return $result;
	}
		
	public function isInstanceAnimateur($action, $user) {
		$result = false;
		if($user->hasRole('ROLE_ANIMATEUR')) {
			$instance = $action->getInstance();
			$userAnimateur = $this->em->getRepository('OrangeMainBundle:Animateur')->findBy(array('utilisateur' => $user->getId(), 'instance' => $instance->getId()));
			if($userAnimateur !== NULL) {
				$result = true;
			}
		}
		return $result;
	}
	
	public function isGestionnaireEspace($action, $user) {
		$result = false;
		if($user->hasRole(Utilisateur::ROLE_GESTIONNAIRE_ESPACE)) {
			$espace = $action->getInstance()->getEspace() ? $action->getInstance()->getEspace() : null;
			if($espace!=null) {
				$membre = $this->em->getRepository('OrangeMainBundle:MembreEspace')->findOneBy(array('utilisateur' => $user, 'espace' => $espace));
				if($membre !== NULL && $membre->getIsGestionnaire()==true) {
					$result = true;
				}
			}
		}
		return $result;
	}
	
	public function isContributeurOfAction($action, $user) {
		$result = false;
		if($user->hasRole(Utilisateur::ROLE_CONTRIBUTEUR)) {
			$contributeur = $this->em->getRepository('OrangeMainBundle:Contributeur')->findBy(array('utilisateur' => $user->getId(), 'action' => $action->getId()));
			if($contributeur!=null)
					$result = true;
		}
		return $result;
	}
	
}