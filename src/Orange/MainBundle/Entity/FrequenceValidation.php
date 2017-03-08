<?php
namespace Orange\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Pas
 * @ORM\Table(name="frequence_validation")
 * @ORM\Entity()
 */
class FrequenceValidation
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;
    
    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=100,nullable=false)
     */
    private $libelle;
    /**
     * @var \Integer
     * @ORM\Column(name="nbr_heure", type="integer", nullable=true)
     */
    private $nbrHeure;
    
    public function __toString()
    {	
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
	public function getNbrHeure() {
		return $this->nbrHeure;
	}
	public function setNbrHeure($nbrHeure) {
		$this->nbrHeure = $nbrHeure;
		return $this;
	}
	

	
	
}
