<?php
namespace Orange\MainBundle\Service;

use Symfony\Bundle\FrameworkBundle\Controller;
use Symfony\Component\Serializer\Encoder\ChainEncoder;

class StructureAction {
	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $em;
	
	protected $container;
	
	public function __construct($em, $container) {
		$this->container = $container;
		$this->em = $em;
	}
	
	public function setStructureForAction(){
		$actions = $this->em->getRepository('OrangeMainBundle:Action')->ActionWithStructureNull();
		foreach ($actions as $action){
			if(!$action->getInstance()->getEspace()){
					$action->setStructure($action->getPorteur()->getStructure());
					$this->em->persist($action);
			}
		}
		$this->em->flush();
	}
}