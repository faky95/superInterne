<?php

namespace Orange\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TypeInstance
 *
 * @ORM\Table(name="type_instance")
 * @ORM\Entity
 */
class TypeInstance
{
	
	const INSTANCE_ACTION_SIGNALETIQUE = 'SIGNALETIQUE';
			
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
    
        /**
     * @var \Doctrine\Common\Collections\Collection
     *
     *  @ORM\OneToMany(targetEntity="Instance", mappedBy="typeInstance", cascade={"persist", "merge"})
     */
    private $instance;
    

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=20, nullable=true)
     */
    private $code;
    
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

    /**
     * Set libelle
     *
     * @param string $libelle
     * @return TypeInstance
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
     * Set code
     *
     * @param string $code
     * @return TypeInstance
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }
}
