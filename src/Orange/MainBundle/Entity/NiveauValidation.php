<?php

namespace Orange\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * NiveauValidation
 *
 * @ORM\Table(name="niveau_validation")
 * @ORM\Entity
 */
class NiveauValidation
{
	
	const INSTANCE 		= 'INSTANCE';
	const SERVICE 		= 'SERVICE';
	const DEPARTEMENT 	= 'DEPARTEMENT';
	const POLE 	 		= 'POLE';
	const DIRECTION 	= 'DIRECTION';
	
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
     * @ORM\Column(name="libelle", type="string", length=45, nullable=true)
     * @Assert\NotBlank()
     * 
     */
    private $libelle;
    
    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=45, nullable=true)
     * @Assert\NotBlank()
     *
     */
    private $code;

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
     * @return NiveauValidation
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
     * @return NiveauValidation
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
    
    public function __toString()
    {
    	return $this->libelle;
    }
}
