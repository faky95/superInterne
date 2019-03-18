<?php
namespace Orange\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Orange\MainBundle\Entity\Action;



/**
 * Tache
 * @ORM\Table(name="tache")
 * @ORM\Entity(repositoryClass="Orange\MainBundle\Repository\TacheRepository")
 */
class Tache
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    
    /**
     * @var String
     * @ORM\Column(name="reference", type="string", length=25, nullable=false)
     */
    private $reference;
    
    /**
     * @ORM\ManyToOne(targetEntity="ActionCyclique", inversedBy="tache")
     * @ORM\JoinColumn(name="action_clique_id", referencedColumnName="id")
     **/
    private $actionCyclique;
    
    /**
     * @var \DateTime
     * @ORM\Column(name="date_debut", type="datetime", nullable=false)
     */
    private $dateDebut;
    
    /**
     * @var \DateTime
     * @ORM\Column(name="date_fin", type="date", nullable=false)
     */
    private $dateFin;

    /**
     * @var \DateTime
     * @ORM\Column(name="date_initial", type="date", nullable=false)
     */
    private $dateInitial;
    
    /**
     * @var \Date
     * @ORM\Column(name="date_fin_execution", type="date", nullable=true)
     */
    private $dateFinExecut;
    
    /**
     * @var \DateTime
     * @ORM\Column(name="date_cloture", type="datetime", nullable=true)
     */
    private $dateCloture;
    
    /**
     * @ORM\OneToMany(targetEntity="TacheStatut", mappedBy="tache", cascade={"persist","remove","merge"})
     */
    private $tacheStatut;
    
    /**
     * @ORM\OneToMany(targetEntity="Document", mappedBy="tache", cascade={"persist","remove","merge"})
     */
    private $document;
    
    /**
     * @var string
     * @ORM\Column(name="etat_courant", type="string", length=255, nullable=true)
     */
    private $etatCourant;

     /**
     * @var integer
     * @ORM\Column(name="numero_tache", type="integer", nullable=false)
     */
    private $numeroTache;
   
    /**
     * Constructor
     */
    public function __construct()
    {
    	$this->tacheStatut = new \Doctrine\Common\Collections\ArrayCollection();
    	$this->document = new \Doctrine\Common\Collections\ArrayCollection();
        $this->etatCourant = Statut::ACTION_NON_ECHUE;
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
	 * @return string
	 */
	public function getReference() {
		return $this->reference;
	}
	
	/**
	 * @param $reference
	 * @return Tache
	 */
	public function setReference($reference) {
		$this->reference = $reference;
		return $this;
	}
	
    /**
     * Set dateDebut
     * @param \DateTime $dateDebut
     * @return Tache
     */
    public function setDateDebut($dateDebut)
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    /**
     * Get dateDebut
     *
     * @return \DateTime 
     */
    public function getDateDebut()
    {
        return $this->dateDebut;
    }
    
    /**
     * Set dateFin
     * @param \DateTime $dateFin
     * @return Tache
     */
    public function setDateFin($dateFin)
    {
    	$this->dateFin = $dateFin;
    	return $this;
    }
    
    /**
     * get dateFin
     * @return \DateTime
     */
    public function getDateFin()
    {
    	return $this->dateFin;
    }

    /**
     * Set dateInitial
     * @param \DateTime $dateInitial
     * @return Tache
     */
    public function setDateInitial($dateInitial)
    {
        $this->dateInitial = $dateInitial;
        return $this;
    }

    /**
     * Get dateInitial
     * @return \DateTime 
     */
    public function getDateInitial()
    {
        return $this->dateInitial;
    }

    /**
     * Set dateCloture
     * @param \DateTime $dateCloture
     * @return Tache
     */
    public function setDateCloture($dateCloture)
    {
        $this->dateCloture = $dateCloture;
        return $this;
    }

    /**
     * Get dateCloture
     * @return \DateTime 
     */
    public function getDateCloture()
    {
        return $this->dateCloture;
    }
    
    /**
     * Set dateFinExecut
     * @param \DateTime $dateFinExecut
     * @return Tache
     */
    public function setDateFinExecut($dateFinExecut)
    {
    	$this->dateFinExecut = $dateFinExecut;
    	return $this;
    }
    
    /**
     * Get dateFinExecut
     * @return \DateTime
     */
    public function getDateFinExecut()
    {
    	return $this->dateFinExecut;
    }

    /**
     * Set actionCyclique
     *
     * @param \Orange\MainBundle\Entity\ActionCyclique $actionCyclique
     * @return Tache
     */
    public function setActionCyclique(\Orange\MainBundle\Entity\ActionCyclique $actionCyclique = null)
    {
        $this->actionCyclique = $actionCyclique;

        return $this;
    }

    /**
     * Get actionCyclique
     *
     * @return \Orange\MainBundle\Entity\ActionCyclique 
     */
    public function getActionCyclique()
    {
        return $this->actionCyclique;
    }

    /**
     * Add tacheStatut
     *
     * @param \Orange\MainBundle\Entity\TacheStatut $tacheStatut
     * @return Tache
     */
    public function addTacheStatut(\Orange\MainBundle\Entity\TacheStatut $tacheStatut)
    {
        $this->tacheStatut[] = $tacheStatut;

        return $this;
    }

    /**
     * Remove tacheStatut
     *
     * @param \Orange\MainBundle\Entity\TacheStatut $tacheStatut
     */
    public function removeTacheStatut(\Orange\MainBundle\Entity\TacheStatut $tacheStatut)
    {
        $this->tacheStatut->removeElement($tacheStatut);
    }
    
    /**
     * Get tacheStatut
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTacheStatut()
    {
    	return $this->tacheStatut;
    }
    
    /**
     * get document
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDocument()
    {
    	return $this->document;
    }

       /**
     * Get etatCourant
     *
     * @return string 
     */
    public function getEtatCourant()
    {
        return $this->etatCourant;
    }

    /**
     * Set etatCourant
     *
     * @param string $etatCourant
     * @return Tache
     */
    public function setEtatCourant($etatCourant)
    {
        $this->etatCourant = $etatCourant;
        return $this;
    }


    /**
     * Get the value of numeroTache
     *
     * @return  integer
     */ 
    public function getNumeroTache()
    {
        return $this->numeroTache;
    }

    /**
     * Set the value of numeroTache
     *
     * @param  integer  $numeroTache
     *
     * @return  self
     */ 
    public function setNumeroTache($numeroTache)
    {
        $this->numeroTache = $numeroTache;

        return $this;
    }
}
