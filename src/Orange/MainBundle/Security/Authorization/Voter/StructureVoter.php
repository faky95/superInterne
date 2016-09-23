<?php 

namespace Orange\MainBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\User\UserInterface;
use Orange\MainBundle\Entity\Structure;
use Orange\MainBundle\Entity\Utilisateur;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter;
use Symfony\Component\DependencyInjection\ContainerInterface;


class StructureVoter extends AbstractVoter
{
	
	const CREATE 	 = 'create';
	const READ 	 	 = 'read';
	const UPDATE 	 = 'update';
	const DELETE	 = 'delete';
	
	private $em;
	
	protected $container;
	
	public function __construct(EntityManager $em, ContainerInterface $container)
	{
		$this->em = $em;
		$this->container = $container;
	}
	
	protected function getSupportedAttributes()
	{
		return array(self::CREATE, self::READ, self::UPDATE, self::DELETE);
	}
	
	protected function getSupportedClasses()
	{
		return array('Orange\MainBundle\Entity\Structure');
	}
	
	protected function isGranted($attribute, $structure, $user = null)
	{
		$user = $this->container->get('security.context')->getToken()->getUser();
		
		// make sure there is a user object (i.e. that the user is logged in)
		if (!$user instanceof UserInterface) 
		{
			return false;
		}
		// double-check that the User object is the expected entity (this
		// only happens when you did not configure the security system properly)
		if (!$user instanceof Utilisateur) 
		{
			throw new \LogicException('The user is somehow not our User class!');
		}
		
		switch($attribute) 
		{
			case self::CREATE:
				// the data object could have for example a method isPrivate()
				// which checks the Boolean attribute $private
				if ($user->hasRole('ROLE_ADMIN') || $user->hasRole('ROLE_SUPER_ADMIN')) 
				{
					return true;
				}
				break;
			case self::READ:
				if ($this->isStructureAdministrateur($structure, $user) || $user->hasRole('ROLE_SUPER_ADMIN')) 
				{
					return true;
				}
				break;
			case self::UPDATE:
				if ($this->isStructureAdministrateur($structure, $user) || $user->hasRole('ROLE_SUPER_ADMIN')) 
				{
					return true;
				}
				break;
			case self::DELETE:
				
				break;
		}
		
		return false;
	
	}
	
	public function isStructureAdministrateur($structure, $user)
	{
		$result = false;
		if($user->getIsAdmin() && ($user->getStructure()->getBuPrincipal()->getId() === $structure->getBuPrincipal()->getId()))
		{
			$result = true;
		}
		return $result;
	}
	
}