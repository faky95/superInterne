<?php

namespace Orange\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Animateur Membre
 *
 * @ORM\Table(name="instance_has_type_action")
 * @ORM\Entity(repositoryClass="Orange\MainBundle\Repository\InstanceHasTypeActionRepository")
 */
class InstanceHasTypeAction {
	
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
	 * @var \TypeAction
	 *  @ORM\ManyToOne(targetEntity="TypeAction", inversedBy="typeAction")
	 *      @ORM\JoinColumns({
	 *      @ORM\JoinColumn(name="type_action_id", referencedColumnName="id")
	 *      })
	 */
	private $typeAction;

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
	public function getTypeAction() {
		return $this->typeAction;
	}
	public function setTypeAction($typeAction) {
		$this->typeAction = $typeAction;
		return $this;
	}
	public function __toString(){
		return $this->typeAction->getType();
	}
	
}
