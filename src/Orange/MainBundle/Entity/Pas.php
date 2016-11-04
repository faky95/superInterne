<?php
namespace Orange\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Pas
 * @ORM\Table(name="pas")
 * @ORM\Entity(repositoryClass="Orange\MainBundle\Repository\PasRepository")
 */
class Pas 
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
     * @var integer
     *
     * @ORM\Column(name="valeur", type="integer", nullable=false)
     */
    private $valeur;
    
    /**
     * @ORM\ManyToOne(targetEntity="Orange\MainBundle\Entity\Periodicite", inversedBy="pas")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="periodicite_id", referencedColumnName="id")
     * })
     */	
    private $periodicite;
    
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToMany(targetEntity="Orange\MainBundle\Entity\Action", mappedBy="pas")
     */
    private $action;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="can_be_cylique", type="boolean", nullable=true)
     */
    private $canBeCyclique;
    
    
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
     * @param string $libelle
     * @return Pas
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;
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
     * Set valeur
     *
     * @param integer $valeur
     * @return Pas
     */
    public function setValeur($valeur)
    {
        $this->valeur = $valeur;
        return $this;
    }

    /**
     * Get valeur
     *
     * @return integer 
     */
    public function getValeur()
    {
        return $this->valeur;
    }

    /**
     * Set periodicite
     * @param integer $periodicite
     * @return Pas
     */
    public function setPeriodicite($periodicite)
    {
        $this->periodicite = $periodicite;

        return $this;
    }

    /**
     * Get periodicite
     *
     * @return integer 
     */
    public function getPeriodicite()
    {
        return $this->periodicite;
    }
    
    public function __toString()
    {	
    	return $this->libelle;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->action = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set canBeCyclique
     *
     * @param boolean $canBeCyclique
     *
     * @return Pas
     */
    public function setCanBeCyclique($canBeCyclique)
    {
        $this->canBeCyclique = $canBeCyclique;

        return $this;
    }

    /**
     * Get canBeCyclique
     *
     * @return boolean
     */
    public function getCanBeCyclique()
    {
        return $this->canBeCyclique;
    }

    /**
     * Add action
     *
     * @param \Orange\MainBundle\Entity\Action $action
     *
     * @return Pas
     */
    public function addAction(\Orange\MainBundle\Entity\Action $action)
    {
        $this->action[] = $action;

        return $this;
    }

    /**
     * Remove action
     *
     * @param \Orange\MainBundle\Entity\Action $action
     */
    public function removeAction(\Orange\MainBundle\Entity\Action $action)
    {
        $this->action->removeElement($action);
    }

    /**
     * Get action
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAction()
    {
        return $this->action;
    }
}
