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

	public function getId() {
		return $this->id;
	}
	public function getSymbole() {
		return $this->symbole;
	}
	public function setSymbole($symbole) {
		$this->symbole = $symbole;
		return $this;
	}
	public function getReferenceStatistique() {
		return $this->referenceStatistique;
	}
	public function setReferenceStatistique($referenceStatistique) {
		$this->referenceStatistique = $referenceStatistique;
		return $this;
	}
	public function getLibelleStatut() {
		return $this->libelleStatut;
	}
	public function setLibelleStatut($libelleStatut) {
		$this->libelleStatut = $libelleStatut;
		return $this;
	}
	
	public function __toString(){
		return $this->symbole;
	}
	
    
}
