<?php

namespace Orange\MainBundle\Service;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\Parser;
use Gedmo\DoctrineExtensions;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\SecurityContext;

use Orange\MainBundle\Entity\Action;
use Orange\MainBundle\Entity\Signalisation;

class Security 
{
	
	protected $container;
	
	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $em;
	
	protected $securityContext;
	
	public function __construct($container, EntityManager $em, SecurityContext $securityContext)
	{
		$this->container = $container;
		$this->em = $em;
		$this->securityContext = $securityContext;
	}
	
	/**
	 * General security checker for both
	 * Action and Signalisation
	 */
	
	public function createChecker($entity, $user)
	{
		if($entity instanceof Action)
		{
			
		}
		elseif ($entity instanceof Signalisation)
		{
			
		}
	}
	
	public function updateChecker($entity, $user)
	{
	
	}
	
	public function deleteChecker($entity, $user)
	{
		
	}
	
	public function readChecker($entity, $user)
	{
		
	}
	
	
}	