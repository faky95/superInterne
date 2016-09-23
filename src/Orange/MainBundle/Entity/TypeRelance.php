<?php

namespace Orange\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * TypeRelance
 *
 * @ORM\Table(name="type_relance")
 * @ORM\Entity
 */
class TypeRelance
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
     * @ORM\Column(name="libelle", type="string", length=255, nullable=false)
     */
    private $libelle;

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
    
	public function getLibelle() {
		return $this->libelle;
	}
	
	public function setLibelle($libelle) {
		$this->libelle = $libelle;
		return $this;
	}
	

}
