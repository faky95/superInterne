<?php

namespace Orange\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Periodicite
 *
 * @ORM\Table(name="periodicite")
 * @ORM\Entity(repositoryClass="Orange\MainBundle\Repository\PeriodiciteRepository")
 */
class Periodicite 
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
     * @ORM\Column(name="libelle", type="string", length=45, nullable=false)
     */
    private $libelle;



    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
    	return $this->id;
    }
    
    /**
     * Set libelle
     *
     * @param string $libelle
     * @return Periodicite
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get libelle
     *
     * @return string 
     */
    public function getLibelle()
    {
        return $this->libelle;
    }


    /**
     * Get libelle
     *
     * @return string
     */
    public function __toString()
    {
    	return $this->libelle;
    }
    
    
}
