<?php

namespace Orange\MainBundle\Service;

use Symfony\Bundle\FrameworkBundle\Controller;
use Symfony\Component\Serializer\Encoder\ChainEncoder;
use Orange\MainBundle\Entity\Structure;
use Orange\MainBundle\Entity\ArchitectureStructure;

class UpdateStructure {
	/**
	 *
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $em;
	protected $container;
	public function __construct($em, $container) {
		$this->container = $container;
		$this->em = $em;
	}
	public function setStructureForAction() {
		$entity = new ArchitectureStructure ();
		$structs = $this->em->getRepository ( 'OrangeMainBundle:Structure' )->StructureToUpdate ();
		foreach ( $structs as $struct ) {
			if ($struct->getTypeStructure () && $struct->getTypeStructure ()->getId () == 3) {
				$entity = new ArchitectureStructure ();
				$entity->setStructure ( $struct );
				$entity->setService ( $struct->getLibelle () );
				if ($struct->getParent () && $struct->getParent ()->getTypeStructure () && $struct->getParent ()->getTypeStructure ()->getId () == 2) {
					$entity->setDepartement ( $struct->getParent ()->getLibelle () );
					if ($struct->getParent () && $struct->getParent ()->getParent() && $struct->getParent ()->getParent ()->getTypeStructure () && $struct->getParent ()->getParent ()->getTypeStructure ()->getId () == 4) {
						$entity->setPole ( $struct->getParent ()->getParent ()->getLibelle () );
						$entity->setDirection ( $struct->getParent ()->getParent ()->getParent ()->getLibelle () );
					} elseif ($struct->getParent () && $struct->getParent ()->getParent() && $struct->getParent ()->getParent ()->getTypeStructure () && $struct->getParent ()->getParent ()->getTypeStructure ()->getId () == 1) {
						$entity->setDirection ( $struct->getParent ()->getParent ()->getLibelle () );
					}
				} elseif ($struct->getParent () && $struct->getParent ()->getTypeStructure () && $struct->getParent ()->getTypeStructure ()->getId () == 4) {
					$entity->setPole ( $struct->getParent ()->getLibelle () );
					$entity->setDirection ( $struct->getParent ()->getParent ()->getLibelle () );
				} elseif ($struct->getParent () && $struct->getParent ()->getTypeStructure () && $struct->getParent ()->getTypeStructure ()->getId () == 1) {
					$entity->setDirection ( $struct->getParent ()->getLibelle () );
				}
				$this->em->persist ( $entity );
			}
			if ($struct->getTypeStructure () && $struct->getTypeStructure ()->getId () == 2) {
				$entity = new ArchitectureStructure ();
				$entity->setStructure ( $struct );
				$entity->setDepartement ( $struct->getLibelle () );
				if ($struct->getParent () && $struct->getParent ()->getTypeStructure () && $struct->getParent ()->getTypeStructure ()->getId () == 4) {
					$entity->setPole ( $struct->getParent ()->getLibelle () );
					$entity->setDirection ( $struct->getParent ()->getParent ()->getLibelle () );
				} elseif ($struct->getParent () && $struct->getParent ()->getTypeStructure () && $struct->getParent ()->getTypeStructure ()->getId () == 1) {
					$entity->setDirection ( $struct->getParent ()->getLibelle () );
				}
				$this->em->persist ( $entity );
			}
			if ($struct->getTypeStructure () && $struct->getTypeStructure ()->getId () == 4) {
				$entity = new ArchitectureStructure ();
				$entity->setStructure ( $struct );
				$entity->setPole ( $struct->getLibelle () );
				if ($struct->getParent () && $struct->getParent ()->getTypeStructure () && $struct->getParent ()->getTypeStructure ()->getId () == 1) {
					$entity->setDirection ( $struct->getParent ()->getLibelle () );
				}
				$this->em->persist ( $entity );
			}
			if ($struct->getTypeStructure () && $struct->getTypeStructure ()->getId () == 1) {
				$entity = new ArchitectureStructure ();
				$entity->setStructure ( $struct );
				$entity->setDirection ( $struct->getLibelle () );
				$this->em->persist ( $entity );
			}
		}
		$this->em->flush ();
	}
}