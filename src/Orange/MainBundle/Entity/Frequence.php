<?php
namespace Orange\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Priorite
 *
 * @ORM\Table(name="frequence")
 * @ORM\Entity
 */
class Frequence
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
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=255, nullable=true)
     */
    private $libelle;
    
    /**
     * @var string
     *
     * @ORM\Column(name="jours", type="string", length=45, nullable=true)
     */
    private $jours;
    
    public function __toString(){
    	return $this->libelle;
    }
    
    
    
	public function getId() {
		return $this->id;
	}
	
	
	public function getLibelle() {
		return $this->libelle;
	}
	public function setLibelle($libelle) {
		$this->libelle = $libelle;
		return $this;
	}
	
	public function getJours() {
		return $this->jours;
	}
	public function setJours($jours) {
		$this->jours = $jours;
		return $this;
	}
	
	
	

}
