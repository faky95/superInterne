<?php
namespace Orange\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Priorite
 *
 * @ORM\Table(name="priorite")
 * @ORM\Entity
 */
class Priorite
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
     * @ORM\Column(name="libelle", type="string", length=45, nullable=true)
     */
    private $libelle;
    
    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=45, nullable=true)
     */
    private $description;
    
    /**
     * @var string
     *
     * @ORM\Column(name="couleur", type="string", length=7, nullable=true)
     */
    private $couleur;
    
    
    /**
     * @var \Bu
     *
     * @ORM\ManyToOne(targetEntity="Bu")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="bu_id", referencedColumnName="id")
     * })
     *
     */
    private $bu;

	public function getId() {
		return $this->id;
	}
	

	public function getCouleur() {
		return $this->couleur;
	}
	public function setCouleur($couleur) {
		$this->couleur = $couleur;
		return $this;
	}
	public function getBu() {
		return $this->bu;
	}
	public function setBu($bu) {
		$this->bu = $bu;
		return $this;
	}
	public function getLibelle() {
		return $this->libelle;
	}
	public function setLibelle($libelle) {
		$this->libelle = $libelle;
		return $this;
	}
	public function getDescription() {
		return $this->description;
	}
	public function setDescription($description) {
		$this->description = $description;
		return $this;
	}
	
	public function __toString(){
		return $this->libelle;
	}

}
