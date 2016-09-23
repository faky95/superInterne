<?php
namespace Orange\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ArchitectureStructure
 *
 * @ORM\Table(name="architecture_structure")
 * @ORM\Entity
 */
class ArchitectureStructure
{
	
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id", type="integer", nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private  $id;
	
	/**
	 * @var string
	 *
	 * @ORM\Column(name="service", type="string", length=255, nullable=true)
	 *
	 */
	private $service;
	
	/**
	 * @var string
	 *
	 * @ORM\Column(name="departement", type="string", length=255, nullable=true)
	 *
	 */
	private $departement;
	
	/**
	 * @var string
	 *
	 * @ORM\Column(name="pole", type="string", length=255, nullable=true)
	 *
	 */
	private $pole;
	
	/**
	 * @var string
	 *
	 * @ORM\Column(name="direction", type="string", length=255, nullable=true)
	 *
	 */
	private $direction;
	
	
	/**
	 * @var string
	 *
	 * @ORM\Column(name="societe", type="string", length=255, nullable=true)
	 *
	 */
	private $societe;
	
	 /**
	 * 
	 * @var Structure
	 * @ORM\OneToOne(targetEntity="Structure", inversedBy="architectureStructure", cascade={"persist","remove","merge"})
	 * @ORM\JoinColumn(name="structure_id", referencedColumnName="id", nullable=false)
	 */
	private $structure;
	
	
	public function getId() {
		return $this->id;
	}
	public function getStructure() {
		return $this->structure;
	}
	public function setStructure($structure) {
		$this->structure = $structure;
		return $this;
	}
	public function getService() {
		return $this->service;
	}
	public function setService($service) {
		$this->service = $service;
		return $this;
	}
	public function getDepartement() {
		return $this->departement;
	}
	public function setDepartement($departement) {
		$this->departement = $departement;
		return $this;
	}
	public function getPole() {
		return $this->pole;
	}
	public function setPole($pole) {
		$this->pole = $pole;
		return $this;
	}
	public function getDirection() {
		return $this->direction;
	}
	public function setDirection($direction) {
		$this->direction = $direction;
		return $this;
	}
	public function getSociete() {
		return $this->societe;
	}
	public function setSociete($societe) {
		$this->societe = $societe;
		return $this;
	}
	
	
	
	 
}
