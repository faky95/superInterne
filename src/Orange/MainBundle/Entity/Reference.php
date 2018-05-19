<?php


namespace Orange\MainBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Reference
 *
 * @ORM\Table(name="reference")
 * @ORM\Entity(repositoryClass="Orange\MainBundle\Repository\ReferenceRepository")
 */
class Reference
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
     * @ORM\Column(name="symbole", type="string", length=45)
     * 
     */
    private $symbole;
    
    /**
     * @var string
     *
     * @ORM\Column(name="reference_statistique", type="string", length=45)
     *
     */
    private $referenceStatistique;
    
    /**
     * @var string
     *
     * @ORM\Column(name="libelle_statut", type="string", length=45)
     *
     */
    private $libelleStatut;

    /**
     * get id
     * @return number
     */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * get symbol
	 * @return unknown
	 */
	public function getSymbole() {
		return $this->symbole;
	}
	
	/**
	 * set symbol
	 * @param unknown $symbole
	 * @return \Orange\MainBundle\Entity\Reference
	 */
	public function setSymbole($symbole) {
		$this->symbole = $symbole;
		return $this;
	}
	
	/**
	 * get referenceStatistique
	 * @return unknown
	 */
	public function getReferenceStatistique() {
		return $this->referenceStatistique;
	}
	
	/**
	 * set referenceStatistique
	 * @param string $referenceStatistique
	 * @return \Orange\MainBundle\Entity\Reference
	 */
	public function setReferenceStatistique($referenceStatistique) {
		$this->referenceStatistique = $referenceStatistique;
		return $this;
	}
	
	/**
	 * get libelleStatut
	 * @return string
	 */
	public function getLibelleStatut() {
		return $this->libelleStatut;
	}
	
	/**
	 * get libelleStatut
	 * @param string $libelleStatut
	 * @return \Orange\MainBundle\Entity\Reference
	 */
	public function setLibelleStatut($libelleStatut) {
		$this->libelleStatut = $libelleStatut;
		return $this;
	}
	
	public function __toString(){
		return $this->symbole;
	}
	
    
}
