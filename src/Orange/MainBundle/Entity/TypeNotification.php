<?php
namespace Orange\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TypeNotification
 * @ORM\Table(name="type_notification")
 * @ORM\Entity
 */
class TypeNotification
{
	
	/**
	 * @var array
	 */
	static $ids;
	
	/**
	 * @var array
	 */
	static $clibles;
	
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
	private $id;
	
	/**
	 * @var string
	 * @ORM\Column(name="libelle", type="string", length=45, nullable=false)
	 */
	private $libelle;
	
	/**
	 * @var number
	 * @ORM\Column(name="cible", type="smallint", nullable=false)
	 */
	private $cible;


    /**
     * Get id
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set libelle
     * @param string $libelle
     * @return TypeAction
     */
    public function setLibelle($libelle)
    {
    	$this->libelle= $libelle;

        return $this;
    }

    /**
     * Get libelle
     * @return string 
     */
    public function getLibelle()
    {
    	return $this->libelle;
    }
    
    /**
     * Set libelle
     * @param number $cible
     * @return TypeAction
     */
    public function setCible($cible)
    {
    	$this->cible= $cible;
    	return $this;
    }
    
    /**
     * Get libelle
     * @return string
     */
    public function getCible()
    {
    	return $this->cible;
    }
    
    /**
     * get libelle
     * @return string
     */
    public function __toString() {
    	return $this->libelle;
    }

}
