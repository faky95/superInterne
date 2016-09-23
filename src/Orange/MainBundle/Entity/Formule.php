<?php
namespace Orange\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Formule
 *
 * @ORM\Table(name="formule")
 * @ORM\Entity(repositoryClass="Orange\MainBundle\Repository\FormuleRepository")
 */
class Formule
{
	
	/**
	 * @var string $num
	 */
	protected $num;

	/**
	 * @var string $num
	 */
	protected $denom;
	
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
     * @ORM\Column(name="numerateur", type="string", length=45)
     * 
     */
    private $numerateur;
    
    /**
     * @var string
     * @ORM\Column(name="denominateur", type="string", length=45)
     */
    private $denominateur;
    
    /**
     * @var string
     * @ORM\Column(name="couleur", type="string", length=45, nullable=true)
     *
     */
    private $couleur;
    
    /**
     * @var string
     * @ORM\Column(name="libelle", type="string", length=45)
     */
    private $libelle;

    /**
     * @var Bu
     * @ORM\ManyToOne(targetEntity="Bu")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="bu_id", referencedColumnName="id")
     * })
     */
    private $bu;
    
    /**
     * @var boolean
     * @ORM\Column(name="visibilite", type="boolean", nullable=true)
     */
    private $visibilite;
    
    /**
     * @return integer
     */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * @return string
	 */
	public function getNumerateur() {
		return $this->numerateur;
	}
	
	/**
	 * @param string $numerateur
	 * @return Formule
	 */
	public function setNumerateur($numerateur) {
		$this->numerateur = $numerateur;
		return $this;
	}
	
	/**
	 * @return string
	 */
	public function getLibelle() {
		return $this->libelle;
	}
	
	/**
	 * @param string $libelle
	 */
	public function setLibelle($libelle) {
		$this->libelle = $libelle;
		return $this;
	}
	
	/**
	 * @return Bu
	 */
	public function getBu() {
		return $this->bu;
	}
	
	/**
	 * @param Bu $bu
	 * @return Formule
	 */
	public function setBu($bu) {
		$this->bu = $bu;
		return $this;
	}
	
	/**
	 * @return string
	 */
	public function getNum() {
		return $this->num;
	}
	
	/**
	 * @param string $num
	 * @return Formule
	 */
	public function setNum($num) {
		$this->num = $num;
		return $this;
	}
	
	/**
	 * @return string
	 */
	public function getDenom() {
		return $this->denom;
	}
	
	/**
	 * @param string $denom
	 * @return Formule
	 */
	public function setDenom($denom) {
		$this->denom = $denom;
		return $this;
	}
	
	/**
	 * @return string
	 */
	public function getDenominateur() {
		return $this->denominateur;
	}
	
	/**
	 * @param string $denom
	 * @return Formule
	 */
	public function setDenominateur($denominateur) {
		$this->denominateur = $denominateur;
		return $this;
	}
	
	/**
	 * @return boolean
	 */
	public function getVisibilite() {
		return $this->visibilite;
	}
	
	/**
	 * @param boolean $visibilite
	 * @return Formule
	 */
	public function setVisibilite($visibilite) {
		$this->visibilite = $visibilite;
		return $this;
	}
	
	public function __toString() {
		return $this->libelle;
	}
	public function getCouleur() {
		return $this->couleur;
	}
	public function setCouleur($couleur) {
		$this->couleur = $couleur;
		return $this;
	}
	
	
	
}
