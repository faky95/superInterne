<?php
namespace Orange\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Pas
 * @ORM\Table(name="exception")
 * @ORM\Entity(repositoryClass="Orange\MainBundle\Repository\ExceptionRepository")
 */
class Exception
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
     * @ORM\Column(name="libelle", type="string", length=100,nullable=false)
     */
    private $libelle;
    
    /**
     * @var \Date
     * @ORM\Column(name="date_exception", type="date", nullable=true)
     */
    private $dateException;
    
    /**
     * @var \Integer
     * @ORM\Column(name="numero_jour", type="integer", nullable=true)
     */
    private $numeroJour;
    
    public function __toString()
    {	
    	return $this->libelle;
    }
	public function getId() {
		return $this->id;
	}
	public function getLibelle() {
		return $this->libelle;
	}
	public function setLibelle($libelle) {
		$this->libelle = $libelle;
		return $this;
	}
	public function getDateException() {
		return $this->dateException;
	}
	public function setDateException($dateException) {
		$this->dateException = $dateException;
		return $this;
	}
	public function getNumeroJour() {
		return $this->numeroJour;
	}
	public function setNumeroJour($numeroJour) {
		$this->numeroJour = $numeroJour;
		return $this;
	}
	public function getValeur() {
		return $this->valeur;
	}
	public function setValeur($valeur) {
		$this->valeur = $valeur;
		return $this;
	}
	
}
