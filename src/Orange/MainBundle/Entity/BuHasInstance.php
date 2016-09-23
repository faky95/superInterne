<?php

namespace Orange\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Animateur Membre
 *
 * @ORM\Table(name="bu_has_instance")
 * @ORM\Entity(repositoryClass="Orange\MainBundle\Repository\BuHasInstanceRepository")
 */
class BuHasInstance {
	
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
	 * @var \Bu
	 *  @ORM\ManyToOne(targetEntity="Bu", inversedBy="bu")
	 *      @ORM\JoinColumns({
	 *      @ORM\JoinColumn(name="bu_id", referencedColumnName="id")
	 *      })
	 */
	private $bu;

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
	public function getBu() {
		return $this->bu;
	}
	public function setBu($bu) {
		$this->bu = $bu;
		return $this;
	}
	
	public function __toString(){
		return $this->instance->getLibelle();
	}
	
	
}
