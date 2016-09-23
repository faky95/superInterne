<?php


namespace Orange\MainBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * TypeStatut
 *
 * @ORM\Table(name="type_statut")
 * @ORM\Entity
 */
class TypeStatut
{
	
	const TYPE_SIGNALISATION 	= 'SIGNALISATION';
	const TYPE_ACTION 		 	= 'ACTION';
	const TYPE_TACHE	     	= 'TACHE';
	
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
     * @ORM\Column(name="libelle", type="string", length=50, nullable=true)
     */
    private $libelle;
    
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
	

}
