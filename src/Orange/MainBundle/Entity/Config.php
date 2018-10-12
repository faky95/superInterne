<?php
namespace Orange\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Config
 * @ORM\Table(name="config")
 * @ORM\Entity
 */
class Config
{

	const PUBLIC_PAS 			= 'PUBLIC_PAS';
	const COORDONNATEUR 		= 'COORDONNATEUR';
	const MANAGER_CAN_DO_IT 	= 'MANAGER_CAN_DO_IT';
	const LINK_INSTEAD_OF_ERQ 	= 'LINK_INSTEAD_OF_ERQ';
	
	const BU_CONNEXION_AD   	= 'BU_CONNEXION_AD';
	const BU_ACTION_GENERIQUE   = 'BU_ACTION_GENERIQUE';
	
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    
    /**
     * @var string
     * @ORM\Column(name="code", type="string", length=25, nullable=false)
     */
    private $code;
    
    /**
     * @var string
     * @ORM\Column(name="libelle", type="string", length=100, nullable=false)
     */
    private $libelle;
    
    /**
     * @var string
     * @ORM\Column(name="description", type="string", length=255, nullable=false)
     */
    private $description;
	
	/**
	 * get id
	 * @return number
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * get code
	 * @return string
	 */
	public function getCode() {
		return $this->code;
	}
	
	/**
	 * set code
	 * @param string $code
	 * @return \Orange\MainBundle\Entity\Config
	 */
	public function setCode($code) {
		$this->code = $code;
		return $this;
	}
	
	/**
	 * get libelle
	 * @return string
	 */
	public function getLibelle() {
		return $this->libelle;
	}
	
	/**
	 * set libelle
	 * @param string $libelle
	 * @return \Orange\MainBundle\Entity\Config
	 */
	public function setLibelle($libelle) {
		$this->libelle = $libelle;
		return $this;
	}
	
	/**
	 * get description
	 */
	public function getDescription() {
		return $this->description;
	}
	
	/**
	 * set description
	 * @param string $description
	 * @return \Orange\MainBundle\Entity\Config
	 */
	public function setDescription($description) {
		$this->description = $description;
		return $this;
	}
    
}
