<?php

namespace Orange\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Animateur Membre
 *
 * @ORM\Table(name="instance_has_domaine")
 * @ORM\Entity(repositoryClass="Orange\MainBundle\Repository\InstanceHasDomaineRepository")
 */
class InstanceHasDomaine {
	
	/**
	 *
	 * @var integer @ORM\Column(name="id", type="integer", nullable=false)
	 *      @ORM\Id
	 *      @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;
	
	/**
	 *
	 * @var \Instance 
	 * @ORM\ManyToOne(targetEntity="Instance", inversedBy="instance")
	 *      @ORM\JoinColumns({
	 *      @ORM\JoinColumn(name="instance_id", referencedColumnName="id")
	 *      })
	 */
	private $instance;
	
	/**
	 *
	 * @var \Domaine
	 *  @ORM\ManyToOne(targetEntity="Domaine", inversedBy="domaine")
	 *      @ORM\JoinColumns({
	 *      @ORM\JoinColumn(name="domaine_id", referencedColumnName="id")
	 *      })
	 */
	private $domaine;

	public function getId() {
		return $this->id;
	}
	public function setId($id) {
		$this->id = $id;
		return $this;
	}
	public function getInstance() {
		return $this->instance;
	}
	public function setInstance($instance) {
		$this->instance = $instance;
		return $this;
	}
	public function getDomaine() {
		return $this->domaine;
	}
	public function setDomaine($domaine) {
		$this->domaine = $domaine;
		return $this;
	}
	
	public function __toString(){
		return $this->domaine->getLibelleDomaine();
	}
	
	
	
	
}
