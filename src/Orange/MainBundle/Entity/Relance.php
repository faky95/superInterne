<?php
namespace Orange\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TypeRelance
 * @ORM\Table(name="relance")
 * @ORM\Entity
 */
class Relance
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \BU
     *
     * @ORM\ManyToOne(targetEntity="Bu")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="bu_id", referencedColumnName="id")
     * })
     *
     */
    private $bu;
    
    /**
     * @var \Projet
     *
     * @ORM\ManyToOne(targetEntity="Projet")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="projet_id", referencedColumnName="id")
     * })
     *
     */
    private $projet;
    
    /**
     * @var \Espace
     *
     * @ORM\ManyToOne(targetEntity="Espace")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="espace_id", referencedColumnName="id")
     * })
     *
     */
    private $espace;
    
    
    /**
     * @var \Frequence
     *
     * @ORM\ManyToOne(targetEntity="Frequence")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="frequence_id", referencedColumnName="id")
     * })
     *
     */
    private $frequence;
    
    /**
     * @var \TypeRelance
     *
     * @ORM\ManyToOne(targetEntity="TypeRelance")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="type_relance_id", referencedColumnName="id")
     * })
     *
     */
    private $typeRelance;
    
    
    public function __toString(){
    	return $this->libelle;
    }
    


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
	public function getBu() {
		return $this->bu;
	}
	public function setBu($bu) {
		$this->bu = $bu;
		return $this;
	}
	public function getProjet() {
		return $this->projet;
	}
	public function setProjet($projet) {
		$this->projet = $projet;
		return $this;
	}
	public function getEspace() {
		return $this->espace;
	}
	public function setEspace($espace) {
		$this->espace = $espace;
		return $this;
	}
	public function getFrequence() {
		return $this->frequence;
	}
	public function setFrequence($frequence) {
		$this->frequence = $frequence;
		return $this;
	}
	public function getTypeRelance() {
		return $this->typeRelance;
	}
	public function setTypeRelance($typeRelance) {
		$this->typeRelance = $typeRelance;
		return $this;
	}
	
	

}
