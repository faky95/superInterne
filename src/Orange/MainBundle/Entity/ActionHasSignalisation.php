<?php

namespace Orange\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Animateur Membre
 *
 * @ORM\Table(name="action_has_signalisation")
 * @ORM\Entity(repositoryClass="Orange\MainBundle\Repository\ActionHasSignalisationRepository")
 */
class ActionHasSignalisation {
	
	/**
	 *
	 * @var integer @ORM\Column(name="id", type="integer", nullable=false)
	 *      @ORM\Id
	 *      @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;
	
	/**
	 *
	 * @var \Action
	 * @ORM\ManyToOne(targetEntity="Action", inversedBy="action")
	 *      @ORM\JoinColumns({
	 *      @ORM\JoinColumn(name="action_id", referencedColumnName="id")
	 *      })
	 */
	private $action;
	
	/**
	 *
	 * @var \Signalisation
	 *  @ORM\ManyToOne(targetEntity="Signalisation", inversedBy="signalisation")
	 *      @ORM\JoinColumns({
	 *      @ORM\JoinColumn(name="signalisation_id", referencedColumnName="id")
	 *      })
	 */
	private $signalisation;

	public function getId() {
		return $this->id;
	}
	public function setId($id) {
		$this->id = $id;
		return $this;
	}
	public function getAction() {
		return $this->action;
	}
	public function setAction($action) {
		$this->action = $action;
		return $this;
	}
	public function getSignalisation() {
		return $this->signalisation;
	}
	public function setSignalisation($signalisation) {
		$this->signalisation = $signalisation;
		return $this;
	}
	
	
}
